<?php
/**

 * Project: 分页类

 * Sub Project: 分页类
 * 例子
 * $pages= & get_singleton("Service_Page");
 $pages->_page_no=$page_no;
 $pages->_total=1000;
 $pages->_url=url("Default","Index");
 $conent=$pages->page();

 * Author: libin

 * Date: 2009年08月05日

 * File: Page.php

 * Version: 1.0

 */
class Service_Page {
	private $_page_no = 1;
	private $_page_num = 10;
	private $_total = 0;
	private $_parm = array ();
	private $_url = "";
	private $_class = "";
	//大的分页
	private $_big_page = 10;
	
	function __construct() {
	}
	/**
	 * get the private parm value
	 *
	 * @param unknown_type $varname
	 * @return unknown
	 */
	function __get($varname) {
		if (isset ( $this->$varname )) {
			return $this->$varname;
		} else {
			return null;
		}
	}
	/**
	 * set the private parm value
	 *
	 * @param unknown_type $varname
	 * @param unknown_type $value
	 */
	function __set($varname, $value) {
		if (isset ( $this->$varname )) {
			$this->$varname = $value;
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Enter description here...
	 *
	 */
	function page() {
		$pagecontent="";
		$pages = ceil ( $this->_total / $this->_page_num );
		$prev = $this->_page_no - 1;
		if ($prev < 1) {
			$prev = 1;
		}
		$next = $this->_page_no + 1;
		if ($next > $pages) {
			$next = $pages;
		}
		$url = $this->_url;
		$url .= $this->createUrlParm ( $this->_parm );
		$prevurl = $url . "&page_no=" . $prev;
		$nexturl = $url . "&page_no=" . $next;
		
		if ($pages > 1) {
			$start=($this->_page_no-1)*$this->_page_num+1;
			$end=$start+$this->_page_num-1;
			if($end>$this->_total){
				$end=$this->_total;
			}
			$select='<select class="selectPage">';
			for ($p=1;$p<=$pages;$p++){
				if($p==$this->_page_no){
					$select.='<option value="'.$p.'" selected>'.$p.'</option>';
				}else{
					$select.='<option value="'.$p.'">'.$p.'</option>';
				}
			}
			$select.='</select>';
			$pagecontent='<div class="hd_t2">共<span>'.$this->_total.'</span>条记录&nbsp;&nbsp;&nbsp;<span>'.$this->_page_num.'</span>条记录/页<a href="'.$url.'&page_no=1">首页</a><a href="'.$prevurl.'">上一页</a><a href="'.$nexturl.'">下一页</a><a href="'.$url.'&page_no='.$pages.'">尾页</a>&nbsp;&nbsp;&nbsp;页次：'.$select.'/'.$pages.'页</div>';
		}
		
		return $pagecontent;
	}
	
/**
	 * Enter description here...
	 *
	 */
	function postPage() {
		$total = $this->_total;
		$offset = ($this->_page_no - 1) * $this->_page_num;
		$pages = ceil ( $this->_total / $this->_page_num );
		
		$max = $offset + $this->_page_num;
		if ($max > $total) {
			$max = $total;
		}
		$offset ++;
		
		$url = $this->_url;
		$formparm=$this->createForm($this->_parm );
		
		
		$prev = $this->_page_no - 1;
		$prev = $prev < 1 ? 1 : $prev;
		$next = $this->_page_no + 1;
		$next = $next > $pages ? $pages : $next;
		
		
		if ($pages > $this->_big_page) {
			$max_big_page = ceil ( $pages / $this->_big_page );
			$current_big_page = ceil ( $this->_page_no / $this->_big_page );
			if ($current_big_page == 1) {
				$prev_big_page = 1;
			} else {
				$prev_big_page = ($current_big_page - 1) * $this->_big_page - 1;
			}
			if ($current_big_page == $max_big_page) {
				$next_big_page = ($max_big_page - 1) * $this->_big_page + 1;
			} else {
				$next_big_page = $current_big_page * $this->_big_page + 1;
			}
			$prev_big = '<li><a href="javascript:void(0)" onclick="goToPage('.$prev_big_page.')">前10頁</a></li>';
			$next_big = '<li><a href="javascript:void(0)" onclick="goToPage('.$next_big_page.')">次10頁</a></li>';
		}else{
			$prev_big="";
			$next_big="";
		}
		
		$start = ceil ( $this->_page_no / $this->_big_page );
		$start = ($start - 1) * $this->_big_page + 1;
		$end = $start + $this->_big_page;
		if ($end > $pages) {
			$end = $pages;
		}
		
		$list="";
		for($i = $start; $i < $end + 1; $i ++) {
				$list .= '<li><a href="javascript:void(0)" onclick="goToPage('.$i.')">'.$i.'</a></li>';
		}
		
		
		$content = '
		<form class="pageForm" id="pageForm" method="post" action="'.$url.'">
		<input type="hidden" name="page_no" id="search_form_page_no" value="1" >'.$formparm.'
		<script type="text/javascript">
			function goToPage(pagenum){
					if(pagenum==""){
						pagenum=1;
					}
					$("#search_form_page_no").val(pagenum);
					$("#pageForm").submit();
			}
		</script>
		<div class="searchPage">
					<div class="numres">検索結果：(<span>該当件数：' . $total . '</span> 件)</div>
					<div class="offset">' . $offset . ' 件目から' . $max . '  件目を表示 
						<span class="list">&lt;<a href="javascript:void(0)" onclick="goToPage('.$prev.')"> 前へ</a>｜<a href="javascript:void(0)" onclick="goToPage('.$next.')">次へ</a>&gt;</span>
					</div>
					</div>
		</form>		
		';
		return $content;
	
	}
	
	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function defaultPage() {
		$prev_big = "";
		$next_big = "";
		
		$pages = ceil ( $this->_total / $this->_page_num );
		if ($this->_page_no < 1) {
			$this->_page_no = 1;
		}
		$url = $this->_url;
		$url .= $this->createUrlParm ( $this->_parm );
		
		if ($pages > $this->_big_page) {
			$max_big_page = ceil ( $pages / $this->_big_page );
			$current_big_page = ceil ( $this->_page_no / $this->_big_page );
			if ($current_big_page == 1) {
				$prev_big_page = 1;
			} else {
				$prev_big_page = ($current_big_page - 1) * $this->_big_page - 1;
			}
			if ($current_big_page == $max_big_page) {
				$next_big_page = ($max_big_page - 1) * $this->_big_page + 1;
			} else {
				$next_big_page = $current_big_page * $this->_big_page + 1;
			}
			$prev_big = "<li style=\"display:inline;\">&nbsp;<a href='" . $url . "&page_no=" . $prev_big_page . "'>前10頁</a>&nbsp;</li>";
			$next_big = "<li style=\"display:inline;\">&nbsp;<a href='" . $url . "&page_no=" . $next_big_page . "'>次10頁</a>&nbsp;</li>";
		}
		$start = ceil ( $this->_page_no / $this->_big_page );
		$start = ($start - 1) * $this->_big_page + 1;
		$end = $start + $this->_big_page;
		if ($end > $pages) {
			$end = $pages;
		}
		
		if ($this->_page_no == 1) {
			$prev = $url . "&page_no=1";
		} else {
			$prev = $url . "&page_no=" . ($this->_page_no - 1);
		}
		if ($this->_page_no == $pages) {
			$next = $url . "&page_no=" . $pages;
		} else {
			$next = $url . "&page_no=" . ($this->_page_no + 1);
		}
		
		if ($pages > 1) {
			$pagecontent = "<ul id='" . $this->_class . "'>";
			$pagecontent .= "<li><a href='" . $prev . "'>前へ</a></li>";
			for($i = $start; $i < $end + 1; $i ++) {
				$resurl = $url . "&page_no=" . $i;
				$pagecontent .= "<li><a  href='" . $resurl . "'>" . $i . "</a></li>";
			}
			$pagecontent .= "<li><a href='" . $next . "'>次へ</a></li></ul>";
		}
		
		return $pagecontent;
	}
	
	/**
	 * 构造参数
	 * @author libin 2008-10-13
	 * @param array() $var
	 * @return unknown
	 */
	function createUrlParm($var) {
		$urlparm = "";
		if (count ( $var ) > 0) {
			foreach ( $var as $key => $value ) {
				$urlparm = $urlparm . "&" . $key . "=" . $value;
			}
		}
		return $urlparm;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $var
	 */
	function createForm($var) {
		$formparm = "";
		if (count ( $var ) > 0) {
			foreach ( $var as $key => $value ) {
				if (is_array ( $value ) && count ( $value ) > 0) {
					foreach ($value as $k=>$v){
						$formparm .= '<input type="hidden" name="'.$key.'['.$k.']" value="'.$v.'">';
					}
				} else {
					$formparm .= '<input type="hidden" name="'.$key.'" value="'.$value.'">';
				}
			}
		}
		return $formparm;
	}
}
?>