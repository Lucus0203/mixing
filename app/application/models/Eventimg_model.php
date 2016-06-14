<?php
/**
 * 
 * @author lucus
 * 店铺图片
 *
 */
class Eventimg_model extends CI_Model {
	
	public function __construct() {
		$this->load->database ();
	}
	
	//查所有
	public function getAll($where = FALSE){
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'public_photo', $where );
		return $query->result_array ();
	}
	
	// 查
	public function getRow($where = FALSE) {
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'public_photo', $where );
		return $query->row_array ();
	}
	// 增
	public function create($obj) {
		$this->db->insert ( 'public_photo', $obj );
		return $this->db->insert_id();
	}
	// 改
	public function update($obj, $id) {
		$this->db->where ( 'id', $id );
		$this->db->update ( 'public_photo', $obj );
	}
	// 删
	public function del($id) {
		$this->db->where ( 'id', $id );
		$this->db->delete ( 'public_photo' );
	}
	//移除所有相关图片
	public function delByCond($where){
		$this->db->where ( $where );
		$this->db->delete ( 'public_photo' );
	}
	
}