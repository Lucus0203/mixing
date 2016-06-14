<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Index extends CI_Controller {
	var $_logininfo;
	function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form','url'));
	}
	
	
	public function index() {
		$this->load->view ( 'index' );
	}
}
