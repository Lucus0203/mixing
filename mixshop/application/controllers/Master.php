<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Master extends CI_Controller {
	var $_tags;
	var $_logininfo;
	function __construct() {
		parent::__construct ();
		$this->load->library ( array('session', 'common' , 'upload'));
		$this->load->helper ( array (
				'form',
				'url',
				'path' 
		) );
		$this->load->model ( array (
				'master_model',
		) );

		$this->_logininfo=$this->session->userdata('loginInfo');
		if (empty ( $this->_logininfo )) {
			redirect ( 'login', 'index' );
		}else{
			$this->load->vars(array('loginInfo'=>$this->_logininfo));
		}
	}
	
	public function index() {
		redirect ( 'index.php/index.html' );
	}
	
	//店主认证
	public function certification() {
		$loginInfo = $this->session->userdata ( 'loginInfo' );
		$act = $this->input->post ( 'act' );
		$msg = '';
		if(!empty($act)){
			$certification=array();
			$certification['name']=$this->input->post('name');
			$certification['tel']=$this->input->post('tel');
			$certification['qq']=$this->input->post('qq');
			$certification['weixin']=$this->input->post('weixin');
			$dir = 'uploads/master/' .$loginInfo['user_name'] . '/';
			if (! file_exists ( $dir )) {
				mkdir ( $dir, 0777 );
			}
			$confimg ['file_name'] = 'id_certification';
			$confimg ['upload_path'] = $dir;
			$confimg ['allowed_types'] = 'gif|jpg|png|jpeg';
			$this->upload->initialize($confimg);
			if ($this->upload->do_upload ( 'IDfile' )) {
				$imginfo = $this->upload->data ();
				$certification['idfile'] = base_url().$dir . $imginfo ['file_name'];
			}else{
				$imginfo = $this->upload->display_errors();
			}
			$confimg ['file_name'] = 'business_license';
			$confimg ['upload_path'] = $dir;
			$confimg ['allowed_types'] = 'gif|jpg|png|jpeg';
			$this->upload->initialize($confimg);
			if ($this->upload->do_upload ( 'business_license' )) {
				$imginfo = $this->upload->data ();
				$certification['business_license'] = base_url().$dir . $imginfo ['file_name'];
			}
			
			if($this->input->post('id')!=''){
				$this->master_model->update($certification,$loginInfo['id']);
			}else{
				$this->master_model->create($certification);
			}
			$msg='保存成功';
		}
		$data=$this->master_model->getRow(array('id'=>$loginInfo['id']));
		$res = array ('data'=>$data,'msg'=>$msg);
		
		$this->load->view ( 'header');
		$this->load->view ( 'left' );
		$this->load->view ( 'master/certification', $res );
		$this->load->view ( 'footer' );
	}
	
}