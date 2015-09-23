<?php
require_once APP_DIR . DS . 'apiLib' . DS . 'ext' . DS . 'Upload.php';
$act=filter($_REQUEST['act']);
switch ($act){
	case 'addDiary':
		addDiary();//新增慢生活
		break;
	case 'getDiarys':
		getDiarys();//查看慢生活
		break;
	case 'delDiary':
		delDiary();//删除慢生活
		break;
	case 'delDiaryImg':
		delDiaryImg();//删除慢生活
		break;
        case 'leaveMsg':
                leaveMsg();//留言
                break;
        case 'delMsg':
                delMsg();//删除留言
                break;
	default:
		break;
}
//新增慢生活
function addDiary(){
        global $db;
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$note=filter(!empty($_REQUEST['note'])?$_REQUEST['note']:'');
	$voice=filter(!empty($_REQUEST['voice'])?$_REQUEST['voice']:'');
	$voice_time=filter(!empty($_REQUEST['voice_time'])?$_REQUEST['voice_time']:'');
	$address=filter(!empty($_REQUEST['address'])?$_REQUEST['address']:'');
	$lng=filter(!empty($_REQUEST['lng'])?$_REQUEST['lng']:'');
	$lat=filter(!empty($_REQUEST['lat'])?$_REQUEST['lat']:'');
        $data=array('user_id'=>$loginid,'note'=>$note,'voice'=>$voice,'voice_time'=>$voice_time,'address'=>$address,'lng'=>$lng,'lat'=>$lat);
        if(empty($loginid)){
            echo json_result(null,'2','请重新登录');
            return ;
        }
        //声音
        $upload = new UpLoad();
        $folder = "upload/voice/";
        if (!file_exists($folder)) {
                mkdir($folder, 0777);
        }
        $upload->setDir($folder . date("Ymd") . "/");
        $upload->setPrefixName('diary' . $loginid);
        $file = $upload->upLoad('voice');
        if ($file['status'] != 0 && $file['status'] != 1){
            echo json_result(null,'3',$file['errMsg']);
            return ;
        }elseif ($file['status'] == 1) {
            $data['voice']=APP_SITE . $file['file_path'];
        }
        $diary_id=$db->create('diary',$data);
        //相册
        $upload = new UpLoad();
        $folder = "upload/diaryPhoto/";
        if (!file_exists($folder)) {
                mkdir($folder, 0777);
        }
        $upload->setDir($folder . date("Ymd") . "/");
        $upload->setPrefixName('diary' . $loginid);
        $file = $upload->uploadFiles('photos');
        if ($file['status'] == 1) {
                foreach ($file['filepaths'] as $path) {
                        $photo['diary_id'] = $diary_id;
                        $photo['user_id'] = $loginid;
                        list($width,$height,$type)=getimagesize($path);
                        $photo['img'] = APP_SITE . $path;
                        $photo['width'] = $width;
                        $photo['height'] = $height;
                        $photo['created'] = date("Y-m-d H:i:s");
                        $db->create('diary_img', $photo);
                }
        }
        echo json_result(array('success'=>'TRUE'));
}

//查看慢生活
function getDiarys(){
        global $db;
	$userid=filter(!empty($_REQUEST['userid'])?$_REQUEST['userid']:'');
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$page_no = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
        if(empty($loginid)){
            echo json_result(null,'2','请重新登录');
            return ;
        }
        if(empty($userid)){
            echo json_result(null,'3','请选择你想查看的用户');
            return ;
        }
        $userallow=$db->getRow('user',array('id'=>$userid),array('allow_news'));//访问权限
        if($userid == $loginid){
            $type = 1;//自己
        }else{
            $relation = getRelationStatus($loginid, $userid);
            if($relation['relation_status']==4){
                $type = 2;//好友
            }else{
                $type = 3;//陌生人
            }
        }
        if($type==3&&$userallow['allow_news']==2){//陌生人且不许查看
            echo json_result(null,'3','用户限制陌生人查看,请先成为TA的好友吧');
            return ;
        }
        if($type==3&&$page_no>1){//陌生人且只能查看8条信息
            echo json_result(null,'4','想看更多内容的话,请先成为TA的好友吧');
            return ;
        }
        $sql="select id as diary_id,note,voice,voice_time,address,lng,lat,created from ".DB_PREFIX."diary diary where isdel=2 and user_id = $userid order by created desc ";//isdel 1删除2正常
	$sql .= " limit $start,$page_size";
	$diarys=$db->getAllBySql($sql);
        
        //获取相册留言等内容
        foreach ($diarys as $k=>$d){
            //相册
            $imgsql="select id as img_id,img,width,height from ".DB_PREFIX."diary_img as img where diary_id=".$d['diary_id'];
            $imgs=$db->getAllBySql($imgsql);
            if(count($imgs)>0){
                $diarys[$k]['imgs']=$imgs;
            }
            //留言
            $msgsql="select msg.id as msg_id,msg.user_id as from_user_id,from_user.nick_name as from_nick_name,from_user.head_photo as from_head_photo,msg.to_user_id,to_user.nick_name as to_nick_name,to_user.head_photo as to_head_photo,msg.msg from ".DB_PREFIX."diary_msg msg left join " .DB_PREFIX. "user from_user on from_user.id=msg.user_id left join ".DB_PREFIX."user to_user on to_user.id=msg.to_user_id "
                    . "where msg.diary_id = ".$d['diary_id'];
            $msgs=$db->getAllBySql($msgsql);
            if(count($msgs)>0){
                $diarys[$k]['msgs']=$msgs;
            }
        }
        
        echo json_result(array('diarys'=>$diarys));
    
}

