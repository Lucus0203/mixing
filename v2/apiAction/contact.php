<?php
require_once APP_DIR.DS.'apiLib'.DS.'ext'.DS.'Huanxin.php';
$act=filter($_REQUEST['act']);
switch ($act){
	case 'getFriends':
		getFriends();//好友/所有联系人(互相关注)
		break;
	case 'searchUsersByKeyword'://根据关键字查找用户
		searchUsersByKeyword();
		break;
	case 'follows':
		follows();//我关注的
		break;
	case 'fans':
		fans();//关注我的
		break;
        case 'myNewFansCount':
                myNewFansCount();//新关注我的人数
                break;
	case 'addFollow'://关注
		addFollow();
		break;
	case 'delFollow'://不再关注
		delFollow();
		break;
	case 'delFan'://移除粉丝
		delFan();
		break;
	case 'black'://拉黑
		black();
		break;
	case 'blackList'://拉黑
		blackList();
		break;
	case 'delBlack';//移除黑名单
		delBlack();
		break;
	case 'relationName'://备注
		relationName();
		break;
	case 'report'://举报
		report();
		break;
	case 'huanxinFriends'://环信好友
		huanxinFriends();
		break;
	case 'huanxinBlocks'://环信黑名单
		huanxinBlocks();
		break;
	default:
		break;
}

function getFriends(){//好友/所有联系人(互相关注)
	global $db;
	$userid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	if(empty($userid)){
		echo json_result(null,'2','请重新登录');
		return;
	}
	$data=array();
	$sql="select u.id as user_id,if((trim(ur1.relation_name)<>'' and ur1.relation_name is not null),ur1.relation_name,u.nick_name) as nick_name,u.user_name,u.head_photo from ".DB_PREFIX."user u left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id
			left join ".DB_PREFIX."user_relation ur2 on ur1.relation_id = ur2.user_id 
			where ur2.relation_id = $userid and ur1.user_id = $userid and ur1.status=1 and ur2.status=1 ";
	
	$z='A';
	for($i=1;$i<=26;$i++){
		$s=$sql." and if ( (ur1.relation_name!='' and ur1.relation_name is not null),if(ur1.relation_pinyin='{$z}',1,0),if(u.pinyin='{$z}',1,0) )  ORDER BY convert(nick_name using gbk); ";
		$friends=$db->getAllBySql($s);
                if(!empty($friends)){
                    $data[$z]=$friends;
                }
                $z++;
	}
	
	//26字母以外的
	$s=$sql." and if ( (ur1.relation_name!='' and ur1.relation_name is not null),if(ur1.relation_pinyin='' or ur1.relation_pinyin is null,1,0),if(u.pinyin='' or u.pinyin is null,1,0) )  ORDER BY convert(nick_name using gbk); ";
        $others=$db->getAllBySql($s);
        if(!empty($others)){
            $data['#']=$others;
        }
	echo json_result($data);
	
}

//根据咖啡号手机号名称查找
function searchUsersByKeyword(){
	global $db;
	$keyword=filter(!empty($_REQUEST['keyword'])?$_REQUEST['keyword']:'');
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$page_no = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	if(empty($loginid)){
		echo json_result(null,'2','请重新登录');
		return;
	}
	if(empty($keyword)){
		echo json_result(null,'3','请输入想要查询的内容');
		return;
	}
        //as isfollowed 1未关注2已关注
	$sql="select u.id as user_id,u.head_photo,if((trim(ur1.relation_name)<>'' and ur1.relation_name is not null),ur1.relation_name,u.nick_name) as nick_name,u.user_name,u.birthday,u.sex,u.constellation,if(ur1.id is null,1,2) as isfollowed from ".DB_PREFIX."user u 
	left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id and ur1.user_id=$loginid
        where user_name ='$keyword' or nick_name = '$keyword' or ur1.relation_name='$keyword' ";
	$sql .= " limit $start,$page_size";
	$data = $db->getAllBySql($sql);
        foreach ($data as $k => $v) {
                $data[$k]['age']='';
                if(!empty($v['birthday'])){
                        $data[$k]['age'] = floor((time()-strtotime($v['birthday'])) / 60 / 60 / 24 / 365);
                }
        }
	$res['users']=$data;
	echo json_result($res);
		
}

function follows(){//我关注的
	global $db;
	$userid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$page_no = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	
	if(empty($userid)){
		echo json_result(null,'2','请重新登录');
		return;
	}
//	//好友
//	$friendsSql="select ur1.relation_id from ".DB_PREFIX."user_relation ur1 
//			left join ".DB_PREFIX."user_relation ur2 on ur1.relation_id = ur2.user_id
//			where ur2.relation_id = $userid and ur1.user_id = $userid and ur1.status=1 ";
//	//我关注的人
//	$mysql="select u.id as user_id,u.nick_name,u.user_name,u.head_photo from ".DB_PREFIX."user u left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id where ur1.user_id = $userid and ur1.status=1 order by ur1.id desc ";
//	
//	//排除好友
//	$sql="select * from ($mysql) m where not exists ( select * from ($friendsSql) f where f.relation_id = m.user_id ) ";
//	
        //isfriend 1互粉2单向关注
	$sql="select u.id as user_id,u.nick_name,u.user_name,u.head_photo,if(ur2.id !='',4,2) relation_status from ".DB_PREFIX."user u "
                . "left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id "
                . "left join ".DB_PREFIX."user_relation ur2 on u.id=ur2.user_id and ur2.relation_id = $userid "
                . "where ur1.user_id = $userid and ur1.status=1 order by ur1.id desc ";
	$sql .= " limit $start,$page_size";
	$data=$db->getAllBySql($sql);
	$res['users']=$data;
	
	echo json_result($res);

}

