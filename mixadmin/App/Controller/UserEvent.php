<?php
class Controller_UserEvent extends FLEA_Controller_Action {
	/**
	 * 
	 * Enter description here ...
	 * @var Class_Common
	 */
	var $_common;
	var $_user;
	var $_user_event;
	var $_admin;
	var $_adminid;
	
	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );

		$this->_user = get_singleton ( "Model_User" );
		$this->_user_event = get_singleton ( "Model_UserEvent" );
		$this->_adminid = isset ( $_SESSION ['loginuserid'] ) ? $_SESSION ['loginuserid'] : "";
		if(empty($_SESSION ['loginuserid'])){
			$url=url("Default","Login");
			redirect($url);
		}
	}
	
	/**
	 * 用户活动
	 *
	 */
	function actionIndex() {
		$pageparm = array ('status'=>1);
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
		$title = isset ( $_GET ['title'] ) ? $this->_common->filter($_GET ['title']) : '';
		
		$conditions=array('status'=>1);
		if(!empty($title)){
			$conditions[]="INSTR(title,'".addslashes($title)."') or INSTR(address,'".addslashes($title)."')  ";
			$pageparm['title']=$title;
		}
		
		$total=$this->_user_event->findCount($conditions);
		
		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "UserEvent", "Index" );
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;
		
		$list=$this->_user_event->findAll($conditions,"datetime desc, id desc limit $start,$page_size");
		
		$this->_common->show ( array ('main' => 'userEvent/event_list.tpl','list'=>$list,'page'=>$page,'title'=>$title) );
	}
	
	
	function actionEdit(){
		$id=isset ( $_GET ['id'] ) ? $_GET ['id'] : '';
		$act=isset ( $_POST ['act'] ) ? $_POST ['act'] : '';
		$msg='';
		if($act=='edit'){
			$data=$_POST;
			$Upload=$this->getUploadObj('userEvent');
			$img=$Upload->upload('imgIndex');
			if($img['status']==1){
				$this->delAppImg($data['img']);
				$data['img']=$img['file_path'];
			}
			//判断经纬度
			if(empty($data['lng'])||empty($data['lat'])){
				$lng=$this->_common->getLngFromBaidu($data['address']);
				$data['lng']=$lng['lng'];
				$data['lat']=$lng['lat'];
			}
			$this->_user_event->update($data);
			$msg="更新成功!";
		}
		$data=$this->_user_event->findByField('id',$id);
		
		$this->_common->show ( array ('main' => 'userEvent/event_edit.tpl','data'=>$data,'msg'=>$msg) );
	}

	function actionDel(){//删除
		$id=$this->_common->filter($_GET['id']);
		$eve=array('id'=>$id,'status'=>2);
		$this->_user_event->update($eve);
		redirect($_SERVER['HTTP_REFERER']);
	}
	function actionAllow(){ //审核通过
		$id=$this->_common->filter($_GET['id']);
		$eve=array('id'=>$id,'allow'=>1);
		$this->_user_event->update($eve);
		redirect($_SERVER['HTTP_REFERER']);
	}
	function actionDeAllow(){//不通过
		$id=$this->_common->filter($_GET['id']);
		$eve=array('id'=>$id,'allow'=>2);
		$this->_user_event->update($eve);
		redirect($_SERVER['HTTP_REFERER']);
	}
	

	//图片处理
	function delAppImg($path){
		if(!empty($path)){
			$file=str_replace(APP_SITE, '../', $path);
			if(file_exists($file)){
				unlink($file);
			}
		}
	}
	function getUploadObj($f){
		$Upload= & get_singleton ( "Service_UpLoad" );
		$folder='../upload/'.$f.'/';
		if (! file_exists ( $folder )) {
			mkdir ( $folder, 0777 );
		}
		$Upload->setDir($folder.date("Ymd")."/");
		$Upload->setReadDir(APP_SITE.'upload/'.$f.'/'.date("Ymd")."/");
		return $Upload;
	}
	
	
}

?>