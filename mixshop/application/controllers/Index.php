<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Index extends CI_Controller {
	var $_logininfo;
	function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form','url'));
		
		$this->_logininfo=$this->session->userdata('loginInfo');
		if(empty($this->_logininfo)){
			redirect('login','index');
		}else{
			$this->load->vars(array('loginInfo'=>$this->_logininfo));
		}
	}
	
	
	public function index() {
		$this->load->view ( 'header' );
		$this->load->view ( 'left' );
		$this->load->view ( 'index' );
		$this->load->view ( 'footer' );
	}
}
