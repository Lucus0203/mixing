<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Wxresponse extends CI_Controller {
	
	function __construct() {
		parent::__construct ();
		$this->load->library ( array( 'wechat' ));
		$this->load->helper ( array (
				'form',
				'url' 
		) );
	}
	
	public function index() {
            //$this->wechat->responseMsg();
            //get post data, May be due to the different environments
            $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
            $time = time();
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
            $eventkey = $postObj->EventKey;
            $keyword = trim($postObj->Content);
            $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>";  
             if($msgType=='event'&&$event=='subscribe'){
                $msgType = "text";
                $contentStr = "感谢您关注咖啡约我";
                $resultStr = sprintf($textTpl, $fromUsername,$toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
             }else if($msgType=='event'&&$event=='CLICK'&&$eventkey=='K_CONTACT_US'){
                $msgType = "text";
                $contentStr = "邮箱:wx@mixing.win
电话:021-63560568
官方网址:http://app.xn--8su10a.com";
                $resultStr = sprintf($textTpl, $fromUsername,$toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            }else if(!empty( $keyword )){
                $msgType = "transfer_customer_service";//客服
                $contentStr = "";
                $resultStr = sprintf($textTpl, $fromUsername,$toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            }else{
                echo '';
            }

            }else {
                    echo "";
                    exit;
            }
	}
	
}
