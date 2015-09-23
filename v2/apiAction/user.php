<?php
require_once APP_DIR.DS.'apiLib'.DS.'ext'.DS.'Upload.php';
require_once APP_DIR.DS.'apiLib'.DS.'ext'.DS.'Huanxin.php';
require_once APP_DIR.DS.'apiLib'.DS.'ext'.DS.'Sms.php';
require_once APP_DIR.DS.'apiLib'.DS.'ext'.DS.'Qrcode.php';
$act=filter($_REQUEST['act']);
switch ($act){
	case 'getVerificationCode':
		getVerificationCode();//获取验证码
		break;
	case 'resetPassword':
		resetPassword();//重置密码
		break;
	case 'register':
		register();//注册
		break;
        case 'registerComplete':
                registerComplete();//完成注册
                break;
	case 'login':
		login();//登录
		break;
	case 'info':
		info();//个人信息
		break;
	case 'infoEdit':
		infoEdit();//个人信息修改
		break;
	case 'infoImgs'://相册
		infoImgs();
		break;
	case 'uploadImg'://上传相册图片
		uploadImg();
		break;
	case 'deleteImg'://删除图片
		deleteImg();
		break;
	case 'uploadHeadImg'://上传头像
		uploadHeadImg();
		break;
        case 'updateUserTag'://更新用户标签
                updateUserTag();
                break;
        case 'infoTags'://用户标签
                infoTags();
                break;
	case 'allowNews':
		allowNews();//允许新消息
		break;
        case 'qrcode':
                qrcode();//二维码名片
		break;
	default:
		break;
}

//验证码
function getVerificationCode(){
	global $db;
	$data=filter($_REQUEST);
	$mobile=$data['mobile'];
	$type=empty($data['type'])?'':$data['type'];//1注册验证码2找回密码验证码
	if($type!=2){
		if($db->getCountBySql("select id from ".DB_PREFIX."user where mobile='$mobile' and uuid is not null and uuid <> '' ")>0){
			echo json_result(null,'7','此手机号已经注册过');
			return;
		}
	}
	if(trim($mobile)==''){
		echo json_result(null,'5','请填写手机号码');//请填写手机号码
		return;
	}
	if(trim($mobile)!=''&&!checkMobile($mobile)){
		echo json_result(null,'6','手机号码不正确');//手机号码不正确
		return;
	}
	//生成验证码
	$code=rand(100000, 999999);
	if(!empty($mobile)&&$db->getCount("user",array('mobile'=>$mobile))>0){
		$db->update('user',array('captcha_code'=>$code),array('mobile'=>$mobile));
	}else{
		$user=array('mobile'=>$mobile,'captcha_code'=>$code);
		$db->create('user', $user);
	}
	$sms=new Sms();
	$sms->sendMsg("您本次验证码是:".$code, $mobile);
	echo json_result('success');//array('code'=>$code)
	
}
//重置密码
function resetPassword(){
	global $db;
	$data=filter($_REQUEST);
	$mobile=!empty($data['mobile'])?$data['mobile']:'';
	$code=!empty($data['code'])?$data['code']:'';
	$user_pass=!empty($data['user_password'])?$data['user_password']:'';
	if(trim($mobile)==''){
		echo json_result(null,'5','请填写手机号码');//请填写手机号码
		return;
	}
	if(trim($mobile)!=''&&!checkMobile($mobile)){
		echo json_result(null,'6','手机号码不正确');//手机号码不正确
		return;
	}
	
	if(trim($user_pass)==''){
		echo json_result(null,'7','请填写密码');//请填写密码
		return;
	}
	
	if($db->getCount("user",array('mobile'=>$mobile,'captcha_code'=>$code))<=0){
		echo json_result(null,'8','验证码不正确');
		return;
	}
	$userInfo=$db->getRow('user',array('mobile'=>$mobile));
	if(!empty($userInfo['uuid'])){
		$pass=array('user_password'=>md5($user_pass));
		$HuanxinObj=Huanxin::getInstance();
		$huserObj=$HuanxinObj->updatePass(strtolower($mobile), md5($user_pass));
		if($huserObj->duration){
			$flag=$db->update('user', $pass ,array('mobile'=>$mobile,'captcha_code'=>$code));
		}else{
			echo json_result(null,'101','密码修改失败,请联系客服');
			return;
		}
	}else{
		$user=array('user_password'=>md5($user_pass),'mobile'=>$mobile,'sex'=>'3','age'=>'','constellation'=>'保密','created'=>date("Y-m-d H:i:s"));
		$HuanxinObj=Huanxin::getInstance();
		$huserObj=$HuanxinObj->addNewAppUser(strtolower($mobile), md5($user_pass));
		$uuid=$huserObj->entities[0]->uuid;
		if(empty($uuid)){
			echo json_result(null,'101','密码修改失败,请联系客服');
			return;
		}
		$user['uuid']=$uuid;
		$flag=$db->update('user', $user ,array('mobile'=>$mobile,'captcha_code'=>$code));
	}
	echo json_result('success');//成功
}

