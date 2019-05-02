<?php 

//====================================================

// 使用范例：

// $Picture=new Service_Picture();

// $Picture->upLoad($fileName);

//====================================================

class Service_Picture

{

	var $photoDir;//图片存放路经



	var $thumDir;//略缩图存放路经


	var $largeDir;//大图存放路经
	
	
	var $postFileName;//页面提交的FILE名称



	var $prefixName;//名称前缀



	var $thumWidth;//略缩图宽度



	var $thumHeight;//略缩图高度
	
	
	
	var $largeWidth;//大图宽度



	var $largeHeight;//大图高度



	/**

	 *初始化参数

	 *

	 */

	function __construct(){

		$this->photoDir="resource/upload/photo";
		
		$this->largeDir="resource/upload/large";

		$this->thumDir="resource/upload/thum";

		$this->prefixName ="";
		
		$this->largeWidth=608;
		
		$this->largeHeight=348;

		$this->thumWidth=128;

		$this->thumHeight=91;

	}

	/**

	 * Enter description here...

	 *

	 * @param unknown_type $photoDir

	 */

	function setPhotoDir($photoDir){

		$this->photoDir=$photoDir;

	}

	/**

	 * Enter description here...

	 *

	 * @param unknown_type $thumDir

	 */

	function setThumDir($thumDir){

		$this->thumDir =$thumDir;

	}
	
	/**

	 * Enter description here...

	 *

	 * @param unknown_type $largeDir

	 */

	function setLargeDir($largeDir){

		$this->largeDir=$largeDir;

	}

	/**

	 * Enter description here...

	 *

	 * @param unknown_type $prefixName

	 */

	function setPrefixName($prefixName){

		$this->prefixName =$prefixName;

	}

	/**

	 * Enter description here...

	 *

	 * @param unknown_type $width

	 */

	function setThumWidth($width){

		$this->thumWidth=$width;

	}

	/**

	 * Enter description here...

	 *

	 * @param unknown_type $height

	 */

	function setThumHeight($height){

		$this->thumHeight=$height;

	}
	
	/**

	 * Enter description here...

	 *

	 * @param unknown_type $width

	 */

	function setLargeWidth($width){

		$this->largeWidth=$width;

	}

	/**

	 * Enter description here...

	 *

	 * @param unknown_type $height

	 */

	function setLargeHeight($height){

		$this->largeHeight=$height;

	}

	/**

	 * 上传

	 * @author libin at 2008-11-11

	 * @param unknown_type $fileName

	 * @return unknown

	 */

	function upLoad($fileName){

		$res =array();

		$this->postFileName =$fileName;

		FLEA::loadHelper('uploader');

		$uploader =& new FLEA_Helper_FileUploader();

		if(!is_dir($this->photoDir)){

			mkdir ($this->photoDir, 0777);

		}
		
		if(!is_dir($this->largeDir)){

			mkdir ($this->largeDir, 0777);

		}

		if(!is_dir($this->thumDir)){

			mkdir ($this->thumDir, 0777);

		}

		if(@$_FILES[$this->postFileName]['name']==""){//没有上传图片的情况

			$res['photo_path']="";
			
			$res['large_path']="";

			$res['thum_path']="";

		}else {

			$postfile = $uploader->getFile($this->postFileName);

			$showtime=date("YmdHis");

			$photoFilename =  $this->prefixName.$showtime."_o.". $postfile->getExt();
			
			$largebFilename = $this->prefixName.$showtime.'_l.jpg';

			$thumbFilename = $this->prefixName.$showtime.'.jpg';

			// 生成缩略图（220 x 220 像素大小）

			FLEA::loadHelper('image');

			$image =& FLEA_Helper_Image::createFromFile($postfile->getTmpName(), $postfile->getExt());
			
			$image->crop($this->largeWidth, $this->largeHeight, true, true);

			$image->saveAsJpeg($this->largeDir .  "/" . $largebFilename);
			$image->destory();
			
			$image_thum=& FLEA_Helper_Image::createFromFile($postfile->getTmpName(), $postfile->getExt());

			$image_thum->crop($this->thumWidth, $this->thumHeight, true, true);

			$image_thum->saveAsJpeg($this->thumDir .  "/" . $thumbFilename);

			$image_thum->destory();

			// 保存原始相片

			$upload_flag=$postfile->move($this->photoDir . "/". $photoFilename);

			//echo $string;



			if($upload_flag){

				$res['photo_path']=$this->photoDir .  "/" . $photoFilename;
				
				$res['large_path']=$this->largeDir .  "/" . $largebFilename;

				$res['thum_path']=$this->thumDir .  "/" . $thumbFilename;

			}else {

				$res['photo_path']="";
				
				$res['large_path']="";

				$res['thum_path']="";

			}

		}

		return $res;

	}
	
}

?>