//删除慢生活
function delDiary(){
        global $db;
	$diaryid=filter(!empty($_REQUEST['diaryid'])?$_REQUEST['diaryid']:'');
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
        if(empty($loginid)){
            echo json_result(null,'2','请重新登录');
            return ;
        }
        if($db->getCount('diary',array('id'=>$diaryid,'user_id'=>$loginid))<=0){
            echo json_result(null,'3','操作失败,您不能删除此内容');
            return;
        }else{
            $diary=$db->update('diary',array('isdel'=>1),array('id'=>$diaryid));
            echo json_result(array('success'=>'TRUE'));
        }
        
}


//删除相册
function delDiaryImg(){
        global $db;
	$imgid=filter(!empty($_REQUEST['imgid'])?$_REQUEST['imgid']:'');
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
        if(empty($loginid)){
            echo json_result(null,'2','请重新登录');
            return ;
        }
        $img=$db->getRow('diary_img',array('id'=>$imgid));
        $db->delete('diary_img',array('id'=>$imgid,'user_id'=>$loginid));
        //返回慢生活信息
        if(empty($img['diary_id'])){
            echo json_result(null,'2','图片已删除');
            return ;
        }
        $diary_id=$img['diary_id'];
        $sql="select id as diary_id,note,voice,voice_time,address,lng,lat,created from ".DB_PREFIX."diary diary where id=$diary_id ";//isdel 1删除2正常
	$diary=$db->getRowBySql($sql);
        
        //获取相册留言等内容
        //相册
        $imgsql="select id as img_id,img,width,height from ".DB_PREFIX."diary_img as img where diary_id=".$diary_id;
        $imgs=$db->getAllBySql($imgsql);
        if(count($imgs)>0){
            $diary['imgs']=$imgs;
        }
        //留言
        $msgsql="select msg.id as msg_id,msg.user_id as from_user_id,from_user.nick_name as from_nick_name,from_user.head_photo as from_head_photo,msg.to_user_id,to_user.nick_name as to_nick_name,to_user.head_photo as to_head_photo,msg.msg from ".DB_PREFIX."diary_msg msg left join " .DB_PREFIX. "user from_user on from_user.id=msg.user_id left join ".DB_PREFIX."user to_user on to_user.id=msg.to_user_id "
                . "where msg.diary_id = ".$diary_id;
        $msgs=$db->getAllBySql($msgsql);
        if(count($msgs)>0){
            $diary['msgs']=$msgs;
        }
        echo json_result(array('diary'=>$diary));
}

//留言
function leaveMsg(){
        global $db;
	$diaryid=filter(!empty($_REQUEST['diaryid'])?$_REQUEST['diaryid']:'');
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$to_userid=filter(!empty($_REQUEST['to_userid'])?$_REQUEST['to_userid']:'');
	$msg=filter(!empty($_REQUEST['msg'])?$_REQUEST['msg']:'');
        if(empty($loginid)){
            echo json_result(null,'2','请重新登录');
            return ;
        }
        if(empty($msg)){
            echo json_result(null,'3','请输入留言内容');
            return ;
        }
        $data=array('diary_id'=>$diaryid,'user_id'=>$loginid,'msg'=>$msg);
        if(!empty($to_userid)){
            $data['to_user_id']=$to_userid;
        }
        $db->create('diary_msg',$data);
        $msgsql="select msg.id as msg_id,msg.user_id as from_user_id,from_user.nick_name as from_nick_name,from_user.head_photo as from_head_photo,msg.to_user_id,to_user.nick_name as to_nick_name,to_user.head_photo as to_head_photo,msg.msg from ".DB_PREFIX."diary_msg msg left join " .DB_PREFIX. "user from_user on from_user.id=msg.user_id left join ".DB_PREFIX."user to_user on to_user.id=msg.to_user_id "
                    . "where msg.diary_id = ".$diaryid;
        $msges=$db->getAllBySql($msgsql);
        echo json_result(array('msges'=>$msges));
        
}

//删除留言
function delMsg(){
        global $db;
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$msgid=filter(!empty($_REQUEST['msgid'])?$_REQUEST['msgid']:'');
        if(empty($loginid)){
            echo json_result(null,'2','请重新登录');
            return ;
        }
        $msg=$db->getRow('diary_msg',array('id'=>$msgid));
        $diary_id=$msg['diary_id'];
        $db->delete('diary_msg',array('id'=>$msgid,'user_id'=>$loginid));
        $msgsql="select msg.id as msg_id,msg.user_id as from_user_id,from_user.nick_name as from_nick_name,from_user.head_photo as from_head_photo,msg.to_user_id,to_user.nick_name as to_nick_name,to_user.head_photo as to_head_photo,msg.msg from ".DB_PREFIX."diary_msg msg left join " .DB_PREFIX. "user from_user on from_user.id=msg.user_id left join ".DB_PREFIX."user to_user on to_user.id=msg.to_user_id "
                    . "where msg.diary_id = ".$diary_id;
        $msges=$db->getAllBySql($msgsql);
        echo json_result(array('msges'=>$msges));
        
        
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