//注册
function register(){
	global $db;
	$data=filter($_REQUEST);
	$mobile=!empty($data['mobile'])?$data['mobile']:'';
	$code=!empty($data['code'])?$data['code']:'';
	$user_pass=!empty($data['user_password'])?$data['user_password']:'';
	if(trim($mobile)==''){
		echo json_result(null,'5','请填写手机号码');//请填写手机号码
		return;
	}
	if(trim($mobile)!=''&&!checkMobile($mobile)){
		echo json_result(null,'6','手机号码不正确');//手机号码不正确
		return;
	}
	
	if(trim($user_pass)==''){
		echo json_result(null,'7','请填写密码');//请填写密码
		return;
	}

	if($db->getCount("user",array('mobile'=>$mobile,'captcha_code'=>$code))<=0){
		echo json_result(null,'8','验证码不正确');
		return;
	}
	
	if($db->getCountBySql("select id from ".DB_PREFIX."user where mobile='$mobile' and uuid is not null and uuid <> '' ")>0){
		echo json_result(null,'9','此手机号已经注册过');
		return;
	}
	$user=array('user_name'=>$mobile,'user_password'=>md5($user_pass),'mobile'=>$mobile,'sex'=>'3','age'=>'','constellation'=>'保密','created'=>date("Y-m-d H:i:s"));
	$HuanxinObj=Huanxin::getInstance();
	$huserObj=$HuanxinObj->addNewAppUser(strtolower($mobile), md5($user_pass));
	$uuid=$huserObj->entities[0]->uuid;
	if(empty($uuid)){
		echo json_result(null,'101','注册失败,请联系客服');
		return;
	}
	$user['uuid']=$uuid;
	$flag=$db->update('user', $user ,array('mobile'=>$mobile,'captcha_code'=>$code));
	if($flag){
                $userobj=$db->getRow('user',array('user_name'=>$mobile,'user_password'=>md5($user_pass)));
		echo json_result(array('user_id'=>$userobj['id']));//成功
	}else{
		echo json_result(null,'101','注册失败,请联系客服');//失败
	}
}

