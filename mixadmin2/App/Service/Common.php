<?php

/**

 * Project: the usualy usering data

 * Sub Project: 共通クラス

 * Author: libin

 * Date: 2011年03月06日

 * File: Data.php

 * Version: 1.0

 */

class Service_Common {
	
	/**
	 * Enter description here...
	 *
	 */
	function __construct() {
	
	}
	
	/**

	 * Enter description here...

	 *

	 * @param unknown_type $array

	 * @param unknown_type $clom

	 * @param unknown_type $flag

	 * @return unknown

	 */
	
	function array_sort($array, $clom = "", $flag = "asc") {
		
		if ($flag == "asc") {
			
			for($i = 0; $i < count ( $array ); $i ++) {
				
				for($j = $i; $j < count ( $array ); $j ++) {
					
					if ($array [$j] [$clom] < $array [$i] [$clom]) {
						
						$min = $array [$i];
						
						$array [$i] = $array [$j];
						
						$array [$j] = $min;
					
					}
				
				}
			
			}
		
		} else {
			
			for($i = 0; $i < count ( $array ); $i ++) {
				
				for($j = $i; $j < count ( $array ); $j ++) {
					
					if ($array [$j] [$clom] > $array [$i] [$clom]) {
						
						$max = $array [$i];
						
						$array [$i] = $array [$j];
						
						$array [$j] = $max;
					
					}
				
				}
			
			}
		
		}
		
		return $array;
	
	}
	
	function array_sort_co($array, $clom = "", $flag = "asc") {
		
		if ($flag == "asc") {
			
			for($i = 0; $i < count ( $array ); $i ++) {
				
				for($j = $i; $j < count ( $array ); $j ++) {
					
					if ($array [$j] [0] [$clom] < $array [$i] [0] [$clom]) {
						
						$min = $array [$i];
						
						$array [$i] = $array [$j];
						
						$array [$j] = $min;
					
					}
				
				}
			
			}
		
		} else {
			
			for($i = 0; $i < count ( $array ); $i ++) {
				
				for($j = $i; $j < count ( $array ); $j ++) {
					
					if ($array [$j] [0] [$clom] > $array [$i] [0] [$clom]) {
						
						$max = $array [$i];
						
						$array [$i] = $array [$j];
						
						$array [$j] = $max;
					
					}
				
				}
			
			}
		
		}
		
		return $array;
	
	}
	
	/**

	 * Enter description here...

	 *

	 * @param unknown_type $dir

	 * @return unknown

	 */
	
	function deldir($dir) {
		$dh = opendir ( $dir );
		
		while ( $file = readdir ( $dh ) ) {
			
			if ($file != "." && $file != "..") {
				
				$fullpath = $dir . "/" . $file;
				
				if (! is_dir ( $fullpath )) {
					
					unlink ( $fullpath );
				
				} else {
					
					deldir ( $fullpath );
				
				}
			
			}
		
		}
		closedir ( $dh );
		
		if (rmdir ( $dir )) {
			return true;
		} else {
			
			return false;
		}
	
	}
	
	function TextReplace($str) {
		$arr = array ('島' => '島 ', '㈱' => '(株)', '㈲' => '(有)', '㈹' => '(代)', '髙' => '高', "'" => "’", '﨑' => '崎', '①' => '1', '②' => '2', '③' => '3', '④' => '4', '⑤' => '5', '⑥' => '6', '⑦' => '7', '⑧' => '8', '⑨' => '9', '⑩' => '10', '⑪' => '11', '⑫' => '12', '⑬' => '13', '⑭' => '14', '⑮' => '15', '⑯' => '16', '⑰' => '17', '⑱' => '18', '⑲' => '19', '⑳' => '20', '㊤' => '上', '㊦' => '下', '㊧' => '左', '㊨' => '右', '㊥' => '中', 'Ⅰ' => '1', 'Ⅱ' => '2', 'Ⅲ' => '3', 'Ⅳ' => '4', 'Ⅴ' => '5', 'Ⅵ' => '6', 'Ⅶ' => '7', 'Ⅷ' => '8', '㎝' => 'ｃｍ' );
		foreach ( $arr as $key => $value ) {
			$str = str_replace ( $key, $value, $str );
		}
		return $str;
	}
	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	function dateReplace($data) {
		$arr = array ('H24' => '2012', 'H23' => '2011', 'H22' => '2010', 'H21' => '2009', 'H20' => '2008', 'H19' => '2007', 'H18' => '2006', 'H17' => '2005', 'H16' => '2004', 'H15' => '2003', 'H14' => '2001', 'H13' => '2000', 'H12' => '1999', 'H11' => '1998', 'H10' => '1997', 'H09' => '1996', 'H08' => '1995', 'H07' => '1994', 'H06' => '1993', 'H05' => '1992', 'H04' => '1991', 'H03' => '1990', 'H02' => '1989', 'H01' => '1988' );
		return $arr [$data];
	}
	/**
	 * 
	 * Enter description here ...
	 */
	function getDistance($lat1, $lng1, $lat2, $lng2) {
		$EARTH_RADIUS = 6378.137;
		//将角度转为狐度
		$radLat1=deg2rad($lat1);
		$radLat2=deg2rad($lat2);
		$radLng1=deg2rad($lng1);
		$radLng2=deg2rad($lng2);
		$a=$radLat1-$radLat2;//两纬度之差,纬度<90
		$b=$radLng1-$radLng2;//两经度之差纬度<180
		$s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*$EARTH_RADIUS;
		return $s;
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $d
	 */
	function rad($d) {
		return $d * 3.1415926535898 / 180.0;
	}
	/**
	 * 
	 * 时间差
	 * @param unknown_type $start
	 * @param unknown_type $end
	 */
	function datediff($start,$end){
		$remain="";
		$time=strtotime($end)-strtotime($start);
		if($time>0){
			$d=floor($time/60/60/24);
			$h=floor(($time-$d*60*24*60)/60/60);
			$h=$h<10?'0'.$h:$h;
			$m=floor(($time-$d*60*24*60-$h*60*60)/60);
			$m=$m<10?'0'.$m:$m;
			$s=$time-$d*60*24*60-$h*60*60-$m*60;
			$s=$s<10?'0'.$s:$s;
			$remain="$d|$h|$m|$s";
		}
		return $remain;
	}
}
?>