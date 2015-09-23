<?php

/**
 * 友盟通知接口
 * http://message.umeng.com/
 * 主要用个人自定义通知
 * Android sendAndroidCustomizedcast()
 * IOS sendIOSCustomizedcast()
 */
require_once(dirname(__FILE__) . '/' . 'Umeng/android/AndroidBroadcast.php');
require_once(dirname(__FILE__) . '/' . 'Umeng/android/AndroidFilecast.php');
require_once(dirname(__FILE__) . '/' . 'Umeng/android/AndroidGroupcast.php');
require_once(dirname(__FILE__) . '/' . 'Umeng/android/AndroidUnicast.php');
require_once(dirname(__FILE__) . '/' . 'Umeng/android/AndroidCustomizedcast.php');
require_once(dirname(__FILE__) . '/' . 'Umeng/ios/IOSBroadcast.php');
require_once(dirname(__FILE__) . '/' . 'Umeng/ios/IOSFilecast.php');
require_once(dirname(__FILE__) . '/' . 'Umeng/ios/IOSGroupcast.php');
require_once(dirname(__FILE__) . '/' . 'Umeng/ios/IOSUnicast.php');
require_once(dirname(__FILE__) . '/' . 'Umeng/ios/IOSCustomizedcast.php');

define('Android_umeng_appkey', '');
define('Android_umeng_master_secret', '');
define('Android_umeng_message_secret', '');
define('IOS_umeng_appkey','54f462edfd98c51d67000385');
define('IOS_master_secret','dha9fagbclfyotmycmdzjef2p6j6qdqj');

class Umeng {

    protected $appkey = NULL;
    protected $appMasterSecret = NULL;
    protected $timestamp = NULL;
    protected $validation_token = NULL;

    function __construct($client) {
    	if($client=='Android'){
	        $this->appkey = Android_umeng_appkey;
	        $this->appMasterSecret = Android_umeng_master_secret;
    	}elseif($client=='IOS'){
	        $this->appkey = IOS_umeng_appkey;
	        $this->appMasterSecret = IOS_master_secret;
    	}
        $this->timestamp = strval(time());
    }

