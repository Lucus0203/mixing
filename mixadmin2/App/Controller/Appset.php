<?php
class Controller_Appset extends FLEA_Controller_Action {
	/**
	 * 
	 * Enter description here ...
	 * @var Class_Common
	 */
	var $_common;
        var $_link;
	var $_admin;
	var $_adminid;
        var $_sms;
	
	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );
		$this->_sms = get_singleton ( "Class_Sms" );
                $this->_link = get_singleton( "Model_Link" );
		$this->_adminid = isset ( $_SESSION ['loginuserid'] ) ? $_SESSION ['loginuserid'] : "";
		if(empty($_SESSION ['loginuserid'])){
			$url=url("Default","Login");
			redirect($url);
		}
	}
	
	/**
	 * 消息
	 *消息类型eventInvitation(搭伴邀请)invitation(普通邀请函)receiver(领取咖啡通知)depositer(被领取咖啡通知)mixing(官方通知,链接方式)
	 */
        
	function actionLink() {
		$config = FLEA::getAppInf ( 'dbDSN' );
		$prefix = $config ['prefix'];
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
		$sql="select * from ".$prefix."link link where 1=1 ";
		$total=$this->_link->findBySql("select count(*) as num from ($sql) s");
		$total=@$total[0]['num'];
		
		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "Appset", "Link" );
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;
		$list=$this->_link->findBySql($sql." order by id desc limit $start,$page_size");
		
		$this->_common->show ( array ('main' => 'appset/link.tpl','list'=>$list,'page'=>$page) );
	}
        
	function actionEditLink() {
                if(!empty($_POST['act'])){
                    $data=array('title'=>$_POST['title'],'link'=>$_POST['link'],'status'=>$_POST['status']);
                    $Upload= & get_singleton ( "Service_UpLoad" );
                    $folder='../v2/upload/link/';
                    if (! file_exists ( $folder )) {
                            mkdir ( $folder, 0777 );
                    }
                    $Upload->setDir($folder.date("Ymd")."/");
                    $Upload->setReadDir(APP_SITE.'upload/link/'.date("Ymd")."/");
                    $img=$Upload->upload('img');
                    if($img['status']==1){
                        $data['img']=$img['file_path'];
                    }elseif($img['status']!=0){
                        echo "<script>alert('图片上传失败')</script>";
                    }
                    if(!empty($_POST['id'])){
                        $data['id']=$_POST['id'];
                        $this->_link->update($data);
                    }else{
                        $this->_link->create($data);
                    }
                    $url=url("Appset","Link");
                    redirect($url);
                }
                $linkid=$_GET['linkid'];
		$data=$this->_link->findByField('id',$linkid);
                $this->_common->show ( array ('main' => 'appset/link_edit.tpl','data'=>$data) );
	}
        function actionDelLink(){//删除
		$id=$this->_common->filter($_GET['id']);
		$this->_link->removeByPkv($id);
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	
}

?>