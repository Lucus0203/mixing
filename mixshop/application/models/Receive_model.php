<?php
/**
 * 
 * @author lucus
 * 领取咖啡
 *
 */
class Receive_model extends CI_Model {
	
	public function __construct() {
		$this->load->database ();
	}

	// 查
	public function getRow($where = FALSE) {
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'encouter_receive', $where );
		return $query->row_array ();
	}
	// 增
	public function create($obj) {
		$this->db->insert ( 'encouter_receive', $obj );
		return $this->db->insert_id();
	}
	// 改
	public function update($obj, $id) {
		$this->db->where ( 'id', $id );
		$this->db->update ( 'encouter_receive', $obj );
	}
	// 删
	public function del($id) {
		$this->db->where ( 'id', $id );
		$this->db->delete ( 'encouter_receive' );
	}
	
	
}