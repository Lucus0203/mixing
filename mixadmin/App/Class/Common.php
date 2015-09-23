<?php
if (! function_exists ( 'mb_substr' )) {
	function mb_substr($str, $start, $len = '', $encoding = "UTF-8") {
		$limit = strlen ( $str );
		
		for($s = 0; $start > 0; -- $start) { // found the real start
			if ($s >= $limit)
				break;
			
			if ($str [$s] <= "\x7F")
				++ $s;
			else {
				++ $s; // skip length
				

				while ( $str [$s] >= "\x80" && $str [$s] <= "\xBF" )
					++ $s;
			}
		}
		
		if ($len == '')
			return substr ( $str, $s );
		else
			for($e = $s; $len > 0; -- $len) { //found the real end
				if ($e >= $limit)
					break;
				
				if ($str [$e] <= "\x7F")
					++ $e;
				else {
					++ $e; //skip length
					

					while ( $str [$e] >= "\x80" && $str [$e] <= "\xBF" && $e < $limit )
						++ $e;
				}
			}
		
		return substr ( $str, $s, $e - $s );
	}
}
/**

 * Project: 公共函数

 * Author: libin

 * Date: 2008年10月13日

 * File: common.php

 * Version: 1.0

 */

class Class_Common extends FLEA_Controller_Action {
	var $_user;
	var $_company;
	var $_costdate;
	
	/**

	 * Enter description here...

	 *

	 */
	
	function __construct() {
		$this->_user = & get_singleton ( "Model_User" );
	}
	
	/**

	 * Open, parse, and return the file content.

	 * @author libin 2008-09-13

	 * @param string string the php file name

	 *

	 * @return string

	 */
	
	function include_fetch($file, $var = array()) {
		extract ( $var ); // Extract the vars to local namespace
		

		ob_start (); // Start output buffering
		

		include ($file); // Include the file
		

		$contents = ob_get_contents (); // Get the contents of the buffer
		

		ob_end_clean (); // End buffering and discard
		

		return $contents; // Return the contents
	

	}
	
	/**

	 *调用包含函数

	 * @author libin 2008-09-13

	 * @param unknown_type $function

	 * @param unknown_type $params

	 * @return unknown

	 */
	
	function include_fetch_function($function, $params = array()) {
		
		ob_start ();
		
		call_user_func_array ( $function, $params );
		
		$contents = ob_get_contents ();
		
		ob_end_clean ();
		
		return $contents;
	
	}
	
	/**

	 * 显示模板页面

	 * @author libin 2008-09-13

	 * @param unknown_type $parm

	 */
	
	function show($parm = array()) {
		$smarty = & $this->_getView ();
		foreach ( $parm as $key => $value ) {
			$smarty->assign ( $key, $value );
		}
		if (@$parm ['title'] == "") {
			$parm ['title'] = DEFAUT_TITLE;
		
		}
		
		$smarty->register_modifier ( "substr", array ("Class_Common", "m_substr" ) );
		$smarty->register_modifier ( "formatdate", array ("Class_Common", "formatdate" ) );
		$smarty->register_modifier ( "formatmoney", array ("Class_Common", "formatmoney" ) );
		
		if (isset ( $_SESSION ['loginuserid'] ) && $_SESSION ['loginuserid'] != "") {
			$loginuserinfo = $this->_user->findByField ( "id", $_SESSION ['loginuserid'] );
			$smarty->assign ( 'loginuserinfo', @$loginuserinfo );
			$smarty->assign ( 'loginuserid', @$_SESSION ['loginuserid'] );
		} else {
			$loginuserinfo = array ();
		}
		$smarty->display ( 'main.tpl' );
	}
	
	function article($parm = array()){
		$smarty = & $this->_getView ();
		foreach ( $parm as $key => $value ) {
			$smarty->assign ( $key, $value );
		}
		$smarty->display ( 'article.tpl' );
	}
	
	/**

	 * 后台管理

	 *

	 * @param unknown_type $parm

	 * @param unknown_type $admin_flag

	 */
	
	function manage($parm = array(), $admin_flag = "") {
		$smarty = & $this->_getView ();
		foreach ( $parm as $key => $value ) {
			$smarty->assign ( $key, $value );
		}
		if (@$parm ['title'] == "") {
			$parm ['title'] = DEFAUT_TITLE;
		
		}
		
		$smarty->register_modifier ( "substr", array ("Class_Common", "m_substr" ) );
		$smarty->register_modifier ( "formatdate", array ("Class_Common", "formatdate" ) );
		$smarty->register_modifier ( "formatmoney", array ("Class_Common", "formatmoney" ) );
		
		if (isset ( $_SESSION ['loginuserid'] ) && $_SESSION ['loginuserid'] != "") {
			$loginuserinfo = $this->_user->findByField ( "id", $_SESSION ['loginuserid'] );
		} else {
			$loginuserinfo = array ();
		}
		
		$smarty->assign ( 'loginuserinfo', @$loginuserinfo );
		$smarty->assign ( 'loginuserid', @$_SESSION ['loginuserid'] );
		$smarty->display ( 'admin/main.tpl' );
	}
	
	/**

	 * 不带模板的页面显示

	 * @author libin 2008-09-13

	 * @param unknown_type $page

	 * @param unknown_type $parm

	 */
	