    function sendAndroidBroadcast() {
        try {
            $brocast = new AndroidBroadcast();
            $brocast->setAppMasterSecret($this->appMasterSecret);
            $brocast->setPredefinedKeyValue("appkey", $this->appkey);
            $brocast->setPredefinedKeyValue("timestamp", $this->timestamp);
            $brocast->setPredefinedKeyValue("ticker", "Android broadcast ticker");
            $brocast->setPredefinedKeyValue("title", "中文的title");
            $brocast->setPredefinedKeyValue("text", "Android broadcast text");
            $brocast->setPredefinedKeyValue("after_open", "go_app");
            // Set 'production_mode' to 'false' if it's a test device. 
            // For how to register a test device, please see the developer doc.
            $brocast->setPredefinedKeyValue("production_mode", "true");
            // [optional]Set extra fields
            $brocast->setExtraField("test", "helloworld");
            print("Sending broadcast notification, please wait...\r\n");
            $brocast->send();
            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    function sendAndroidUnicast() {
        try {
            $unicast = new AndroidUnicast();
            $unicast->setAppMasterSecret($this->appMasterSecret);
            $unicast->setPredefinedKeyValue("appkey", $this->appkey);
            $unicast->setPredefinedKeyValue("timestamp", $this->timestamp);
            // Set your device tokens here
            $unicast->setPredefinedKeyValue("device_tokens", "xx");
            $unicast->setPredefinedKeyValue("ticker", "Android unicast ticker");
            $unicast->setPredefinedKeyValue("title", "Android unicast title");
            $unicast->setPredefinedKeyValue("text", "Android unicast text");
            $unicast->setPredefinedKeyValue("after_open", "go_app");
            // Set 'production_mode' to 'false' if it's a test device. 
            // For how to register a test device, please see the developer doc.
            $unicast->setPredefinedKeyValue("production_mode", "true");
            // Set extra fields
            $unicast->setExtraField("test", "helloworld");
            print("Sending unicast notification, please wait...\r\n");
            $unicast->send();
            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    function sendAndroidFilecast() {
        try {
            $filecast = new AndroidFilecast();
            $filecast->setAppMasterSecret($this->appMasterSecret);
            $filecast->setPredefinedKeyValue("appkey", $this->appkey);
            $filecast->setPredefinedKeyValue("timestamp", $this->timestamp);
            $filecast->setPredefinedKeyValue("ticker", "Android filecast ticker");
            $filecast->setPredefinedKeyValue("title", "Android filecast title");
            $filecast->setPredefinedKeyValue("text", "Android filecast text");
            $filecast->setPredefinedKeyValue("after_open", "go_app");  //go to app
            print("Uploading file contents, please wait...\r\n");
            // Upload your device tokens, and use '\n' to split them if there are multiple tokens
            $filecast->uploadContents("aa" . "\n" . "bb");
            print("Sending filecast notification, please wait...\r\n");
            $filecast->send();
            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    function sendAndroidGroupcast() {
        try {
            /*
             *  Construct the filter condition:
             *  "where": 
             * 	{
             * 		"and": 
             * 		[
             * 			{"tag":"test"},
             * 			{"tag":"Test"}
             * 		]
             * 	}
             */
            $filter = array(
                "where" => array(
                    "and" => array(
                        array(
                            "tag" => "test"
                        ),
                        array(
                            "tag" => "Test"
                        )
                    )
                )
            );

            $groupcast = new AndroidGroupcast();
            $groupcast->setAppMasterSecret($this->appMasterSecret);
            $groupcast->setPredefinedKeyValue("appkey", $this->appkey);
            $groupcast->setPredefinedKeyValue("timestamp", $this->timestamp);
            // Set the filter condition
            $groupcast->setPredefinedKeyValue("filter", $filter);
            $groupcast->setPredefinedKeyValue("ticker", "Android groupcast ticker");
            $groupcast->setPredefinedKeyValue("title", "Android groupcast title");
            $groupcast->setPredefinedKeyValue("text", "Android groupcast text");
            $groupcast->setPredefinedKeyValue("after_open", "go_app");
            // Set 'production_mode' to 'false' if it's a test device. 
            // For how to register a test device, please see the developer doc.
            $groupcast->setPredefinedKeyValue("production_mode", "true");
            print("Sending groupcast notification, please wait...\r\n");
            $groupcast->send();
            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    //自定义单独设备通知
    function sendAndroidCustomizedcast($alias_type, $login_id, $ticker, $title, $text, $after_open = "go_app", $activity = "", $extras=array() ) {
        try {
            $customizedcast = new AndroidCustomizedcast();
            $customizedcast->setAppMasterSecret($this->appMasterSecret);
            $customizedcast->setPredefinedKeyValue("appkey", $this->appkey);
            $customizedcast->setPredefinedKeyValue("timestamp", $this->timestamp);
            // Set your alias here, and use comma to split them if there are multiple alias.
            // And if you have many alias, you can also upload a file containing these alias, then 
            // use file_id to send customized notification.
            $customizedcast->setPredefinedKeyValue("alias", $login_id);
            // Set your alias_type here
            $customizedcast->setPredefinedKeyValue("alias_type", $alias_type);
            $customizedcast->setPredefinedKeyValue("ticker", $ticker);
            $customizedcast->setPredefinedKeyValue("title", $title);
            $customizedcast->setPredefinedKeyValue("text", $text);
            $customizedcast->setPredefinedKeyValue("after_open", $after_open);
            $customizedcast->setPredefinedKeyValue("activity", $activity);
            $customizedcast->setExtraField("from", "push");
            foreach ($extras as $key=>$ext){
            	$customizedcast->setExtraField($key, $ext);
            }
            //print("Sending customizedcast notification, please wait...\r\n");
            $customizedcast->send();
            //print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            //print("Caught exception: " . $e->getMessage());
        }
    }

    function sendIOSBroadcast() {
        try {
            $brocast = new IOSBroadcast();
            $brocast->setAppMasterSecret($this->appMasterSecret);
            $brocast->setPredefinedKeyValue("appkey", $this->appkey);
            $brocast->setPredefinedKeyValue("timestamp", $this->timestamp);

            $brocast->setPredefinedKeyValue("alert", "IOS 广播测试");
            $brocast->setPredefinedKeyValue("badge", 0);
            $brocast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $brocast->setPredefinedKeyValue("production_mode", "false");
            // Set customized fields
            $brocast->setCustomizedField("test", "helloworld");
            print("Sending broadcast notification, please wait...\r\n");
            $brocast->send();
            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    function sendIOSUnicast() {
        try {
            $unicast = new IOSUnicast();
            $unicast->setAppMasterSecret($this->appMasterSecret);
            $unicast->setPredefinedKeyValue("appkey", $this->appkey);
            $unicast->setPredefinedKeyValue("timestamp", $this->timestamp);
            // Set your device tokens here
            $unicast->setPredefinedKeyValue("device_tokens", "xx");
            $unicast->setPredefinedKeyValue("alert", "IOS 单播测试");
            $unicast->setPredefinedKeyValue("badge", 0);
            $unicast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $unicast->setPredefinedKeyValue("production_mode", "false");
            // Set customized fields
            $unicast->setCustomizedField("test", "helloworld");
            print("Sending unicast notification, please wait...\r\n");
            $unicast->send();
            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    function sendIOSFilecast() {
        try {
            $filecast = new IOSFilecast();
            $filecast->setAppMasterSecret($this->appMasterSecret);
            $filecast->setPredefinedKeyValue("appkey", $this->appkey);
            $filecast->setPredefinedKeyValue("timestamp", $this->timestamp);

            $filecast->setPredefinedKeyValue("alert", "IOS 文件播测试");
            $filecast->setPredefinedKeyValue("badge", 0);
            $filecast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $filecast->setPredefinedKeyValue("production_mode", "false");
            print("Uploading file contents, please wait...\r\n");
            // Upload your device tokens, and use '\n' to split them if there are multiple tokens
            $filecast->uploadContents("aa" . "\n" . "bb");
            print("Sending filecast notification, please wait...\r\n");
            $filecast->send();
            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    function sendIOSGroupcast() {
        try {
            /*
             *  Construct the filter condition:
             *  "where": 
             * 	{
             * 		"and": 
             * 		[
             * 			{"tag":"iostest"}
             * 		]
             * 	}
             */
            $filter = array(
                "where" => array(
                    "and" => array(
                        array(
                            "tag" => "iostest"
                        )
                    )
                )
            );

            $groupcast = new IOSGroupcast();
            $groupcast->setAppMasterSecret($this->appMasterSecret);
            $groupcast->setPredefinedKeyValue("appkey", $this->appkey);
            $groupcast->setPredefinedKeyValue("timestamp", $this->timestamp);
            // Set the filter condition
            $groupcast->setPredefinedKeyValue("filter", $filter);
            $groupcast->setPredefinedKeyValue("alert", "IOS 组播测试");
            $groupcast->setPredefinedKeyValue("badge", 0);
            $groupcast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $groupcast->setPredefinedKeyValue("production_mode", "false");
            print("Sending groupcast notification, please wait...\r\n");
            $groupcast->send();
            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    function sendIOSCustomizedcast($alias_type, $login_id, $alert, $extras=array()) {
        try {
            $customizedcast = new IOSCustomizedcast();
            $customizedcast->setAppMasterSecret($this->appMasterSecret);
            $customizedcast->setPredefinedKeyValue("appkey", $this->appkey);
            $customizedcast->setPredefinedKeyValue("timestamp", $this->timestamp);

            // Set your alias here, and use comma to split them if there are multiple alias.
            // And if you have many alias, you can also upload a file containing these alias, then 
            // use file_id to send customized notification.
            $customizedcast->setPredefinedKeyValue("alias", $login_id);
            // Set your alias_type here
            $customizedcast->setPredefinedKeyValue("alias_type", $alias_type);
            $customizedcast->setPredefinedKeyValue("alert", $alert);
            $customizedcast->setPredefinedKeyValue("badge", 1);
            $customizedcast->setPredefinedKeyValue("sound", "default");
            // Set 'production_mode' to 'true' if your app is under production mode
            $customizedcast->setPredefinedKeyValue("production_mode", "true");
            foreach ($extras as $key=>$ext){
            	$customizedcast->setCustomizedField($key, $ext);
            }
            $customizedcast->setCustomizedField("display_type", "notification");
            //$customizedcast->setCustomizedField("display_type", "message");
            $body=array(
            		// 通知展现内容:
            		"ticker"=>$alert,     // 必填 通知栏提示文字
            		"title"=>"咖啡约我",      // 必填 通知标题
            		"text"=>$alert,       // 必填 通知文字描述
            		// 点击"通知"的后续行为，默认为打开app。
            		"after_open"=> "go_app" // 必填 值可以为:
	            		//"go_app": 打开应用
	            		//"go_url": 跳转到URL
	            		//"go_activity": 打开特定的activity
	            		//"go_custom": 用户自定义内容。
            );
            $customizedcast->setCustomizedField("body", $body);
            //print("Sending customizedcast notification, please wait...\r\n");
            $res=$customizedcast->send();
            //print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            //print("Caught exception: " . $e->getMessage());
        }
    }

}

// Set your appkey and master secret here
//$demo = new Demo("your appkey", "your app master secret");
//$demo->sendAndroidUnicast();
/* these methods are all available, just fill in some fields and do the test
 * $demo->sendAndroidBroadcast();
 * $demo->sendAndroidFilecast();
 * $demo->sendAndroidGroupcast();
 * $demo->sendAndroidCustomizedcast();
 *
 * $demo->sendIOSBroadcast();
 * $demo->sendIOSUnicast();
 * $demo->sendIOSFilecast();
 * $demo->sendIOSGroupcast();
 * $demo->sendIOSCustomizedcast();
 */
