<?php
require_once 'lib/wechat.php';
//define your token
$wechatObj = WeChat::getInstance();
$wechatObj->valid();
//网站登录获取code
//function getCode(){
//    $wechatObj = WeChat::getInstance();
//    $appid=$wechatObj->_appID;
//    $appsecret=$wechatObj->_appsecret;
//    $redirecturi="http://weixin.xn--8su10a.com/login.php";
//    $uri="https://open.weixin.qq.com/connect/qrconnect?appid=".$appid."&redirect_uri=".  urlencode($redirecturi)  ."&response_type=code&scope=snsapi_login&state=123456#wechat_redirect";
//    header("Location: ".$uri); 
//}
//getCode();
//$wechat->responseMsg('测试信息,您好~',$touser);
?>