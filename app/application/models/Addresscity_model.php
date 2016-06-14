<?php
class Addresscity_model extends CI_Model {
	
	public function __construct() {
		$this->load->database ();
	}
	
	public function get_cities($provinceid = FALSE){
		if($provinceid === FALSE){
			return array();
		}else{
			$this->db->order_by('code','asc');
			$query = $this->db->get_where('address_city',array('province_id'=>$provinceid));
		}
		return $query->result_array();
	}
	
}