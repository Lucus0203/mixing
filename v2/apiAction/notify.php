<?php
$act=filter($_REQUEST['act']);
switch ($act){
	case 'getNotifys':
		getNotifys();//查看消息通知
		break;
	case 'readNotify':
		readNotify();//阅读消息
		break;
        case 'readAll':
                readAll();//全部已读
                break;
        case 'countRead':
                countRead();//查看未读数
                break;
	default:
		break;
}

//查看消息通知 type //invitation //payer //waiter //depositer //payer //receiver //mixing 官方活动(链接)
function getNotifys(){
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$page_no = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
        $sql="select id,img,send_time,msg,url,type,dataid,isread from ".DB_PREFIX."notify where user_id=$loginid or (user_id is null and type='mixing') order by id desc limit $start,$page_size";
        $data=$db->getAllBySql($sql);
        //$data=$db->getAll('notify',array('user_id'=>$loginid."' or (user_id is null and type='mixing')"),array('id','img','send_time','msg','url','type','dataid','isread'),"order by id desc limit $start,$page_size");
        echo json_result(array('notifys'=>$data));
}

//阅读消息
function readNotify(){
	global $db;
	$notifyid=filter($_REQUEST['notifyid']);
        $db->update('notify',array('isread'=>2),array('id'=>$notifyid));
        echo json_result(array('success'=>'TRUE'));
}

//全部已读
function readAll(){
	global $db;
	$loginid=filter($_REQUEST['loginid']);
        $db->update('notify',array('isread'=>2),array('user_id'=>$loginid));
        echo json_result(array('success'=>'TRUE'));
}

//查看未读消息数
function countRead(){
	global $db;
	$loginid=filter($_REQUEST['loginid']);
        $usercreated=$db->getRow('user',array('id'=>$loginid),array('created'));
        $countSql="select * from ".DB_PREFIX."notify notify where (user_id={$loginid} and isread=1) ";
        $count=$db->getCountBySql($countSql);
        //$count=$db->getCount('notify',array('user_id'=>$loginid,'isread'=>1));
        $lastRow="select msg,DATE_FORMAT(send_time,'%Y-%m-%d %H:%i') as created from ".DB_PREFIX."notify notify where (user_id={$loginid} or type='mixing') order by id desc limit 0,1 ";
        $data=$db->getRowBySql($lastRow);
        $isHasGroup=$db->getCount('chatgroup_user',array('user_id'=>$loginid)) > 0 ? 1 : 2 ;//1有2无
        echo json_result(array('count'=>$count,'msg'=>$data['msg'],'created'=>$data['created'],'isHasGroup'=>$isHasGroup));
}


