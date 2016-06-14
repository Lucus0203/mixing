<?php
/**
 * 
 * @author lucus
 * 微信用户(投票)
 *
 */
class Weixinuser_model extends CI_Model {
	
	public function __construct() {
		$this->load->database ();
	}

	// 查
	public function getRow($where = FALSE) {
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'weixin_user', $where );
		return $query->row_array ();
	}
	// 增
	public function create($obj) {
		$this->db->insert ( 'weixin_user', $obj );
		return $this->db->insert_id();
	}
	// 改
	public function update($obj, $id) {
		$this->db->where ( 'id', $id );
		$this->db->update ( 'weixin_user', $obj );
	}
	// 删
	public function del($id) {
		$this->db->where ( 'id', $id );
		$this->db->delete ( 'weixin_user' );
	}
	
	
}