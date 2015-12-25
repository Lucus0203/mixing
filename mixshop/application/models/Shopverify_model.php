<?php
/**
 * 
 * @author lucus
 * 店铺验劵记录
 *
 */
class Shopverify_model extends CI_Model {
	
	public function __construct() {
		$this->load->database ();
	}

        //查所有
	public function getAll($where = FALSE){
		if ($where === FALSE) {
			return array ();
		}
                //$this->db->order_by('id','desc');
		$query = $this->db->get_where ( 'shop_verify', $where );
		return $query->result_array ();
	}
        
	// 查
	public function getRow($where = FALSE) {
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'shop_verify', $where );
		return $query->row_array ();
	}
	// 增
	public function create($obj) {
		$this->db->insert ( 'shop_verify', $obj );
		return $this->db->insert_id();
	}
	// 改
	public function update($obj, $id) {
		$this->db->where ( 'id', $id );
		$this->db->update ( 'shop_verify', $obj );
	}
	// 删
	public function del($id) {
		$this->db->where ( 'id', $id );
		$this->db->delete ( 'shop_verify' );
	}
	
	
}