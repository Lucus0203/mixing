<?php
class Controller_Default extends FLEA_Controller_Action {
	/**
	 * 
	 * Enter description here ...
	 * @var Class_Common
	 */
	var $_common;
	var $_userid;
	var $_admin;
	
	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );

		$this->_admin = get_singleton ( "Model_Admin" );
		
		$this->_userid = isset ( $_SESSION ['loginuserid'] ) ? $_SESSION ['loginuserid'] : "";
	}
	
	/**
	 * Enter description here...
	 *
	 */
	function actionIndex() {
		if(empty($_SESSION ['loginuserid'])){
			$url=url('Default','Login');
			redirect($url);
		}
		
		$this->_common->show ( array ('main' => 'index.tpl') );
	}
	
	/**
	 * Enter description here...
	 *
	 */
	function actionLogin() {
		$url = @$_SERVER [HTTP_REFERER];
		if ($url == "" && preg_match ( '/Login/i', $url )) {
			$url = url ( "Default", "Index" );
		}
		$error_msg = "";
		$user = @$_POST ["admname"];
		$pass=isset($_POST ["password"])?$_POST ["password"]:"";
		
		$username     = @$_COOKIE['cofeCookie']['username'];
		$userpassword = @$_COOKIE['cofeCookie']['password'];
		
		if ($user != "" && $pass != "") {
			$condition = array ('adm_name' => $user);
			$userinfo = $this->_admin->find ( $condition );
			if (count ( $userinfo ) > 0 && is_array ( $userinfo )) {
				$pwd = @$userinfo ['adm_password'];
				$pass=md5($pass);
				if ($pwd == $pass) {
					$_SESSION ['loginuserid'] = $userinfo ['id'];
					setcookie ( "cofeCookie[username]", '', time () + 86400,"/" );
					setcookie ( "cofeCookie[password]", '', time () + 86400,"/" );
					$url = url ( "Default", "Index" );
					redirect ( $url );
				}else{
					$error_msg="密码错误";
				}
			}else{
				$error_msg="登录名和密码错误";
			}
		}
		$this->_common->display ( 'login.tpl', array ('url'=>$url,'error_msg'=>$error_msg,'username'=>$username,'userpassword'=>$userpassword) );
	
	}
	
	/**
	 * Enter description here...
	 *
	 */
	function actionLoginOut() {
		unset ( $_SESSION ['loginuserid'] );
		$url = url ( "Default", "Login" );
		redirect ( $url );
	}
	
	
}

?>