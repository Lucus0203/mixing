<?php
require_once APP_DIR.DS.'apiLib'.DS.'ext'.DS.'Sms.php';
$act=filter($_REQUEST['act']);
switch ($act){
	case 'sms':
		sms();//注册
		break;
	default:
		break;
}

//注册
function sms(){
	$sms=new Sms();
	echo $sms->sendMsg("您本次验证码是:278232，欢迎您使用", "18521356928");
}