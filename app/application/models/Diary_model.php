<?php
/**
 * 
 * @author lucus
 * 漫生活
 *
 */
class Diary_model extends CI_Model {
	
	public function __construct() {
		$this->load->database ();
	}

        //查所有
	public function getAll($where = FALSE){
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'diary', $where );
		return $query->result_array ();
	}
        
	// 查
	public function getRow($where = FALSE) {
		if ($where === FALSE) {
			return array ();
		}
		$query = $this->db->get_where ( 'diary', $where );
		return $query->row_array ();
	}
	// 增
	public function create($obj) {
		$this->db->insert ( 'diary', $obj );
		return $this->db->insert_id();
	}
	// 改
	public function update($obj, $id) {
		$this->db->where ( 'id', $id );
		$this->db->update ( 'diary', $obj );
	}
	// 删
	public function del($id) {
		$this->db->where ( 'id', $id );
		$this->db->delete ( 'diary' );
	}
	
	
}