function fans(){//关注我的
	global $db;
	$userid=filter($_REQUEST['loginid']);
	$page_no = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	
	if(empty($userid)){
		echo json_result(null,'2','请重新登录');
		return;
	}
	
	//好友
//	$friendsSql="select ur1.relation_id from ".DB_PREFIX."user_relation ur1
//			left join ".DB_PREFIX."user_relation ur2 on ur1.relation_id = ur2.user_id
//				where ur2.relation_id = $userid and ur1.user_id = $userid and ur1.status=1 ";
//	//关注我的人
//	$mysql="select u.id as user_id,u.nick_name,u.user_name,u.head_photo from ".DB_PREFIX."user u left join ".DB_PREFIX."user_relation ur2 on u.id=ur2.user_id where ur2.relation_id = $userid and ur2.status=1 order by ur2.id desc ";
//	//排除好友
//	$sql="select * from ($mysql) m where not exists ( select * from ($friendsSql) f where f.relation_id = m.user_id ) ";
//	
        //isfriend 1互粉2单向关注
        $sql="select u.id as user_id,u.nick_name,u.user_name,u.head_photo,if(ur1.id !='',4,3) relation_status from ".DB_PREFIX."user u "
                . "left join ".DB_PREFIX."user_relation ur2 on u.id=ur2.user_id "
                . "left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id and ur1.user_id=$userid "
                . "where ur2.relation_id = $userid and ur2.status=1 order by ur2.id desc ";
	$sql .= " limit $start,$page_size";
	$data=$db->getAllBySql($sql);
	$db->update('user_relation', array('ischeck'=>1),array('relation_id'=>$userid));
	$res['users']=$data;
	echo json_result($res);
	
}

function myNewFansCount(){//新关注我的人数
	global $db;
	$userid=filter($_REQUEST['loginid']);
	if(empty($userid)){
		echo json_result(null,'2','请重新登录');
		return;
	}
	$count=$db->getCount('user_relation',array('relation_id'=>$userid,'ischeck'=>'0'));
	echo json_result(array('count'=>$count));
}

function addFollow(){//关注
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$userid=filter($_REQUEST['userid']);
	if(empty($loginid)){
		echo json_result(null,'2','请重新登录');
		return;
	}
	//好友关系
	$rinfo=array('user_id'=>$loginid,'relation_id'=>$userid);
	$relation=$db->getRow('user_relation',array('user_id'=>$loginid,'relation_id'=>$userid));
	if(!is_array($relation)||count($relation)==0){//没关注
		$nickname=$db->getRow('user',array('id'=>$userid),array('nick_name','pinyin'));
		$rinfo['created']=date("Y-m-d H:i:s");
		$rinfo['relation_name']=$nickname['nick_name'];
		$rinfo['relation_pinyin']=$nickname['pinyin'];
		$db->create('user_relation', $rinfo);//关注
	}elseif ($relation['status']==2){//拉黑者
		$relation['status']=1;
		unset($relation['updated']);
		$db->update('user_relation', $relation,$rinfo);//重新关注
	}
	$res=getRelationStatus($loginid, $userid);
	echo json_result($res);
}

//移除关注
function delFollow(){
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$userid=filter($_REQUEST['userid']);
	if(empty($loginid)){
		echo json_result(null,2,'请重新登录');
		return;
	}
	$db->delete('user_relation', array('user_id'=>$loginid,'relation_id'=>$userid));
	$res=getRelationStatus($loginid, $userid);
	echo json_result($res);
}

//移除粉丝
function delFan(){
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$userid=filter($_REQUEST['userid']);
	if(empty($loginid)){
		echo json_result(null,2,'请重新登录');
		return;
	}
	if(empty($userid)){
		echo json_result(null,3,'对方不是您的粉丝');
		return;
	}
	$db->delete('user_relation', array('user_id'=>$userid,'relation_id'=>$loginid));
	$res=getRelationStatus($loginid, $userid);
	echo json_result($res);
}

