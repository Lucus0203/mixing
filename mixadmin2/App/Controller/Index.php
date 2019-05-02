<?php
class Controller_Index extends FLEA_Controller_Action {
	/**
	 *
	 * Enter description here ...
	 * @var Class_Common
	 */
	var $_common;
	var $_user;
	var $_banner;
	var $_admin;
	var $_adminid;
	var $_tags;

	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );

		$this->_user = get_singleton ( "Model_User" );
		$this->_banner = get_singleton ( "Model_Banner" );
		$this->_adminid = isset ( $_SESSION ['loginuserid'] ) ? $_SESSION ['loginuserid'] : "";
		if(empty($_SESSION ['loginuserid'])){
			$url=url("Default","Login");
			redirect($url);
		}
	}

	/**
	 * banner管理
	 *
	 */
	function actionBanner() {
		$act=isset ( $_POST ['act'] ) ? $_POST ['act'] : '';
		if($act=='edit'){
			$data=$_POST;
			$Upload=$this->getUploadObj('banner');
			$banner=$this->_banner->removeAll();
			//创建新banner图
			if(isset($data['oldbanner'] )){
				foreach ($data['oldbanner'] as $b){
					$pp=array('img'=>$b);
					$this->_banner->create($pp);
				}
			}
			$banners=$Upload->uploadFiles('banners');
			if($banners['status']==1){
				foreach ($banners['filepaths'] as $p){
					$pp=array('img'=>$p,'created'=>date("Y-m-d H:i:s"));
					$this->_banner->create($pp);
				}
			}
		}
		$banner=$this->_banner->findAll();
		$this->_common->show ( array ('main' => 'index/banner.tpl','banner'=>$banner) );
	}
	
	function actionDelBanner(){
		$pid=isset ( $_GET ['pid'] ) ? $_GET ['pid'] : '';
		$pid=$this->_common->filter($pid);
		$banner=$this->_banner->findByField('id',$pid);
		$this->delAppImg($banner['img']);
		echo $this->_banner->removeByPkv($pid);
	}
	
	//图片处理
	function delAppImg($path){
		if(!empty($path)){
			$file=str_replace(APP_SITE, '../', $path);
			if(file_exists($file))
				unlink($file);
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
