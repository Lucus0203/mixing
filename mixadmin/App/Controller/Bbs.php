<?php
class Controller_Bbs extends FLEA_Controller_Action {
	/**
	 * 
	 * Enter description here ...
	 * @var Class_Common
	 */
	var $_common;
	var $_user;
	var $_shop;
	var $_shop_bbs;
	var $_public_bbs;
	var $_user_event;
	var $_user_event_bbs;
	var $_admin;
	var $_adminid;
	
	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );

		$this->_user = get_singleton ( "Model_User" );
		$this->_shop = get_singleton ( "Model_Shop" );
		$this->_shop_bbs = get_singleton ( "Model_ShopBbs" );
		$this->_public_bbs = get_singleton ( "Model_PublicBbs" );
		$this->_user_event = get_singleton ( "Model_UserEvent" );
		$this->_user_event_bbs = get_singleton ( "Model_UserEventBbs" );
		$this->_adminid = isset ( $_SESSION ['loginuserid'] ) ? $_SESSION ['loginuserid'] : "";
		if(empty($_SESSION ['loginuserid'])){
			$url=url("Default","Login");
			redirect($url);
		}
	}
	
	/**
	 * 店铺留言
	 *
	 */
	function actionShop() {
		$config = FLEA::getAppInf ( 'dbDSN' );
		$prefix = $config ['prefix'];
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
		$keyword = isset ( $_GET ['keyword'] ) ? $this->_common->filter($_GET ['keyword']) : '';
		$shopid = isset ( $_GET ['shopid'] ) ? $this->_common->filter($_GET ['shopid']) : '';
		if(empty($shopid)){
			$url=url('Shop','Index');
			redirect($url);
			return ;
		}

		$pageparm = array ('shopid'=>$shopid);
		$conditions = ' bbs.shop_id = '.$shopid;
		if(!empty($keyword)){
			$conditions.=" and content like '%".addslashes($keyword)."%' ";
			$pageparm['keyword']=$keyword;
		}
		$sql="select bbs.id,u.user_name,p.path,bbs.content,bbs.allow,bbs.created from ".$prefix."shop_bbs bbs left join ".$prefix."user u on bbs.user_id=u.id
			 left join ".$prefix."user_photo p on u.head_photo_id = p.id where ".$conditions;
		$total=$this->_shop_bbs->findBySql("select count(*) as num from ($sql) s");
		$total=@$total[0]['num'];
		
		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "Bbs", "Shop" );
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;
		
		$list=$this->_shop_bbs->findBySql($sql." order by bbs.id desc limit $start,$page_size");
		$shop=$this->_shop->findByField('id',$shopid);
		
		$this->_common->show ( array ('main' => 'bbs/bbs_shop_list.tpl','list'=>$list,'page'=>$page,'keyword'=>$keyword,'shop'=>$shop,'inputAct'=>'Shop') );
	}
	
	/**
	 * 活动留言
	 *
	 */
	function actionUserEvent() {
		$config = FLEA::getAppInf ( 'dbDSN' );
		$prefix = $config ['prefix'];
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
		$keyword = isset ( $_GET ['keyword'] ) ? $this->_common->filter($_GET ['keyword']) : '';
		$eventid = isset ( $_GET ['eventid'] ) ? $this->_common->filter($_GET ['eventid']) : '';
		if(empty($eventid)){
			$url=url('Bbs','UserEvent');
			redirect($url);
			return ;
		}
	
		$pageparm = array ('eventid'=>$eventid);
		$conditions = ' bbs.user_event_id = '.$eventid;
		if(!empty($keyword)){
			$conditions.=" and content like '%".addslashes($keyword)."%' ";
			$pageparm['keyword']=$keyword;
		}
		$sql="select bbs.id,u.user_name,p.path,bbs.content,bbs.allow,bbs.created from ".$prefix."userevent_bbs bbs left join ".$prefix."user u on bbs.user_id=u.id
			 left join ".$prefix."user_photo p on u.head_photo_id = p.id where ".$conditions;
		$total=$this->_shop_bbs->findBySql("select count(*) as num from ($sql) s");
		$total=@$total[0]['num'];
	
		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "Bbs", "UserEvent" );
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;
	
		$list=$this->_user_event_bbs->findBySql($sql." order by bbs.id desc limit $start,$page_size");
		$event=$this->_user_event->findByField('id',$eventid);
	
		$this->_common->show ( array ('main' => 'bbs/bbs_event_list.tpl','list'=>$list,'page'=>$page,'keyword'=>$keyword,'event'=>$event,'inputAct'=>'Shop') );
	}
	
	function actionDel(){//删除
		$id=$this->_common->filter($_GET['id']);
		$type=$this->_common->filter($_GET['type']);
		if($type=='shop'){
			$this->_shop_bbs->removeByPkv($id);
		}elseif ($type=='userEvent'){
			$this->_user_event_bbs->removeByPkv($id);
		}
		redirect($_SERVER['HTTP_REFERER']);
	}
	function actionAllow(){ //审核通过
		$id=$this->_common->filter($_GET['id']);
		$type=$this->_common->filter($_GET['type']);
		$bbs=array('id'=>$id,'allow'=>1);
		if($type=='shop'){
			$this->_shop_bbs->update($bbs);
		}elseif ($type=='userEvent'){
			$this->_user_event_bbs->update($bbs);
		}
		redirect($_SERVER['HTTP_REFERER']);
	}
	function actionDeAllow(){//不通过
		$id=$this->_common->filter($_GET['id']);
		$type=$this->_common->filter($_GET['type']);
		$bbs=array('id'=>$id,'allow'=>2);
		if($type=='shop'){
			$this->_shop_bbs->update($bbs);
		}elseif ($type=='userEvent'){
			$this->_user_event_bbs->update($bbs);
		}
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