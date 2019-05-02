<?php

/**

 * ?送?件

 * Author: libin

 * Date: 2008年10月31日

 * File: Mail.php

 * Version: 1.0
 * example
 *
$mail = & new Service_Mail();
$attachment[0]['path']=$_FILES['upfile']['tmp_name'];
$attachment[0]['name']= $_FILES['upfile']['name'];
$attachment[0]['type']=$_FILES['upfile']['type'];
$mail->init("lb@shunc.cn","aa@12.com","新しいフォルダ","新しいフォルダ新しいフォルダ新しいフォルダ新しいフォルダ新しいフォルダ",$attachment);
$mail->send();

 */

class Service_Mail
{
	var $_to="";
	var $_from="";
	var $_subject="";
	var $_body="";

	var $_attachment=array();

	function __construct(){
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $to
	 */
	function setTo($to=""){
		$this->_to=$to;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $from
	 */
	function setFrom($from =""){
		$this->_from = $from;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $subject
	 */
	function setSubject($subject=""){
		$subject ="=?UTF-8?B?".base64_encode($subject)."?=";
		$this->_subject = $subject;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $body
	 */
	function setBody($body=""){
		$this->_body = $body;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $attachment
	 */
	function setAttachment($attachment=array()){
		$this->_attachment=$attachment;
	}
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $to
	 * @param unknown_type $from
	 * @param unknown_type $subject
	 * @param unknown_type $body
	 * @param unknown_type $attachment
	 */
	function init($to,$from,$subject,$body,$attachment=array()){
		$this->setTo($to);
		$this->setFrom($from);
		$this->setSubject($subject);
		$this->setBody($body);
		$this->setAttachment($attachment);
	}
	/**
 	* Enter description here...
 	*
 	*/

	function send(){
		$num = count($this->_attachment);
		if(is_array($this->_attachment) && $num>0){
			$attchflag =true;
		}else{
			$attchflag =false;
		}

		$boundary = md5(uniqid(rand()));

		$msg = "";
		$header = "From: $this->_from\n";
		$header .= "Reply-To: $this->_from\n";
		$header .= "X-Mailer: PHP/".phpversion()."\n";
		$header .= "MIME-version: 1.0\n";

		if($attchflag){
			$header .= "Content-Type: multipart/mixed;\n";
			$header .= "\tboundary=\"$boundary\"\n";
			$msg .= "This is a multi-part message in MIME format.\n\n";
			$msg .= "--$boundary\n";
			$msg .= "Content-Type: text/plain; charset=UTF-8\n";
			$msg .= "Content-Transfer-Encoding: 7bit\n\n";
		}else{
			$header .= "Content-Type: text/plain; charset=UTF-8\n";
			$header .= "Content-Transfer-Encoding: 7bit\n";
		}
		$msg .= $this->_body."\n";

		if($attchflag){
			foreach ($this->_attachment as $value){
				if(file_exists($value['path'])){
					$filename =$upfile_name ="=?UTF-8?B?".base64_encode($value['name'])."?=";

					$fp = fopen($value['path'], "r");
					$contents = fread($fp, filesize($value['path']));
					fclose($fp);
					$f_encoded = chunk_split(base64_encode($contents));
					$msg .= "\n\n--$boundary\n";
					$msg .= "Content-Type: " . $value['type'] . ";\n";
					$msg .= "\tname=\".$filename.\"\n";
					$msg .= "Content-Transfer-Encoding: base64\n";
					$msg .= "Content-Disposition: attachment;\n";
					$msg .= "\tfilename=\".$filename\"\n\n";
					$msg .= "$f_encoded\n";
				}

			}
			$msg .= "--$boundary--";
		}
		if(mail($this->_to, $this->_subject, $msg, $header)){
			return true;

		}else{
			return false;
		}
	}
	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function sendHtml(){
		$num = count($this->_attachment);
		if(is_array($this->_attachment) && $num>0){
			$attchflag =true;
		}else{
			$attchflag =false;
		}

		$boundary = md5(uniqid(rand()));

		$msg = "";
		$header = "From: $this->_from\n";
		$header .= "Reply-To: $this->_from\n";
		$header .= "X-Mailer: PHP/".phpversion()."\n";
		$header .= "MIME-version: 1.0\n";

		if($attchflag){
			$header .= "Content-Type: multipart/mixed;\n";
			$header .= "\tboundary=\"$boundary\"\n";
			$msg .= "This is a multi-part message in MIME format.\n\n";
			$msg .= "--$boundary\n";
			$msg .= "Content-Type: text/html; charset=UTF-8\n";
			$msg .= "Content-Transfer-Encoding: 7bit\n\n";
		}else{
			$header .= "Content-Type: text/html; charset=UTF-8\n";
			$header .= "Content-Transfer-Encoding: 7bit\n";
		}
		$msg .= $this->_body."\n";

		if($attchflag){
			foreach ($this->_attachment as $value){
				if(file_exists($value['path'])){
					$filename =$upfile_name ="=?UTF-8?B?".base64_encode($value['name'])."?=";

					$fp = fopen($value['path'], "r");
					$contents = fread($fp, filesize($value['path']));
					fclose($fp);
					$f_encoded = chunk_split(base64_encode($contents));
					$msg .= "\n\n--$boundary\n";
					$msg .= "Content-Type: " . $value['type'] . ";\n";
					$msg .= "\tname=\".$filename.\"\n";
					$msg .= "Content-Transfer-Encoding: base64\n";
					$msg .= "Content-Disposition: attachment;\n";
					$msg .= "\tfilename=\".$filename\"\n\n";
					$msg .= "$f_encoded\n";
				}

			}
			$msg .= "--$boundary--";
		}
		if(mail($this->_to, $this->_subject, $msg, $header)){
			return true;

		}else{
			return false;
		}
	}


	/**

	 * ?送?件

	 * @author  libin at 2008-10-31

	 * @param unknown_type $from

	 * @param unknown_type $to

	 * @param unknown_type $subject

	 * @param unknown_type $message

	 * @return unknown

	 */

	function mail($from,$to,$subject,$message){

		mb_language("ja");

		mb_internal_encoding("UTF-8");

		$header = "From: ".mb_encode_mimeheader($from)."<$from>";



		$message =mb_convert_kana($message ,"K", "utf-8");



		mb_language("ja");

		mb_internal_encoding("UTF-8");



		if (mb_send_mail($to, $subject, $message, $header))

		{

			return true;

		}

		else

		{

			return false;

		}



	}

}

?>