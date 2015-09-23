<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Common{
	
	public function getLngFromBaidu($address){
		$addr=array('lng'=>0,'lat'=>0);
		//获取经纬度
		$loc_json=file_get_contents("http://api.map.baidu.com/geocoder/v2/?address=".$address."&output=json&ak=".BAIDU_AK);
		$loc=json_decode($loc_json);
		if($loc->status==0){
			$addr['lng']=$loc->result->location->lng;
			$addr['lat']=$loc->result->location->lat;
		}
		return $addr;
	}
	
}