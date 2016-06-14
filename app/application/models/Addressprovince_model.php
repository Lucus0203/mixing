<?php
class Addressprovince_model extends CI_Model {
	
	public function __construct() {
		$this->load->database ();
	}
	
	public function get_provinces(){
		$query = $this->db->get('address_province');
    	return $query->result_array();
	}
	
}