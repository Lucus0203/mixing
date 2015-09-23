<?php 
//====================================================
// 使用范例：
// $Picture=new Service_Picture();
// $Picture->upLoad($fileName);
//单张upload()
//array('status'=>'1','file_path'=>path)
//多张uploadFiles()
//array('status'=>'1','filepaths'=>$filepaths)
//====================================================
class Service_UpLoad
{
	var $dir;//图片存放路经
	var $readDir;

	var $postFileName;//页面提交的FILE名称

	var $prefixName;//名称前缀
	var $uptypes;
	var $max_file_size;

	/**
	 *初始化参数
	 *
	 */
	function __construct(){
		$this->dir="resource/upload";
		$this->prefixName ="";
		$this->max_file_size=10000000;//10M
	}
	function setDir($dir){
		$this->dir=$dir;
	}
	function setReadDir($dir){
		$this->readDir=$dir;
	}
	function setPrefixName($prefixName){
		$this->prefixName =$prefixName;
	}
	function setFileSize($size){
		$this->max_file_size =intval($size);
	}
	function setUpTypes($types){
		$this->uptypes = $types;
	}
	/**
	 * 上传
	 * @author libin at 2008-11-11
	 * @param unknown_type $fileName
	 * @return unknown
	 */
	function upLoad($fileName){
		$this->postFileName =$fileName;
		if (!is_uploaded_file ( $_FILES [$this->postFileName] ['tmp_name'] )){ // 是否存在文件
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
		$fname = $this->prefixName.time () . "." . $ftype;
		$destination = $this->dir . $fname;
// 		if (file_exists ( $destination )) {
// 			echo "同名文件已经存在了";
// 			exit ();
// 		}
		if (! move_uploaded_file ( $filename, $destination )) {
			return array('status'=>'4','errMsg'=>"上传文件出错");
		}
		$file_path=empty($this->readDir)?($this->dir . $fname) : ($this->readDir . $fname);
		return array('status'=>'1','file_path'=>$file_path);

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
			if (!is_uploaded_file ( $tmpnames[$i] )){
				continue;
			}
			if ($this->max_file_size < $sizes[$i]){ // 检查文件大小
				$res = array('status'=>'2','errMsg'=>"文件太大!");
				$flag=false;
			}
			if (!empty($this->uptypes) && !in_array ( $types[$i], $this->uptypes )){ // 检查文件类型
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
			$fname = $this->prefixName.$i.time () . "." . $ftype;
			$destination = $this->dir . $fname;
			// 		if (file_exists ( $destination )) {
			// 			echo "同名文件已经存在了";
			// 			exit ();
			// 		}
			if (! move_uploaded_file ( $filename, $destination )) {
				$res = array('status'=>'4','errMsg'=>"上传文件出错");
				$flag=false;
			}else{
				$filepaths[]=empty($this->readDir)?$this->dir . $fname:$this->readDir . $fname;
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
	
}
?>