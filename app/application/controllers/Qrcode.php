<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Qrcode extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->library ( array('session', 'common' , 'upload' , 'image_lib' ,'imgsizepress'));
		$this->load->helper ( array (
				'form',
				'url',
				'path' 
		) );
		$this->load->model ( array (
		) );
	}
	
	public function index() {
		$this->load->view ( 'qrcode/qrcode' );
	}
	
	
	
}
