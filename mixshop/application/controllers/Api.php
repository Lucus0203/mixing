<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Api extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->database ();
		$this->load->library('session');
		$this->load->helper(array('form','url'));
		
		$loginInfo=$this->session->userdata('loginInfo');
		if(empty($loginInfo)){
			redirect('login','index');
		}
	}
	
	//获取城市
	public function getCityByProvince() {
		$province_id = $this->input->get('province_id');
		$this->db->select('id,name');
		$this->db->where('province_id',$province_id);
		$this->db->order_by('id','asc');
		$query=$this->db->get('address_city');
		$city=$query->result_array();
		$str="";
		foreach ($city as $c){
			$str.="<option value='".$c['id']."'>".$c['name']."</option>";
		}
		echo $str;
		exit();
	}
	
	//获取区县
	public function getTownByCity(){
		$city_id = $this->input->get('city_id');
		$this->db->select('id,name');
		$this->db->where('city_id',$city_id);
		$this->db->order_by('id','asc');
		$query=$this->db->get('address_town');
		$towns=$query->result_array();
		$str="";
		foreach ($towns as $t){
			$str.="<option value='".$t['id']."'>".$t['name']."</option>";
		}
		echo $str;
		exit();
	}
	
	
}
