<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Welcome extends CI_Controller {
	
	function __construct() {
		parent::__construct ();
		$this->load->library('session');
		$this->load->helper ( array (
				'form',
				'url' 
		) );
	}
	
	public function index() {
		$this->load->view ( 'index' );
	}
	
}
