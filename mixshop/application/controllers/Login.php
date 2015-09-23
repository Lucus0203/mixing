<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Login extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library(array('session','sms'));
		$this->load->helper(array('form','url'));
		$this->load->model(array('master_model'));
		
	}
	
	
	public function index() {
		$user = $this->input->post('username');
		$pass = $this->input->post('password');
		$data=array();
		if ($user != "" && $pass != "") {
			$userinfo = $this->master_model->getRow(array('user_name'=>$user));
			if (count ( $userinfo ) > 0 && is_array ( $userinfo )) {
				$pwd = $userinfo ['user_password'];
				if ($pwd == md5($pass)) {
					$this->session->set_userdata('loginInfo',$userinfo);
					redirect ( 'index','index' );
				}else{
					$data=array('error_msg'=>"密码错误");
				}
			}else{
				$data=array('error_msg'=>"账号或密码错误");
			}
		}
		$this->load->view ( 'login/login' ,$data );
		
	}
	
	//注册
	public function register(){
		$res=array();
		$user = $this->input->post('username');
		$pass = $this->input->post('password');
		$mobile = $this->input->post('mobile');
		$code = $this->input->post('captcha_code');
		$act = $this->input->post('act');
		if(!empty($act)){
			$userinfo = $this->master_model->getRow(array('user_name'=>$user,"mobile != '$mobile'"));
			if(!empty($userinfo)){
				$res['msg']='账号已被使用';
			}else{
				$userinfo = $this->master_model->getRow(array('mobile'=>$mobile));
				if(!empty($userinfo)&&$userinfo['captcha_code']==$code){
					$code=rand(1000, 9999);//换个验证码
					$this->master_model->update(array('user_name'=>$user,'user_password'=>md5($pass),'captcha_code'=>$code,'status'=>2),$userinfo['id']);
					$userinfo = $this->master_model->getRow(array('user_name'=>$user));
					$this->session->set_userdata('loginInfo',$userinfo);
					redirect ( 'index','index' );
					//redirect(base_url('login/register_success'));
				}else{
					$res['msg']='验证码错误';
				}
			}
		}
		
		$this->load->view ( 'login/register',$res );
	}
	
	//注册成功
	public function register_success(){
		$this->load->view ( 'login/register_success' );
	}
	
	//获取验证码
	public function getcode(){
		$user = $this->input->post('username');
		$mobile = $this->input->post('mobile');
		$code=rand(1000, 9999);
		$userinfo = $this->master_model->getRow(array('mobile'=>$mobile));
		if($userinfo['status']==2){
			echo '此手机号已注册';
			return false;
		}
		$repeatname = $this->master_model->getRow(array('user_name'=>$user,"mobile != '$mobile'"));
		if(!empty($repeatname)){
			echo '账号已被使用';
			return false;
		}
		if(!empty($userinfo['id'])){
			$this->master_model->update(array('captcha_code'=>$code),$userinfo['id']);
		}else{
			$this->master_model->create(array('mobile'=>$mobile,'captcha_code'=>$code,'created'=>date("Y-m-d H:i:s"),'status'=>1));
		}
		echo '1';
		$this->sms->sendMsg('验证码:'.$code,$mobile);
	}
	
	public function loginout(){
		$this->session->sess_destroy();
		redirect(base_url('login'));
	}
	
}