	function display($page, $parm = array()) {
		$smarty = & $this->_getView ();
		foreach ( $parm as $key => $value ) {
			$smarty->assign ( $key, $value );
		}
		$smarty->register_modifier ( "substr", array ("Com_Common", "m_substr" ) );
		
		$smarty->display ( $page );
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $date
	 */
	function formatdate($date) {
		$date = str_replace ( "-", "/", $date );
		return $date;
	}
	/**
	 * 
	 * 格式化金额
	 * @param unknown_type $date
	 */
	function formatmoney($date) {
		$date = number_format ( $date );
		return $date;
	}
	
	/**

	 * word cut

	 * @author libin 2008-09-13

	 * @param unknown_type $word

	 * @param unknown_type $num

	 * @return unknown

	 */
	
	function word_explode($word, $num) {
		
		$str = wordwrap ( $word, $num, "|", 1 );
		
		$str = explode ( "|", $str );
		
		$str = $str [0];
		
		return $str;
	
	}
	
	/**

	 *字截取

	 *

	 * @param unknown_type $word

	 * @param unknown_type $startw

	 * @param unknown_type $length

	 * @return unknown

	 */
	
	function m_substr($word, $start, $length, $more = "") {
		$word = htmlspecialchars_decode ( $word );
		$word = str_replace ( "&acute;", "'", $word );
		$str = mb_substr ( $word, $start, $length, "UTF-8" );
		
		if (strlen ( $word ) > strlen ( $str )) {
			$str = $str . $more;
		}
		
		return $str;
	
	}
	
	/**
	 * 
	 * 检查用户角色信息
	 * @param unknown_type $userid
	 * @param unknown_type $role
	 */
	function checkUser($userid, $role = "") {
		$userinfo = $this->_user->findByField ( "id", $userid );
		if (! is_array ( $role )) {
			$role = explode ( ",", $role );
		}
		$flag = false;
		if (count ( $userinfo ) > 0 && is_array ( $userinfo )) {
			foreach ( $role as $v ) {
				if ($v == $userinfo ['role']) {
					$flag = true;
				}
			}
		}
		if ($flag) {
			return true;
		} else {
			$url = url ( "Default", "Login" );
			redirect ( $url );
		}
	}
	
	/**
	 * 
	 * 过滤参数
	 * @return undefine
	 * @author libin
	 * @property created at 2012-10-29
	 * @property updated at 2012-10-29
	 * @example  
	 */
	function filter($value) {
		if(is_array($value)){
			foreach ($value as $k=>$v){
				if(is_array($v)){
					foreach ($v as $kk=>$vv){
						$v[$kk]=htmlspecialchars ( $vv);
					}
					$value[$k]=$v;
				}else{
					$value[$k]=htmlspecialchars ( $v);
				}
			}
		}else{
			$value = htmlspecialchars ( $value);
		}
		return $value;
	}
	
	//获取地址
	function getAddressFromBaidu($lng,$lat){
		$add_json=file_get_contents("http://api.map.baidu.com/geocoder/v2/?callbakc=renderReverse&location=".$lat.",".$lng."&output=json&ak=".BAIDU_AK);
		$add=json_decode($add_json);
		if($add->status==0){
			return $add->result->addressComponent->city . $add->result->addressComponent->district;//当前用户位置 formatted_address 全地址
		}
	}
        //获取cityCode
	function getCityCodeFromBaidu($lng,$lat){
		$add_json=file_get_contents("http://api.map.baidu.com/geocoder/v2/?callbakc=renderReverse&location=".$lat.",".$lng."&output=json&ak=".BAIDU_AK);
		$add=json_decode($add_json);
		if($add->status==0){
			return $add->result->cityCode;//城市编码
		}
	}
	//获取经纬度
	function getLngFromBaidu($address){
		$addr=array('lng'=>0,'lat'=>0);
		//获取经纬度
		$loc_json=file_get_contents("http://api.map.baidu.com/geocoder/v2/?address=".$address."&output=json&ak=".BAIDU_AK);
		$loc=json_decode($loc_json);
		if($loc->status==0){
			$addr['lng']=$loc->result->location->lng;
			$addr['lat']=$loc->result->location->lat;
		}
		return $addr;
	}
        
        //获取汉字首字母
        function getFirstCharter($str){
            if(empty($str)){return '';}
            $fchar=ord($str{0});
            if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
            $s1=iconv('UTF-8','gb2312',$str);
            $s2=iconv('gb2312','UTF-8',$s1);
            $s=$s2==$str?$s1:$str;
            $asc=ord($s{0})*256+ord($s{1})-65536;
            if($asc>=-20319&&$asc<=-20284) return 'A';
            if($asc>=-20283&&$asc<=-19776) return 'B';
            if($asc>=-19775&&$asc<=-19219) return 'C';
            if($asc>=-19218&&$asc<=-18711) return 'D';
            if($asc>=-18710&&$asc<=-18527) return 'E';
            if($asc>=-18526&&$asc<=-18240) return 'F';
            if($asc>=-18239&&$asc<=-17923) return 'G';
            if($asc>=-17922&&$asc<=-17418) return 'H';
            if($asc>=-17417&&$asc<=-16475) return 'J';
            if($asc>=-16474&&$asc<=-16213) return 'K';
            if($asc>=-16212&&$asc<=-15641) return 'L';
            if($asc>=-15640&&$asc<=-15166) return 'M';
            if($asc>=-15165&&$asc<=-14923) return 'N';
            if($asc>=-14922&&$asc<=-14915) return 'O';
            if($asc>=-14914&&$asc<=-14631) return 'P';
            if($asc>=-14630&&$asc<=-14150) return 'Q';
            if($asc>=-14149&&$asc<=-14091) return 'R';
            if($asc>=-14090&&$asc<=-13319) return 'S';
            if($asc>=-13318&&$asc<=-12839) return 'T';
            if($asc>=-12838&&$asc<=-12557) return 'W';
            if($asc>=-12556&&$asc<=-11848) return 'X';
            if($asc>=-11847&&$asc<=-11056) return 'Y';
            if($asc>=-11055&&$asc<=-10247) return 'Z';
            //return $str{0};
            return '';
        }
	
	
}

?>