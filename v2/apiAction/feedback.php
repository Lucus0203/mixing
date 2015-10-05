<?php
$act=filter($_REQUEST['act']);
switch ($act){
	case 'sendMsg':
		sendMsg();//用户反馈
		break;
	default:
		break;
}

function sendMsg(){
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$content=filter($_REQUEST['content']);
	if(empty($loginid)){
		echo json_result(null,'2','您还未登录');
		return;
	}
	$feedback=array('user_id'=>$loginid,'type'=>'mixing','content'=>$content,'created'=>date("Y-m-d H:i:s"));
	$db->create('feedback', $feedback);
	echo json_result(array('success'=>'TRUE'));
}