//完成注册
function registerComplete(){
        global $db;
        $user_name=filter($_REQUEST['user_name']);
        $user_password=filter($_REQUEST['user_password']);
        $nickname=filter($_REQUEST['nick_name']);
        $sex=filter($_REQUEST['sex']);//1男2女
	if($db->getCount('user',array('user_name'=>$user_name))>0){
		$user=$db->getRow('user',array('user_name'=>$user_name));
	}else{
		echo json_result(null,'2','帐号不正确');
		return;
	}
	if($user['user_password']!=md5($user_password)){
		echo json_result(null,'3','密码不正确');
		return;
	}
        if(empty($nickname)){
                echo json_result(null,'4','没有昵称怎能让别人记住你');
		return;
        }
        $info=array('nick_name'=>$nickname,'sex'=>$sex);
	//上传相册图片
	$upload=new UpLoad();
	$folder="upload/userPhoto/";
	if (! file_exists ( $folder )) {
		mkdir ( $folder, 0777 );
	}
	$upload->setDir($folder.date("Ymd")."/");
	$upload->setPrefixName('user'.$user['id']);
	$upload->setSHeight(260);
	$upload->setSWidth(260);
	$upload->setLHeight(640);
	$upload->setLWidth(640);
	$file=$upload->upLoadImg('head_photo');//$_File['photo'.$i]
	if($file['status']!=0&&$file['status']!=1){
		echo json_result(null,'37',$file['errMsg']);
		return;
	}
	if($file['status']==1){
		$info['head_photo']=APP_SITE.$file['s_path'];
	}
		
	$db->update('user', $info , array('id'=>$user['id']));
        $info['userid']=$user['id'];
        $info['user_name']=$user['user_name'];
	$info['pinyin']=!empty($info['nick_name'])?getFirstCharter($info['nick_name']):'';
        $info['mobile']=$user['mobile'];
	
        echo json_result($info);
}

//登录
function login(){
	global $db;
	$data=filter($_REQUEST);
	$user_name=$data['user_name'];
	$user_pass=$data['user_password'];
	if($db->getCount('user',array('user_name'=>$user_name))>0){
		$user=$db->getRow('user',array('user_name'=>$user_name));
	}elseif($db->getCount('user',array('mobile'=>$user_name))>0){
		$user=$db->getRow('user',array('mobile'=>$user_name));
	}elseif($db->getCount('user',array('email'=>$user_name))>0){
		$user=$db->getRow('user',array('email'=>$user_name));
	}else{
		echo json_result(null,'11','帐号不正确');
		return;
	}
	if($user['user_password']!=md5($user_pass)){
		echo json_result(null,'12','密码不正确');
		return;
	}else{
		$info=array();
		$info['userid']=$user['id'];
		$info['user_name']=$user['user_name'];
		$info['nick_name']=$user['nick_name'];
		$info['sex']=$user['sex'];
		$info['mobile']=$user['mobile'];
		$info['head_photo']=$user['head_photo'];
		$info['allow_find']=$user['allow_find'];
		$info['allow_flow']=$user['allow_flow'];
		if(!empty($info['head_photo_id'])){
			$head=$db->getRow('user_photo',array('id'=>$info['head_photo_id']));
			$info['head_photo']=$head['path'];
		}
		echo json_result($info);
	}
}

//个人信息
function info(){
	global $db;
	$data=filter($_REQUEST);
	$user_id=$data['userid'];
	$loginid=$data['loginid'];//登陆者id
	$info=$db->getRow('user',array('id'=>$user_id),array('id','head_photo','sex','birthday','user_name','nick_name','height','emotion','frequented','weight','blood','hometown','home_province_id','home_city_id','home_town_id','profession','salary','career','hobby','personality'));
	//查询人物关系 当loginid不为空的时候
	if(!empty($loginid)){
		//好友关系
		$relation_status=getRelationStatus($loginid,$user_id);
		$info['relation']=$relation_status['relation'];//陌生人
		$info['relation_status']=$relation_status['relation_status'];
		//备注
		$relation=$db->getRow('user_relation',array('user_id'=>$loginid,'relation_id'=>$user_id),array('relation_name'));
		if(!empty($relation['relation_name'])){
			$info['nick_name']=$relation['relation_name'];
		}
		$me=$db->getRow('user',array('id'=>$loginid),array('lat','lng'));
                
		$info['distance']=(!empty($me['lat'])&&!empty($me['lng'])&&!empty($info['lat'])&&!empty($info['lng']))?getDistance($info['lat'],$info['lng'],$me['lat'],$me['lng']):lang_UNlOCATE;
		//$info['lasttime']=time2Units(time()-strtotime($info['logintime']));
		//$info['address']=($info['allow_find']==1)&&!empty($info['lat'])&&!empty($info['lng'])?getAddressFromBaidu($info['lng'],$info['lat']):"未获取到位置";
	}
//	if(empty($loginid)){
//		$info['address']=!empty($info['lat'])&&!empty($info['lng'])?getAddressFromBaidu($info['lng'],$info['lat']):"未获取到位置";
//	}
        
        $info['age']=floor((time()-strtotime($data['birthday'])) / 60 / 60 / 24 / 365);
        $info['constellation']=  get_zodiac_sign(date("n",strtotime($data['birthday'])), date("j",strtotime($data['birthday'])));

	$info['user_photos']=$db->getAll('user_photo',array('user_id'=>$user_id,'isdelete'=>0),array('id','path'));
	if(is_array($info)){
		echo json_result($info);
	}else{
		echo json_result(null,'11','信息获取失败');
	}
}

