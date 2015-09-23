<?php
define('CLIENTID', 'YXA6QsMZ4HnIEeSoQhVnJpbyzQ');
define('CLIENTSECRET', 'YXA6_WPJ7tDIfsPSljWqdzHae7SAUV0');
Class Huanxin {
	var $_access_token;
	var $_token_file;
	private $url;
	private static $instance;
	private function __construct() {
		$this->url = 'https://a1.easemob.com/zcsy/coffee/';
			
		$this->_token_file=dirname(__FILE__) . '/access_token.hx';
		$ctime = filectime($this->_token_file);
		$this->_access_token = file_get_contents($this->_token_file);
		if(empty($this->_access_token)||(time() - $ctime)>=60*60*24*5){//五天
			$this->setToken();
		}
	}
	
	private function __clone() {
	}
	
	public static function getInstance() {
		if (! self::$instance instanceof self) {
			self::$instance = new Huanxin;
		}
		return self::$instance;
	}
	
	function setToken(){
		$tokenurl=$this->url . "token";
		$parm=array('grant_type'=>'client_credentials','client_id'=>CLIENTID,'client_secret'=>CLIENTSECRET);
		$data=$this->sendJsonDataWithNoToken($tokenurl,json_encode($parm),1);
		$this->_access_token=$data->access_token;
		file_put_contents($this->_token_file, $data->access_token);
	}
	
	function getToken(){
		return $this->_access_token;
	}
	
	//注册
	function addNewAppUser($username,$password){
		$data=array('username'=>$username,'password'=>$password);
		$url = $this->url . "users";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		return $this->postCurl ( $url, $data, $header, $type = "POST" );
		//return $this->sendJsonData($this->url . "users",json_encode($data),1);
	}
	//修改密码
	function updatePass($username,$password){
		$data=array('newpassword'=>$password);
		$url = $this->url . "users/{$username}/password";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		return $this->postCurl ( $url, $data, $header, $type = "POST" );
		//return $this->sendJsonData($this->url . "users/{$username}/password",json_encode($data),1);
	}
	//查找用户
	function findIMUser($username){
		$url = $this->url . "users/{$username}";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		return $this->postCurl ( $url, '', $header, $type = "GET" );
		//return $this->getJsonData($this->url . "users/".$username);
	}
	//加入黑名单
	function block($login,$user){
		$data=array('usernames'=>array($user));
		$url = $this->url . "users/{$login}/blocks/users";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		return $this->postCurl ( $url, $data, $header, $type = "POST" );
		//return $this->sendJsonData($this->url . "users/{$login}/blocks/users'",json_encode($data),1);
	}
	//移除黑名单
	function unblock($login,$user){
		$url = $this->url . "users/{$login}/blocks/users/{$user}";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		return $this->postCurl ( $url, '', $header, $type = "DELETE" );
		//return $this->sendJsonData($this->url . "users/{$login}/blocks/users/{$user}",null,"DELETE");
	}
	//查看用户好友
	function getFriends($login){
		$url = $this->url . "users/{$login}/contacts/users";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		return $this->postCurl ( $url, '', $header, $type = "GET" );
		//return $this->getJsonData($this->url . "users/{$login}/contacts/users");
	}
	//查看用户黑名单
	function getBlocks($login){
		$url = $this->url . "users/{$login}/blocks/users";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		return $this->postCurl ( $url, '', $header, $type = "GET" );
		//return $this->getJsonData("https://a1.easemob.com/zcsy/coffee/users/{$login}/blocks/users");
	}
        //发送消息给用户
        function sendmsgToUser($from,$to,$msg){
		$data=array('target_type'=>'users',
                    'target'=>array($to),
                    'msg'=>array('type'=>'txt','msg'=>$msg),
                    'from'=>$from);
		$url = $this->url . "chatgroups";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		return $this->postCurl ( $url, $data, $header, $type = "POST" );
        }
	//创建群
        function createGroup($groupname,$desc,$maxusers,$owner){
		$data=array('groupname'=>$groupname,//群组名称, 此属性为必须的
                    'desc'=>$desc,//群组描述, 此属性为必须的
                    'public'=>true,//是否是公开群, 此属性为必须的,为false时为私有群
                    'maxusers'=>$maxusers,//群组成员最大数(包括群主), 值为数值类型,默认值200,此属性为可选的
                    'approval'=>false,
                    'owner'=>$owner
                    );
		$url = $this->url . "chatgroups";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		return $this->postCurl ( $url, $data, $header, $type = "POST" );
        }
	//获取建群
        function getGroup($groupid){
		$url = $this->url . "chatgroups/{$groupid}";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		return $this->postCurl ( $url, '', $header, $type = "GET" );
        }
        //修改群
        function updateGroup($groupid,$groupname,$desc,$maxusers){
		$data=array('groupname'=>$groupname,//群组名称, 此属性为必须的
                    'description'=>$desc,//群组描述, 此属性为必须的
                    'maxusers'=>$maxusers//群组成员最大数(包括群主), 值为数值类型,默认值200,此属性为可选的
                    );
		$url = $this->url . "chatgroups/{$groupid}";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		return $this->postCurl ( $url, $data, $header, $type = "PUT" );
        }
        //删除群
        function delGroup($groupid){
		$url = $this->url . "chatgroups/{$groupid}";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		return $this->postCurl ( $url, '', $header, $type = "DELETE" );
        }
        //获取群成员
        function getGroupUsers($groupid){
		$url = $this->url . "chatgroups/{$groupid}/users";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		return $this->postCurl ( $url, '', $header, $type = "GET" );
        }
        //增加群成员
        function addGroupUser($groupid,$username){
		$url = $this->url . "chatgroups/{$groupid}/users/{$username}";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		return $this->postCurl ( $url, $data, $header, $type = "POST" );
        }
        //减少群成员
        function delGroupUser($groupid,$username){
		$url = $this->url . "chatgroups/{$groupid}/users/{$username}";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		return $this->postCurl ( $url, '', $header, $type = "POST" );
        }
        //获取一个用户参与的所有群组
        function getGroups($username){
		$url = $this->url . "users/{$username}/joined_chatgroups";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		return $this->postCurl ( $url, '', $header, $type = "GET" );
        }
        
	/**
	 * CURL Post
	 */
	private function postCurl($url, $option, $header = 0, $type = 'POST') {
		array_push($header, 'Accept:application/json');
		array_push($header, 'Content-Type:application/json');
		
		$curl = curl_init (); // 启动一个CURL会话
		curl_setopt ( $curl, CURLOPT_URL, $url ); // 要访问的地址
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, FALSE ); // 对认证证书来源的检查
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, FALSE ); // 从证书中检查SSL加密算法是否存在
		curl_setopt ( $curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)' ); // 模拟用户使用的浏览器
		curl_setopt ( $curl, CURLOPT_TIMEOUT, 30 ); // 设置超时限制防止死循环
		curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header ); // 设置HTTP头
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 ); // 获取的信息以文件流的形式返回
		if (! empty ( $option )) {
			$options = json_encode ( $option );
			curl_setopt ( $curl, CURLOPT_POSTFIELDS, $options ); // Post提交的数据包
		}
		switch ($type){
			case "GET" :
				curl_setopt($curl, CURLOPT_HTTPGET, true);
				break;
			case "POST":
				curl_setopt($curl, CURLOPT_POST,true);
				break;
			case "PUT" :
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
				break;
			case "DELETE":
				curl_setopt ($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
				break;
		}
		curl_setopt ( $curl, CURLOPT_CUSTOMREQUEST, $type );
		
		$result = curl_exec ( $curl ); // 执行操作
		//$res = object_array ( json_decode ( $result ) );
		//$res ['status'] = curl_getinfo ( $curl, CURLINFO_HTTP_CODE );
		//pre ( $res );
		curl_close ( $curl ); // 关闭CURL会话
		return json_decode($result);
	}

	//请求json数据
	function getJsonData($url,$parm="",$post=0){
		$ch = curl_init(); //初始化curl
		curl_setopt($ch, CURLOPT_URL, $url); //抓取指定网页
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_POST, $post); //post提交方式
		curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parm);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-Type:application/json","Authorization: Bearer ".$this->_access_token));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);// 终止从服务端进行验证
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		$jsondata = curl_exec($ch); //运行curl
		curl_close($ch);
		return json_decode($jsondata);
	}
	
	//发送json数据
	function sendJsonData($url,$parm="",$post=0){
		$ch = curl_init(); //初始化curl
		curl_setopt($ch, CURLOPT_URL, $url); //抓取指定网页
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_POST, $post); //post提交方式
		curl_setopt($ch, CURLOPT_HEADER, 0); //设置header
		curl_setopt($ch, CURLOPT_POSTFIELDS, $parm);
		curl_setopt($ch, CURLOPT_HTTPHEADER,array("Content-Type:application/json","Authorization: Bearer ".$this->_access_token));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);// 终止从服务端进行验证
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		$jsondata = curl_exec($ch); //运行curl
		curl_close($ch);
		return json_decode($jsondata);
	
	}
	
	
	//更新token时使用
	function sendJsonDataWithNoToken($url,$parm="",$post=0){
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
	
	
	
}

//$hx=Huanxin::getInstance();
//echo $hx->getToken();