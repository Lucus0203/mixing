<?php
class Addresstown_model extends CI_Model {
	
	public function __construct() {
		$this->load->database ();
	}
	
	public function get_towns($cityid = FALSE){
		if($cityid===FALSE){
			return array();
		}else{
			$this->db->where('city_id',$cityid);
			$this->db->order_by('code','asc');
			$query = $this->db->get('address_town');
		}
		return $query->result_array();
	}
	
}