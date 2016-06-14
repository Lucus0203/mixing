<?php
/**
 * 
 * @author lucus
 * 投票列表
 *
 */
class Votelist_model extends CI_Model {
	
	public function __construct() {
		$this->load->database ();
	}

	// 查
	public function getRow($where = FALSE) {
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'vote_list', $where );
		return $query->row_array ();
	}
	// 增
	public function create($obj) {
		$this->db->insert ( 'vote_list', $obj );
		return $this->db->insert_id();
	}
	// 改
	public function update($obj, $id) {
		$this->db->where ( 'id', $id );
		$this->db->update ( 'vote_list', $obj );
	}
	// 删
	public function del($id) {
		$this->db->where ( 'id', $id );
		$this->db->delete ( 'vote_list' );
	}
	
	
}