<?php
class Controller_User extends FLEA_Controller_Action {
	/**
	 *
	 * Enter description here ...
	 * @var Class_Common
	 */
	var $_common;
	var $_user;
	var $_admin;
	var $_adminid;

	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );

		$this->_user = get_singleton ( "Model_User" );
		$this->_admin = get_singleton ( "Model_Admin" );
		$this->_adminid = isset ( $_SESSION ['loginuserid'] ) ? $_SESSION ['loginuserid'] : "";
		if(empty($_SESSION ['loginuserid'])){
			$url=url("Default","Login");
			redirect($url);
		}
	}

	/**
	 * 用户列表
	 *
	 */
	function actionIndex() {
		$pageparm = array ();
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
		$keyword = isset ( $_GET ['keyword'] ) ? $this->_common->filter($_GET ['keyword']) : '';

		$conditions=array();
		if(!empty($keyword)){
			$conditions[]=" INSTR(user_name,'".addslashes($keyword)."') or INSTR(nick_name,'".addslashes($keyword)."')  or mobile = '".addslashes($keyword)."' ";
			$pageparm['keyword']=$keyword;
		}

		$total=$this->_user->findCount($conditions);

		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "User", "Index" );
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;

		$list=$this->_user->findAll($conditions,"id desc limit $start,$page_size");
		foreach ($list as $k=>$v){
			$list[$k]['address']=$this->_common->getAddressFromBaidu($v['lng'],$v['lat']);
		}

		$this->_common->show ( array ('main' => 'user/user_list.tpl','list'=>$list,'page'=>$page) );
	}
	
	/**
	 * 
	 * 用户编辑
	 */
	function actionEdit(){

		$id=isset ( $_GET ['id'] ) ? $_GET ['id'] : '';
		$act=isset ( $_POST ['act'] ) ? $_POST ['act'] : '';
		$msg='';
		if($act=='edit'){
			$data=$_POST;
			if(!empty($data['user_password'])){
				$data['user_password']=md5($data['user_password']);
			}else{
				$data['user_password']=$data['old_password'];
			}
			$this->_user->update($data);
			$msg="更新成功!";
		}
		$data=$this->_user->findByField('id',$id);
		
		$this->_common->show ( array ('main' => 'user/user_edit.tpl','data'=>$data,'msg'=>$msg) );
	}
	
	function actionFeedback(){
		$config = FLEA::getAppInf ( 'dbDSN' );
		$prefix = $config ['prefix'];
		$pageparm = array ();
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
		$keyword = isset ( $_GET ['keyword'] ) ? $this->_common->filter($_GET ['keyword']) : '';

		$sql = "select f.*,u.user_name,u.nick_name,u.sex,u.mobile,u.email from ".$prefix."feedback f left join ".$prefix."user u on f.user_id = u.id where 1=1 ";
		
		if(!empty($keyword)){
			$sql.="and user_name like '%$keyword%' or nick_name like '%$keyword%' or content like '%$keyword%' ";
			$pageparm['keyword']=$keyword;
		}
		$countsql = "select count(*) as num from ($sql) m";
		$totaldata = $this->_user->findBySql ( $countsql );
		$total = $totaldata [0] ['num'];

		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "User", "Feedback" );
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;
		
		$sql .= " order by f.id desc limit $start,$page_size";
		$list = $this->_user->findBySql ( $sql );

		$this->_common->show ( array ('main' => 'user/user_feedback.tpl','list'=>$list,'page'=>$page,'keyword'=>$keyword) );
		
	}
	
}