//个人信息修改
function infoEdit(){
	global $db;
	$data=filterIlegalWord($_REQUEST);
	$loginid=$data['loginid'];
	if(empty($loginid)){
		echo json_result(null,'13','获取不到当前用户id');
		return;
	}
	if($db->getCount('user',array('id'=>$loginid))<=0){
		echo json_result(null,'14','找不到当前用户id,请重新登录');
		return;
	}
	$info=array();
        
	if(!empty($data['birthday'])){
                $info['birthday']=$data['birthday'];
                $info['age']=floor((time()-strtotime($data['birthday'])) / 60 / 60 / 24 / 365);
		$info['constellation']=  get_zodiac_sign(date("n",strtotime($data['birthday'])), date("j",strtotime($data['birthday'])));
	}
	if(!empty($data['nick_name'])){
		$info['nick_name']=$data['nick_name'];
		$info['pinyin']=!empty($info['nick_name'])?getFirstCharter($info['nick_name']):'';
	}
	if(!empty($data['height'])){
		$info['height']=$data['height'];
	}
	if(!empty($data['emotion'])){
		$info['emotion']=$data['emotion'];
	}
	if(!empty($data['frequented'])){
		$info['frequented']=$data['frequented'];
	}
	if(!empty($data['weight'])){
		$info['weight']=$data['weight'];
	}
	if(!empty($data['blood'])){
		$info['blood']=$data['blood'];
	}
	if(!empty($data['home_province_id'])){
		$info['home_province_id']=$data['home_province_id'];
                $province=$db->getRow('address_province',array('id'=>$info['home_province_id']));
	}
	if(!empty($data['home_city_id'])){
		$info['home_city_id']=$data['home_city_id'];
                $city=$db->getRow('address_city',array('id'=>$info['home_city_id']));
	}
	if(!empty($data['home_town_id'])){
		$info['home_town_id']=$data['home_town_id'];
                $town=$db->getRow('address_town',array('id'=>$info['home_town_id']));
	}
        if(!empty($data['home_province_id'])||!empty($data['home_city_id'])||!empty($data['home_town_id'])){
                if($province['name']==$city['name']){
                        $hometown=$city['name'].$town['name'];
                }else{
                        $hometown=$province['name'].$city['name'].$town['name'];
                }
                $info['hometown'] =  str_replace('-', '', $hometown);
        }
	if(!empty($data['profession'])){//行业
		$info['profession']=$data['profession'];
	}
	if(!empty($data['salary'])){
		$info['salary']=$data['salary'];
	}
	if(!empty($data['career'])){
		$info['career']=$data['career'];
	}
	if(!empty($data['hobby'])){
		$info['hobby']=$data['hobby'];
	}
	if(!empty($data['personality'])){//性格特点
		$info['personality']=$data['personality'];
	}
	//经纬度
//	$loc_json=file_get_contents("http://api.map.baidu.com/geocoder/v2/?address=".$data['address']."&output=json&ak=".BAIDU_AK);
//	$loc=json_decode($loc_json);
//	if($loc->status==0){
//		$info['ad_lng']=$loc->result->location->lng;
//		$info['ad_lat']=$loc->result->location->lat;
//	}
	$db->update('user', $info,array('id'=>$loginid));
        $info=$db->getRow('user',array('id'=>$loginid),array('id','head_photo','sex','age','constellation','user_name','nick_name','height','emotion','frequented','weight','blood','hometown','home_province_id','home_city_id','home_town_id','profession','salary','career','hobby','personality'));
	echo json_result($info);
}

