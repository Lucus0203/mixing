<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Imgsizepress{

	function __construct() {
	}
	
	private function __clone() {
	}
	
	//压缩图
	function image_png_size_press($imgsrc,$imgdst){
		list($width,$height,$type)=getimagesize($imgsrc);
		$new_width = $width;
		$new_height =$height;
		switch($type){
			case 1:
				$giftype=$this->check_gifcartoon($imgsrc);
				if($giftype){
					header('Content-Type:image/gif');
					$image_wp=imagecreatetruecolor($new_width, $new_height);
					$image = imagecreatefromgif($imgsrc);
					imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
					imagejpeg($image_wp, $imgdst,75);
					imagedestroy($image_wp);
				}
				break;
			case 2:
				header('Content-Type:image/jpeg');
				$image_wp=imagecreatetruecolor($new_width, $new_height);
				$image = imagecreatefromjpeg($imgsrc);
				imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				imagejpeg($image_wp, $imgdst,75);
				imagedestroy($image_wp);
				break;
			case 3:
				header('Content-Type:image/png');
				$image_wp=imagecreatetruecolor($new_width, $new_height);
				$image = imagecreatefrompng($imgsrc);
				imagecopyresampled($image_wp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				imagejpeg($image_wp, $imgdst,75);
				imagedestroy($image_wp);
				break;
		}
	}
	/**
	 * desription 判断是否gif动画
	 * @param sting $image_file图片路径
	 * @return boolean t 是 f 否
	 */
	function check_gifcartoon($image_file){
		$fp = fopen($image_file,'rb');
		$image_head = fread($fp,1024);
		fclose($fp);
		return preg_match("/".chr(0x21).chr(0xff).chr(0x0b).'NETSCAPE2.0'."/",$image_head)?false:true;
	}
	
	
}
