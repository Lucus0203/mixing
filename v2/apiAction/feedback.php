<?php
$act=filter($_REQUEST['act']);
switch ($act){
	case 'sendFeedback':
		sendFeedback();//用户反馈
		break;
	default:
		break;
}

function sendFeedback(){
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	$content=filter($_REQUEST['content']);
	if(empty($loginid)){
		echo json_result(null,'38','您还未登录');
		return;
	}
	$feedback=array('user_id'=>$loginid,'content'=>$content,'created'=>date("Y-m-d H:i:s"));
	$db->create('feedback', $feedback);
	echo json_result(array('uesrid'=>$loginid));
}