//相册
function infoImgs(){
	global $db;
	$data=filter($_REQUEST);
	$user_id=$data['loginid'];
	if(empty($user_id)){
		echo json_result(null,'14','获取不到当前用户id');
		return;
	}
	$user_photo=$db->getAll('user_photo',array('user_id'=>$user_id,'isdelete'=>0));
	$info['photos']=$user_photo;
	echo json_result($info);
	
}

//上传相册图片
function uploadImg(){
	global $db;
	$user_id=filter($_REQUEST['loginid']);
	if(empty($user_id)){
		echo json_result(null,'14','获取不到当前用户id');
		return;
	}
	//上传相册图片
	$upload=new UpLoad();
	$folder="upload/userPhoto/";
	if (! file_exists ( $folder )) {
		mkdir ( $folder, 0777 );
	}
	$upload->setDir($folder.date("Ymd")."/");
	$upload->setPrefixName('user'.$user_id);
	$file=$upload->upLoad('photo');//$_File['photo'.$i]
	if($file['status']!=0&&$file['status']!=1){
		echo json_result(null,'37',$file['errMsg']);
		return;
	}
	if($file['status']==1){
                $photo['path']=APP_SITE.$file['file_path'];
                $photo['user_id']=$user_id;
                $photo['created']=date("Y-m-d H:i:s");
                $photo['id']=$db->create('user_photo', $photo);
	}
        $info['pid']=$photo['id'];
	$info['user_photo']=$photo['path'];
	echo json_result($info);
}

//删除图片
function deleteImg(){
	global $db;
	$user_id=filter($_REQUEST['loginid']);
	if(empty($user_id)){
		echo json_result(null,'14','获取不到当前用户id');
		return;
	}
	$pids=filter($_REQUEST['pid']);
	$pids=explode(",", $pids);
	foreach ($pids as $pid){
		$photo=$db->getRow('user_photo',array('id'=>$pid,'user_id'=>$user_id));
		if(!is_array($photo)){
			echo json_result(null,'38','图片已删除');
			return;
		}
		$path=str_replace(APP_SITE, "", $photo['path']);
		unlink($path);
		$path=str_replace("_s", "_b", $path);
		unlink($path);
		$db->delete('user_photo', array('id'=>$pid,'user_id'=>$user_id));
	}
	echo json_result(array('success'=>'TRUE'));
}

//上传头像
function uploadHeadImg(){
	global $db;
	$user_id=filter($_REQUEST['loginid']);
	if(empty($user_id)){
		echo json_result(null,'14','获取不到当前用户id');
		return;
	}
	//上传相册图片
        $info=array();
        $upload=new UpLoad();
	$folder="upload/userPhoto/";
	if (! file_exists ( $folder )) {
		mkdir ( $folder, 0777 );
	}
	$upload->setDir($folder.date("Ymd")."/");
	$upload->setPrefixName('user'.$user['id']);
	$upload->setSHeight(260);
	$upload->setSWidth(260);
	$upload->setLHeight(640);
	$upload->setLWidth(640);
	$file=$upload->upLoadImg('photo');//$_File['photo'.$i]
	if($file['status']!=0&&$file['status']!=1){
		echo json_result(null,'37',$file['errMsg']);
		return;
	}
	if($file['status']==1){
                $head_old=$db->getRow('user',array('id'=>$user_id),array('head_photo'));
                if(!empty($head_old['head_photo'])){
                        $path=str_replace(APP_SITE, "", $head_old['head_photo']);
                        unlink($path);
                        $path=str_replace("_s", "_b", $path);
                        unlink($path);
                }
		$info['head_photo']=APP_SITE.$file['s_path'];
	}else{
            echo json_result(null,'3',$file['errMsg']);
            return ;
        }
        $db->update('user', $info,array('id'=>$user_id));
	echo json_result($info);
}

