<?php
class Controller_PublicEvent extends FLEA_Controller_Action {
	/**
	 * 
	 * Enter description here ...
	 * @var Class_Common
	 */
	var $_common;
	var $_user;
	var $_public_event;
	var $_public_photo;
	var $_admin;
	var $_adminid;
	
	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );

		$this->_user = get_singleton ( "Model_User" );
		$this->_public_event = get_singleton ( "Model_PublicEvent" );
		$this->_public_photo = get_singleton ( "Model_PublicPhoto" );
		$this->_adminid = isset ( $_SESSION ['loginuserid'] ) ? $_SESSION ['loginuserid'] : "";
		if(empty($_SESSION ['loginuserid'])){
			$url=url("Default","Login");
			redirect($url);
		}
	}
	
	/**
	 * 官方活动
	 *
	 */
	function actionIndex() {
		$pageparm = array ();
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
		$title = isset ( $_GET ['title'] ) ? $this->_common->filter($_GET ['title']) : '';
		
		$conditions=array('isdelete'=>'0');
		if(!empty($title)){
			$conditions[]=" (INSTR(title,'".addslashes($title)."') or INSTR(address,'".addslashes($title)."')  or INSTR(datetime,'".addslashes($title)."') ) ";
			$pageparm['title']=$title;
		}
		
		$total=$this->_public_event->findCount($conditions);
		
		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "PublicEvent", "Index" );
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;
		
		$list=$this->_public_event->findAll($conditions,"num asc,datetime desc,ispublic asc,id desc limit $start,$page_size");
		
		$this->_common->show ( array ('main' => 'publicEvent/public_list.tpl','list'=>$list,'page'=>$page,'title'=>$title) );
	}
	
	function actionAdd(){
		$act=isset ( $_POST ['act'] ) ? $_POST ['act'] : '';
		if($act=='add'){
			$data=$_POST;
			$Upload=$this->getUploadObj('publicEvent');
			$img=$Upload->upload('img');
			if($img['status']==1){
				$data['img']=$img['file_path'];
			}
			//判断经纬度
			if(empty($data['lng'])||empty($data['lat'])){
				$lng=$this->_common->getLngFromBaidu($data['address']);
				$data['lng']=$lng['lng'];
				$data['lat']=$lng['lat'];
			}
			$id=$this->_public_event->create($data);
			$Upload=$this->getUploadObj('publicPhoto');
			$files=$Upload->uploadFiles('photos');
			if($files['status']==1){
				foreach ($files['filepaths'] as $p){
					$pp=array('public_event_id'=>$id,'img'=>$p,'created'=>date("Y-m-d H:i:s"));
					$this->_public_photo->create($pp);
				}
			}
			$url=url('PublicEvent','Index');
			redirect($url);
		}
		$this->_common->show ( array ('main' => 'publicEvent/public_add.tpl') );
	}
	
	function actionEdit(){
		$id=isset ( $_GET ['id'] ) ? $_GET ['id'] : '';
		$act=isset ( $_POST ['act'] ) ? $_POST ['act'] : '';
		$msg='';
		if($act=='edit'){
			$data=$_POST;
			$Upload=$this->getUploadObj('publicEvent');
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
			$this->_public_event->update($data);
			$this->_public_photo->removeByConditions(array('public_event_id'=>$id));
			//创建新的活动海报
			if(isset($data['public_photos'] )){
				foreach ($data['public_photos'] as $pub){
					$pp=array('public_event_id'=>$id,'img'=>$pub,'created'=>date("Y-m-d H:i:s"));
					$this->_public_photo->create($pp);
				}
			}
			$Upload=$this->getUploadObj('publicPhoto');
			$files=$Upload->uploadFiles('photos');
			if($files['status']==1){
				foreach ($files['filepaths'] as $p){
					$pp=array('public_event_id'=>$id,'img'=>$p,'created'=>date("Y-m-d H:i:s"));
					$this->_public_photo->create($pp);
				}
			}
			$msg="更新成功!";
		}
		$data=$this->_public_event->findByField('id',$id);
		$photo=$this->_public_photo->findAll(array('public_event_id'=>$id));
		
		$this->_common->show ( array ('main' => 'publicEvent/public_edit.tpl','data'=>$data,'photo'=>$photo,'msg'=>$msg) );
	}
	
	function actionDelPhoto(){//删除海报
		$pid=isset ( $_GET ['pid'] ) ? $_GET ['pid'] : '';
		$pid=$this->_common->filter($pid);
		$pub_photo=$this->_public_photo->findByField('id',$pid);
		$this->delAppImg($pub_photo['img']);
		echo $this->_public_photo->removeByPkv($pid);
		
	}

	function actionDel(){//删除
		$id=$this->_common->filter($_GET['id']);
		$eve=array('id'=>$id,'isdelete'=>1);
		$this->_public_event->update($eve);
		redirect($_SERVER['HTTP_REFERER']);
	}
	function actionPublic(){ //发布
		$id=$this->_common->filter($_GET['id']);
		$eve=array('id'=>$id,'ispublic'=>1);
		$this->_public_event->update($eve);
		redirect($_SERVER['HTTP_REFERER']);
	}
	function actionDePublic(){//不发布
		$id=$this->_common->filter($_GET['id']);
		$eve=array('id'=>$id,'ispublic'=>2);
		$this->_public_event->update($eve);
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	function actionOrder(){//排序
		$id=$this->_common->filter($_POST['ids']);
		$num=$this->_common->filter($_POST['nums']);
		$nums=split(",", $num);
		$ids=split(",", $id);
		foreach ($nums as $k=>$n){
			if($n!=''){
				$eve=array('id'=>$ids[$k],'num'=>$n);
				$this->_public_event->update($eve);
			}
		}
		echo $_SERVER['HTTP_REFERER'];
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
        
        function actionUploadImage(){
                $extensions = array("jpg","bmp","gif","png");  
                $uploadFilename = $_FILES['upload']['name'];  
                $extension = pathInfo($uploadFilename,PATHINFO_EXTENSION);  
                echo $extension;
                if(in_array($extension,$extensions)){
                        $Upload=$this->getUploadObj('publicPhoto');
                        $img=$Upload->upload('upload');
                        $callback = $_REQUEST["CKEditorFuncNum"];
                        echo $callback;
                        if($img['status']==1){
                                echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($callback,'".$img['file_path']."','');</script>";
                        }else{
                                echo "<font color=\"red\"size=\"2\">*文件上传失败</font>";  
                        }
                }else{
                        echo "<font color=\"red\"size=\"2\">*文件格式不正确（必须为.jpg/.gif/.bmp/.png文件）</font>";  
                }
        }
	
	
}

?>