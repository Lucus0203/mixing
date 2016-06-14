<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
include_once "wxBizMsg/wxBizMsgCrypt.php";
class WeChat {
	var $_appID='wx8d69b946f39e0a65';//咖啡约我 wx8d69b946f39e0a65//k3coffee wx92dce7b18073925e
	var $_appsecret='933ba61a9ca2fb8fad1aaa0f6779f15f';//咖啡约我 933ba61a9ca2fb8fad1aaa0f6779f15f //k3coffee c78f52ef6a1b0acf1204d3aa46c5983e
	var $_access_token;
	var $_token_file;
        
	function __construct() {
		$this->_token_file=dirname(__FILE__) . '/access_token.wx';
		$ctime = filectime($this->_token_file);
		$this->_access_token = file_get_contents($this->_token_file);
		if(empty($this->_access_token)||(time() - $ctime)>=7200){
			$this->setToken();
		}
	}
	
	private function __clone() {
	}
	function setToken(){
		$tokenurl="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->_appID."&secret=".$this->_appsecret;
		$data=$this->returnWeChatJsonData($tokenurl);
		$this->_access_token=$data->access_token;
		file_put_contents($this->_token_file, $data->access_token);
	}
	
	function getToken(){
		return $this->_access_token;
	}
        
        //返回构造好的跳转链接
        function getCodeRedirect($redirecturi,$state){
            $uri="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->_appID."&redirect_uri=".  urlencode($redirecturi)  ."&response_type=code&scope=snsapi_userinfo&state=".$state."#wechat_redirect";
            return $uri;
        }
	/**
         * access_token	接口调用凭证
           expires_in	access_token接口调用凭证超时时间，单位（秒）
           refresh_token	用户刷新access_token
           openid	授权用户唯一标识
           scope	用户授权的作用域，使用逗号（,）分隔
           unionid	当且仅当该网站应用已获得该用户的userinfo授权时，才会出现该字段。
         * 
         */
        function getTokenData($code){
            $uri="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->_appID."&secret=".$this->_appsecret."&code=".$code."&grant_type=authorization_code";
            $data=$this->returnWeChatJsonData($uri);
            return $data;
        }
        
        /**
         * 获取用户信息
         *  openid	普通用户的标识，对当前开发者帐号唯一
            nickname	普通用户昵称
            sex	普通用户性别，1为男性，2为女性
            province	普通用户个人资料填写的省份
            city	普通用户个人资料填写的城市
            country	国家，如中国为CN
            headimgurl	用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空
            privilege	用户特权信息，json数组，如微信沃卡用户为（chinaunicom）
            unionid	用户统一标识。针对一个微信开放平台帐号下的应用，同一用户的unionid是唯一的。
         */
	function getUserInfo($token,$openid){
            $uri="https://api.weixin.qq.com/sns/userinfo?access_token=".$token."&openid=".$openid;
            $userInfo=$this->returnWeChatJsonData($uri);
            return $userInfo;
        }
        /**
         * 
         * @param type $menu
         * button	是	一级菜单数组，个数应为1~3个
            sub_button	否	二级菜单数组，个数应为1~5个
            type	是	菜单的响应动作类型
            name	是	菜单标题，不超过16个字节，子菜单不超过40个字节
            key	click等点击类型必须	菜单KEY值，用于消息接口推送，不超过128字节
            url	view类型必须	网页链接，用户点击菜单可打开链接，不超过256字节
            media_id	media_id类型和view_limited类型必须	调用新增永久素材接口返回的合法media_id
         */
        function createMenu($menu){
            return $this->sendJsonData( "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->_access_token,$menu,1);
        }
        
        /*
         * type	是	素材的类型，图片（image）、视频（video）、语音 （voice）、图文（news）
            offset	是	从全部素材的该偏移位置开始返回，0表示从第一个素材 返回
            count	是	返回素材的数量，取值在1到20之间
         */
        function getMedialist($type,$offset,$count){
            $obj=array('type'=>$type,'offset'=>$offset,'count'=>$count);
            return $this->returnWeChatJsonData("https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=".$this->_access_token,json_encode($obj),1);
        }
	//发送客服消息
	function sendCustomMsg($msg,$touser){
		$sendurl='https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$this->_access_token;
		$data=array('touser'=>$touser,
			'msgtype'=>'text',
			'text'=>array('content'=>'@msg@'));
		$data=json_encode($data);
		$data=str_replace('@msg@', $msg, $data);
		$this->res($this->sendJsonData($sendurl,$data,1));
	}
	
	//处理返回值
	function res($data){
		if(!empty($data->errcode)){//如果有错误返回错误值
			switch ($data->errcode){
				case 40014:
					$this->setToken();
					break;
				default:
					break;
			}
			return $data->errcode;
		}else{
			return $data;
		}
	}
	
	//发送json数据
	function sendJsonData($url,$parm="",$post=0){
		$ch = curl_init(); //初始化curl
		curl_setopt($ch, CURLOPT_URL, $url); //抓取指定网页
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_POST, $post); //post提交方式
		curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parm);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-Type:application/json"));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);// 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		$jsondata = curl_exec($ch); //运行curl
		curl_close($ch);
		return json_decode($jsondata);
		
	}
	
	//请求json数据
	function returnWeChatJsonData($url,$parm=array(),$post=0){
		$ch = curl_init(); //初始化curl
		curl_setopt($ch, CURLOPT_URL, $url); //抓取指定网页
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_POST, $post); //post提交方式
		curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parm);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);// 终止从服务端进行验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		$jsondata = curl_exec($ch); //运行curl
		curl_close($ch);
		return json_decode($jsondata);
	}
	
	public function valid()
    {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
      	//extract post data
        if (!empty($postStr)){
        /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
           the best way is to check the validity of xml by yourself */
        libxml_disable_entity_loader(true);
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        $msgType = $postObj->MsgType;
        $event =  $postObj->Event;
         if($msgType=='event'&&$event=='subscribe'){
                $this->sendCustomMsg("感谢您的关注",$fromUsername);
         }
        $this->sendCustomMsg("感谢您的关注",$fromUsername);
        $keyword = trim($postObj->Content);
        $time = time();
        $textTpl = "<xml>
                                                <ToUserName><![CDATA[%s]]></ToUserName>
                                                <FromUserName><![CDATA[%s]]></FromUserName>
                                                <CreateTime>%s</CreateTime>
                                                <MsgType><![CDATA[%s]]></MsgType>
                                                <Content><![CDATA[%s]]></Content>
                                                <FuncFlag>0</FuncFlag>
                                                </xml>";             
		if(!empty( $keyword )){
              		$msgType = "transfer_customer_service";//text
                	$contentStr = "感谢您关注咖啡约我";
                	$resultStr = sprintf($textTpl, $fromUsername,$toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
                }else{
                	echo "Input something...";
                }

        }else {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = "mixing";
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
	
}
?>