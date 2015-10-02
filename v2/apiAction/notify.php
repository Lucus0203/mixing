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

//查看消息通知
function getNotifys(){
	global $db;
	$loginid=filter($_REQUEST['loginid']);
        $data=$db->getAll('notify',array('user_id'=>$loginid),array('id','img','send_time','msg','type','dataid','isread'));
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
        $count=$db->getCount('notify',array('user_id'=>$loginid,'isread'=>1));
        echo json_result(array('count'=>$count));
}