function black(){//拉黑
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$userid=filter($_REQUEST['userid']);
	if(empty($loginid)){
		echo json_result(null,2,'请重新登录');
		return;
	}
	//好友关系
	$rinfo=array('user_id'=>$loginid,'relation_id'=>$userid);
	$relation=$db->getRow('user_relation',array('user_id'=>$loginid,'relation_id'=>$userid));
	if(!is_array($relation)||count($relation)==0){//没关注
		$nickname=$db->getRow('user',array('id'=>$userid),array('nick_name','pinyin'));
		$rinfo['status']=2;
		$rinfo['created']=date("Y-m-d H:i:s");
		$rinfo['relation_name']=$nickname['nick_name'];
		$rinfo['relation_pinyin']=$nickname['pinyin'];
		$db->create('user_relation', $rinfo);//关注
	}else{//拉黑者
		$relation['status']=2;
		unset($relation['updated']);
		$db->update('user_relation', $relation,$rinfo);//重新关注
	}
	
	$login=$db->getRow('user',array('id'=>$loginid),array('mobile'));
	$user=$db->getRow('user',array('id'=>$userid),array('mobile'));
	//环信拉黑
	$HuanxinObj=Huanxin::getInstance();
	$huserObj=$HuanxinObj->block($login['mobile'], $user['mobile']);
	
	$db->update('user_relation',array('status'=>2),array('user_id'=>$loginid,'relation_id'=>$userid));
	$res=getRelationStatus($loginid, $userid);
	echo json_result($res);
}

function blackList(){//黑名单
        global $db;
	$loginid=filter($_REQUEST['loginid']);
	$page_no = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	if(empty($loginid)){
		echo json_result(null,'2','请重新登录');
		return;
	}
        $sql="select u.id as user_id,u.nick_name,u.user_name,u.head_photo from ".DB_PREFIX."user u "
                . "left join ".DB_PREFIX."user_relation ur1 on u.id=ur1.relation_id "
                . "where ur1.user_id = $loginid and ur1.status=2 order by ur1.id desc ";
	$sql .= " limit $start,$page_size";
	$data=$db->getAllBySql($sql);
	$res['users']=$data;
	echo json_result($res);
}



function delBlack(){//移除黑名单
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$userid=filter($_REQUEST['userid']);
	if(empty($loginid)){
		echo json_result(null,2,'请重新登录');
		return;
	}
	//好友关系
	$rinfo=array('user_id'=>$loginid,'relation_id'=>$userid);
	$relation=$db->getRow('user_relation',array('user_id'=>$loginid,'relation_id'=>$userid));
	if(!is_array($relation)||count($relation)==0){//没关注
		$rinfo['status']=1;
		$rinfo['created']=date("Y-m-d H:i:s");
		$db->create('user_relation', $rinfo);//关注
	}else{//已关注
		$relation['status']=1;
		unset($relation['updated']);
		$db->update('user_relation', $relation,$rinfo);//重新关注
	}
	

	$login=$db->getRow('user',array('id'=>$loginid),array('mobile'));
	$user=$db->getRow('user',array('id'=>$userid),array('mobile'));
	//环信移除黑名单
	$HuanxinObj=Huanxin::getInstance();
	$huserObj=$HuanxinObj->unblock($login['mobile'], $user['mobile']);
	
	$db->update('user_relation',array('status'=>1),array('user_id'=>$loginid,'relation_id'=>$userid));
	$res=getRelationStatus($loginid, $userid);
	echo json_result($res);
}

//备注
function relationName(){
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$userid=filter($_REQUEST['userid']);
	$name=filter($_REQUEST['name']);
	if(empty($loginid)){
		echo json_result(null,2,'请先登录');
		return;
	}
        if(!empty($name)){
            $conditions=array('user_id'=>$loginid,'relation_id'=>$userid);
            $pinyin=!empty($name)?getFirstCharter($name):'';
            $db->update('user_relation',array('relation_name'=>$name,'relation_pinyin'=>$pinyin),$conditions);
        }
	echo json_result(array('success'=>'TRUE'));
}

//举报
function report(){
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$userid=filter($_REQUEST['userid']);
	$content=filter($_REQUEST['content']);
	if(empty($loginid)){
		echo json_result(null,2,'请先登录');
		return;
	}
	if(empty($content)){
		echo json_result(null,3,'请输入内容');
		return;
	}
	//举报
	$rinfo=array('user_id'=>$loginid,'relation_id'=>$userid,'content'=>$content);
	$reportcount=$db->getCount('user_report',array('relation_id'=>$userid));
	$rinfo['created']=date("Y-m-d H:i:s");
	$db->create('user_report', $rinfo);//举报

	$reportcount=$db->getCount('user_report',array('relation_id'=>$userid));
	$db->update('user', array('report'=>$reportcount));//更新举报次数

	echo json_result(array('success'=>"TRUE"));
}

//环信好友
function huanxinFriends(){
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$login=$db->getRow('user',array('id'=>$loginid),array('mobile'));

	$HuanxinObj=Huanxin::getInstance();
	$huserObj=$HuanxinObj->getFriends($login['mobile']);
	print_r($huserObj);
}

//环信黑名单
function huanxinBlocks(){
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$login=$db->getRow('user',array('id'=>$loginid),array('mobile'));

	$HuanxinObj=Huanxin::getInstance();
	$huserObj=$HuanxinObj->getBlocks($login['mobile']);
	print_r($huserObj);
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
			$info['relation']='拉黑';//黑名单
			$info['relation_status']=5;
		}
	}
	if($myfav_count<=0&&$myfun_count<=0){
		$info['relation']='陌生人';//陌生人
		$info['relation_status']=1;
	}
	return $info;
}

