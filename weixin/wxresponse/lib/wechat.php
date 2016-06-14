<?php
define('APPID', 'wx8d69b946f39e0a65');
define('APPSECRET', '933ba61a9ca2fb8fad1aaa0f6779f15f');
define("TOKEN", "mixing");
class WeChat {
	var $_appID=APPID;
	var $_appsecret=APPSECRET;
	var $_access_token;
	var $_token_file;
	
	private static $instance;
	private function __construct() {
		$this->_token_file=dirname(__FILE__) . '/access_token.wx';
		$ctime = filectime($this->_token_file);
		$this->_access_token = file_get_contents($this->_token_file);
		if(empty($this->_access_token)||(time() - $ctime)>=7200){
			$this->setToken();
		}
	}
	
	private function __clone() {
	}
	
	public static function getInstance() {
		if (! self::$instance instanceof self) {
			self::$instance = new WeChat;
		}
		return self::$instance;
	}
	function setToken(){
		$tokenurl="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APPID."&secret=".APPSECRET;
		$data=$this->returnWeChatJsonData($tokenurl);
		$this->_access_token=$data->access_token;
		file_put_contents($this->_token_file, $data->access_token);
	}
	
	function getToken(){
		return $this->_access_token;
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
//                 if($msgType=='event'&&$event=='subscribe'){
//                 	$this->sendCustomMsg("感谢您的关注",$fromUsername);
//                 }
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
				if(!empty( $keyword ))
                {
              		$msgType = "text";
                	$contentStr = "Welcome to wechat world!";
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
        		
		$token = TOKEN;
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