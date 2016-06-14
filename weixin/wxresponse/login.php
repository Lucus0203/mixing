<?php

require_once 'lib/wechat.php';
function getToken($code){
    $wechatObj = WeChat::getInstance();
    $appid=$wechatObj->_appID;
    $appsecret=$wechatObj->_appsecret;
    $redirecturi="http://weixin.xn--8su10a.com/login.php";
    $uri="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";
    $data=$wechatObj->returnWeChatJsonData($uri);
    $uri="https://api.weixin.qq.com/sns/userinfo?access_token=".$data->access_token."&openid=".$appid;
    $userInfo=$wechatObj->returnWeChatJsonData($uri);
    print_r($userInfo);
}
if(!empty($_GET['code'])){
    getToken($_GET['code']);
}else{
    header("Location: http://weixin.xn--8su10a.com"); 
}
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

