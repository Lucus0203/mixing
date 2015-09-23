<?php
/**
 * 
 * @author lucus
 * 菜单
 *
 */
class Menu_model extends CI_Model {
	
	public function __construct() {
		$this->load->database ();
	}

	//查所有
	public function getAll($where = FALSE){
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'shop_menu', $where );
		return $query->result_array ();
	}
	
	// 查
	public function getRow($where = FALSE) {
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'shop_menu', $where );
		return $query->row_array ();
	}
	// 增
	public function create($obj) {
		$this->db->insert ( 'shop_menu', $obj );
		return $this->db->insert_id();
	}
	// 改
	public function update($obj, $id) {
		$this->db->where ( 'id', $id );
		$this->db->update ( 'shop_menu', $obj );
	}
	// 删
	public function del($id) {
		$this->db->where ( 'id', $id );
		$this->db->delete ( 'shop_menu' );
	}

	//移除所有相关菜单
	public function delByCond($where){
		$this->db->where ( $where );
		$this->db->delete ( 'shop_menu' );
	}
	
	
}