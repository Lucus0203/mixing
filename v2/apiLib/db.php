<?php
require_once 'apiLib/config.php';
class db {
	private $conn;
	private static $sql;
	private static $instance;
	private function __construct() {
		$this->conn = mysqli_connect ( SERVER_NAME, DB_USER_NAME, DB_PASSWORD,DATABASE )or die("Error " . mysqli_error($this->conn));
		mysqli_query ($this->conn, "set names utf8;");
	}
	private function __clone() {
	}
	public static function getInstance() {
		if (! self::$instance instanceof self) {
			self::$instance = new db;
		}
		return self::$instance;
	}
	
	/*
	 * 查询数据库
	 */
	public function getAll($table, $condition = array(), $field = array() , $limit="") {
		$table=DB_PREFIX.$table;
		$where = '';
		if (! empty ( $condition )) {
			
			foreach ( $condition as $k => $v ) {
				$where .= $k . "='" . $v . "' and ";
			}
			$where = 'where ' . $where . '1=1';
		}
		$fieldstr = '';
		if (! empty ( $field )) {
			
			foreach ( $field as $k => $v ) {
				$fieldstr .= $v . ',';
			}
			$fieldstr = rtrim ( $fieldstr, ',' );
		} else {
			$fieldstr = '*';
		}
		self::$sql = "select {$fieldstr} from {$table} {$where} {$limit}";
		$result = mysqli_query ($this->conn, self::$sql );
		$resuleRow = array ();
		$i = 0;
		while ( $row = @mysqli_fetch_assoc ( $result ) ) {
			foreach ( $row as $k => $v ) {
				$resuleRow [$i] [$k] = $v;
			}
			$i ++;
		}
		return $resuleRow;
	}
	//查询一条记录
	public function getRow($table, $condition = array(), $field = array()) {
		$table=DB_PREFIX.$table;
		$where = '';
		if (! empty ( $condition )) {
				
			foreach ( $condition as $k => $v ) {
				$where .= $k . "='" . $v . "' and ";
			}
			$where = 'where ' . $where . '1=1';
		}
		$fieldstr = '';
		if (! empty ( $field )) {
				
			foreach ( $field as $k => $v ) {
				$fieldstr .= $v . ',';
			}
			$fieldstr = rtrim ( $fieldstr, ',' );
		} else {
			$fieldstr = '*';
		}
		self::$sql = "select {$fieldstr} from {$table} {$where}";
		$result = mysqli_query ($this->conn, self::$sql );
		$resuleRow = array ();
		while ( $row = mysqli_fetch_assoc ( $result ) ) {
			return $row;
		}
	}
	//用sql查询记录条数
	public function getCount($table,$condition=array()){
		$table=DB_PREFIX.$table;
		$where = '';
		if (! empty ( $condition )) {
				
			foreach ( $condition as $k => $v ) {
				$where .= $k . "='" . $v . "' and ";
			}
			$where = 'where ' . $where . '1=1';
		}
		self::$sql = "select count(*) as count from {$table} {$where} limit 1";
		$result = mysqli_query ($this->conn, self::$sql );
		while ( $row = @mysqli_fetch_assoc ( $result ) ) {
			return @$row['count'];
			//return array_shift($row);
		}
	}
	/**
	 * 添加一条记录
	 */
	public function create($table, $data) {
		$table=DB_PREFIX.$table;
		$values = '';
		$datas = '';
		foreach ( $data as $k => $v ) {
			$values .= $k . ',';
			$datas .= "'$v'" . ',';
		}
		$values = rtrim ( $values, ',' );
		$datas = rtrim ( $datas, ',' );
		self::$sql = "INSERT INTO  {$table} ({$values}) VALUES ({$datas})";
		if (mysqli_query ($this->conn, self::$sql )) {
			return mysqli_insert_id ($this->conn);
		} else {
			return false;
		}
		;
	}
	/**
	 * 修改一条记录
	 */
	public function update($table, $data, $condition = array()) {
		$table=DB_PREFIX.$table;
		$where = '';
		if (! empty ( $condition )) {
			
			foreach ( $condition as $k => $v ) {
				$where .= $k . "='" . $v . "' and ";
			}
			$where = 'where ' . $where . '1=1';
		}
		$updatastr = '';
		if (! empty ( $data )) {
			foreach ( $data as $k => $v ) {
				$updatastr .= $k . "='" . $v . "',";
			}
			$updatastr = 'set ' . rtrim ( $updatastr, ',' );
		}
		self::$sql = "update {$table} {$updatastr} {$where}";
		return mysqli_query ($this->conn, self::$sql );
	}
	/**
	 * 删除记录
	 */
	public function delete($table, $condition) {
		$table=DB_PREFIX.$table;
		$where = '';
		if (! empty ( $condition )) {
			
			foreach ( $condition as $k => $v ) {
				$where .= $k . "='" . $v . "' and ";
			}
			$where = 'where ' . $where . '1=1';
		}
		self::$sql = "delete from {$table} {$where}";
		return mysqli_query ($this->conn, self::$sql );
	}
	
	//用sql查询所有
	public function getAllBySql($sql) {
		self::$sql = $sql;
		$result = mysqli_query ($this->conn, self::$sql );
		$return = array ();
		while ( $row = @mysqli_fetch_assoc ( $result ) ) {
			$return [] = @$row;
		}
		return $return;
	}
	
	//用sql查询一条记录
	public function getRowBySql($sql){
		self::$sql = $sql;
		$result = mysqli_query ($this->conn, self::$sql );
		while ( $row = @mysqli_fetch_assoc ( $result ) ) {
			return @$row;
		}
	}
	public function getCountBySql($sql){
		self::$sql = "select count(*) as count from ($sql) s limit 1 ";
		$result = mysqli_query ($this->conn, self::$sql );
		while ( $row = @mysqli_fetch_assoc ( $result ) ) {
			return @$row['count'];
		}
	}
        
        //执行sql语句
        public function excuteSql($sql){
                self::$sql = $sql;
                $result = mysqli_query ($this->conn, self::$sql );
        }
	
	
	public static function getLastSql() {
		echo self::$sql;
	}
	
}