//更新标签
function updateUserTag(){
        global $db;
	$loginid=filter($_REQUEST['loginid']);
	$tag_ids=filter($_REQUEST['tag_ids']);
        if(!empty($tag_ids)){
                $tag_ids=  explode(',', $tag_ids);
                $db->delete('user_tag',array('user_id'=>$loginid));
                foreach ($tag_ids as $t){
                        $db->create('user_tag',array('user_id'=>$loginid,'tag_id'=>$t));
                }
        }
	echo json_result(array('success'=>'TRUE'));
        
}

//查看用户标签
function infoTags(){
        global $db;
	$userid=filter($_REQUEST['userid']);
        $sql="select base_tag.name from ".DB_PREFIX."user_tag tag left join ".DB_PREFIX."base_user_tag base_tag on tag.tag_id=base_tag.id where tag.user_id=$userid ";
        $tags=$db->getAllBySql($sql);
        echo json_result(array('tags'=>$tags));
}

//允许新消息
function allowNews(){
	global $db;
	$data=filter($_REQUEST);
	$user_id=$data['userid'];
	$allow=$data['allow'];//1允许2不允许
	if (empty($user_id)){
		echo json_result(null,'2','请先登录');
		return;
	}
	$db->update('user',array('allow_news'=>$allow),array('id'=>$user_id));
	echo json_result(array('userid'=>$user_id));
}

//二维码名片
function qrcode(){
	$loginid=filter($_REQUEST['loginid']);
        if(empty($loginid)){
		echo json_result(null,'2','请先登录');
		return;
        }
        $qrfile="upload/qrcode/user/user{$loginid}.png";
        $str=WEB_SITE.'userqrcode.html?u='. base64_encode($loginid);//带用户id的下载页面
        if(!file_exists($qrfile)){
            QRcode::png($str,$qrfile,0,10,1);//生成二维码
        }
        echo json_result(array('qrcode'=>APP_SITE.$qrfile));
}


function getRelationStatus($myself_id,$user_id){
	global $db;
	$info=array();
	//我关注的
	$myfav_count=$db->getCount('user_relation',array('user_id'=>$myself_id,'relation_id'=>$user_id));
	//关注我的
	$myfun_count=$db->getCount('user_relation',array('user_id'=>$user_id,'relation_id'=>$myself_id));
	if($myfav_count>0&&$myfun_count>0){
		$info['relation']='好友';
		$info['relation_status']=4;
	}elseif ($myfav_count>0){
		$info['relation']='关注中';//我关注的人
		$info['relation_status']=2;
	}elseif ($myfun_count>0){
		$info['relation']='被关注';//关注我的人
		$info['relation_status']=3;
	}
	if ($myfun_count>0){
		$re=$db->getRow('user_relation',array('user_id'=>$user_id,'relation_id'=>$myself_id));
		if($re['status']==2){
			$info['relation']='陌生人';//对方黑名单中
			$info['relation_status']=6;
		}
	}
	if ($myfav_count>0){
		$re=$db->getRow('user_relation',array('user_id'=>$myself_id,'relation_id'=>$user_id));
		if($re['status']==2){
			$info['relation']='黑名单';//黑名单
			$info['relation_status']=5;
		}
	}
	if($myfav_count<=0&&$myfun_count<=0){
		$info['relation']='陌生人';//陌生人
		$info['relation_status']=1;
	}
	return $info;
}
