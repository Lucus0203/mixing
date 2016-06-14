<?php
require_once 'Image.php';
// ====================================================

// 使用范例：

// $Picture=new Service_Picture();

// $Picture->upLoad($fileName);
//单张upload()
//array('status'=>'1','file_path'=>path)
//多张uploadFiles()
//array('status'=>'1','filepaths'=>$filepaths)
// ====================================================
class UpLoad {
	var $dir; // 图片存放路经
	var $postFileName; // 页面提交的FILE名称
	var $prefixName; // 名称前缀
	var $uptypes;
	var $max_file_size;
	var $SWidth;
	var $SHeight;
	var $LWidth;
	var $LHeight;
	
	/**
	 * 初始化参数
	 */
	function __construct() {
		$this->dir = "upload/";
		$this->prefixName = "";
		$this->max_file_size=10000000;//10M
		$this->uptypes = array (
				'image/jpg',
				'image/jpeg',
				'image/png',
				'image/pjpeg',
				'image/gif',
				'image/bmp',
				'image/x-png',
                                'audio/x-amr',
                                'audio/amr'
		);
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $photoDir        	
	 */
	function setDir($dir) {
		$this->dir = $dir;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $prefixName        	
	 */
	function setPrefixName($prefixName) {
		$this->prefixName = $prefixName;
	}

	function setSWidth($width){
	
		$this->SWidth=$width;
	
	}
	
	function setSHeight($height){
	
		$this->SHeight=$height;
	
	}
	
	function setLWidth($width){
	
		$this->LWidth=$width;
	
	}
	
	function setLHeight($height){
	
		$this->LHeight=$height;
	
	}
	/**
	 * 上传单张图
	 */
	function upLoad($fileName) {
		$this->postFileName = $fileName;
		if (!is_uploaded_file ( $_FILES [$this->postFileName] [tmp_name] )){ // 是否存在文件
			return array('status'=>'0','errMsg'=>"图片不存在!");
		}
		$file = $_FILES [$this->postFileName];
		if ($this->max_file_size < $file ["size"]){ // 检查文件大小
			return array('status'=>'2','errMsg'=>"文件太大!");
		}
		if (!empty($this->uptypes) && !in_array ( $file ["type"], $this->uptypes )){ // 检查文件类型
			return array('status'=>'3','errMsg'=>"文件类型不符!" . $file ["type"]);
		}
		if (! file_exists ( $this->dir )) {
			mkdir ( $this->dir, 0777 );
		}
		$filename = $file ["tmp_name"];
		$image_size = getimagesize ( $filename );
		$pinfo = pathinfo ( $file ["name"] );
		$ftype = $pinfo ['extension'];
		$destination = $this->dir . $this->prefixName.time () . "." . $ftype;
		if (! move_uploaded_file ( $filename, $destination )) {
			return array('status'=>'4','errMsg'=>"上传文件出错");
		}
		return array('status'=>'1','file_path'=>$destination);
	}
	
	/**
	 * 上传多张图
	 */
	function uploadFiles($fileName){
		$this->postFileName = $fileName;
		$file = $_FILES [$this->postFileName];
		$sizes=$file ["size"];
		$types=$file ["type"];
		$tmpnames=$file ["tmp_name"];
		$filenames=$file ["name"];
		$flag=true;
		$res=array();
		$filepaths=array();
		for($i=0;$i<count($filenames);$i++){
                        if(empty($filenames[$i])){
                            continue;
                        }
			if ($this->max_file_size < $sizes[$i]){ // 检查文件大小
				$res = array('status'=>'2','errMsg'=>"文件太大!");
				$flag=false;
			}
			if (! in_array ( $types[$i], $this->uptypes )){ // 检查文件类型
				$res = array('status'=>'3','errMsg'=>"文件类型不符!" . $file ["type"]);
				$flag=false;
			}
			if (! file_exists ( $this->dir )) {
				mkdir ( $this->dir, 0777 );
			}
			$filename = $tmpnames[$i];
			$image_size = getimagesize ( $filename );
			$pinfo = pathinfo ( $filenames[$i] );
			$ftype = $pinfo ['extension'];
			$destination = $this->dir . $this->prefixName.$i.time () . "." . $ftype;
			if (! move_uploaded_file ( $filename, $destination )) {
				$res = array('status'=>'4','errMsg'=>"上传文件出错");
				$flag=false;
			}else{
				$filepaths[]=$destination;
			}
		}
		if($i==0){
			$res = array('status'=>'0','errMsg'=>"没有要上传的图片");
		}
		if(!$flag){//上传失败删除之前的图片
			foreach ($filepaths as $path){
				unlink($path);
			}
		}
		$res = array('status'=>'1','filepaths'=>$filepaths);
		return $res;
	}
	
	/**
	 * 上传缩略图和原图
	 */
	function upLoadImg($fileName) {
		$this->postFileName = $fileName;
		if (!is_uploaded_file ( $_FILES [$this->postFileName] [tmp_name] )){ // 是否存在文件
			return array('status'=>'0','errMsg'=>"图片不存在!");
		}
		$file = $_FILES [$this->postFileName];
		if ($this->max_file_size < $file ["size"]){ // 检查文件大小
			return array('status'=>'2','errMsg'=>"文件太大!");
		}
		if (!empty($this->uptypes) && !in_array ( $file ["type"], $this->uptypes )){ // 检查文件类型
			return array('status'=>'3','errMsg'=>"文件类型不符!" . $file ["type"]);
		}
		if (! file_exists ( $this->dir )) {
			mkdir ( $this->dir, 0777 );
		}
		$filename = $file ["tmp_name"];
		list($o_w, $o_h) = getimagesize ( $filename );
		$s_height=$this->SWidth / $o_w * $o_h ;
		$l_height=$this->LHeight / $o_w * $o_h ;
		if($o_h/$o_w > 1.7786){ //1334/750
			$s_height=$this->SWidth * 1.7786 ;
			$l_height=$this->LHeight * 1.7786 ;
		}elseif($o_h/$o_w < 0.625){ //320/200
			$s_height=$this->SWidth * 0.625 ;
			$l_height=$this->LHeight * 0.625 ;
		}
		$this->setSHeight($s_height);
		$this->setLHeight($l_height);
		$pinfo = pathinfo ( $file ["name"] );
		$ftype = $pinfo ['extension'];
		$s = $this->dir . $this->prefixName.time () . "_s." . $ftype;
		$b = $this->dir . $this->prefixName.time () . "_b." . $ftype;
		
		
		//保存大图
		$image_larg=& FLEA_Helper_Image::createFromFile($filename, $ftype);
		$image_larg->crop($this->LWidth, $this->LHeight, true, true);
		
		$image_larg->saveAsJpeg($b);
		
		$image_larg->destory();
		//保存小图
		$image_thum=& FLEA_Helper_Image::createFromFile($filename, $ftype);
		$image_thum->crop($this->SWidth, $this->SHeight, true, true);
		
		$image_thum->saveAsJpeg($s);
		
		$image_thum->destory();
		
		// 保存原始相片
		//$upload_flag=move_uploaded_file ( $filename, $b );
		//echo $string;
		
		$res=array();
		//if($upload_flag){
			$res['status']='1';
			$res['s_path']=$s;
			$res['b_path']=$b;
// 		}else {
// 			$res['status']='4';
// 			$res['errMsg']='上传文件出错';
// 		}
		return $res;
	}
	
}
?>