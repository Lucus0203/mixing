<?php
class Controller_Api extends FLEA_Controller_Action {
	/**
	 *
	 * Enter description here ...
	 * @var Class_Common
	 */
	var $_common;
	var $_admin;
	var $_adminid;
	var $_address_city;
	var $_address_province;
	var $_address_town;
	var $_shop_addcity;
	var $_shop_addarea;
	var $_shop_addcircle;

	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );

		$this->_admin = get_singleton ( "Model_Admin" );
		$this->_address_city = get_singleton ( "Model_AddressCity" );
		$this->_address_province = get_singleton ( "Model_AddressProvince" );
		$this->_address_town = get_singleton ( "Model_AddressTown" );
		$this->_shop_addcity = get_singleton ( "Model_ShopAddcity" );
		$this->_shop_addarea = get_singleton ( "Model_ShopAddarea" );
		$this->_shop_addcircle = get_singleton ( "Model_ShopAddcircle" );
		$this->_adminid = isset ( $_SESSION ['loginuserid'] ) ? $_SESSION ['loginuserid'] : "";
		if(empty($_SESSION ['loginuserid'])){
			$url=url("Default","Login");
			redirect($url);
		}
	}

	//获取城市
	function actionGetCityByProvince() {
		$province_id = isset ( $_GET ['province_id'] ) ? $this->_common->filter($_GET ['province_id']) : '';
		$prov=$this->_address_province->findByField('id',$province_id);
		$city=$this->_address_city->findAll(array('provinceCode'=>$prov['code']),'id asc',null,array('id','name'));
		$str="";
		foreach ($city as $c){
			$str.="<option value='".$c['id']."'>".$c['name']."</option>";
		}
		echo $str;
		exit();
	}
	
	//获取区县
	function actionGetTownByCity(){
		$city_id = isset ( $_GET ['city_id'] ) ? $this->_common->filter($_GET ['city_id']) : '';
		$ctow=$this->_address_city->findByField('id',$city_id);
		$towns=$this->_address_town->findAll(array('cityCode'=>$ctow['code']));
		$str="";
		foreach ($towns as $t){
			$str.="<option value='".$t['id']."'>".$t['name']."</option>";
		}
		echo $str;
		exit();
	}
        
        //获取咖啡店城市
	function actionGetShopCityByProvince() {
		$province_id = isset ( $_GET ['province_id'] ) ? $this->_common->filter($_GET ['province_id']) : '';
		//$prov=$this->_address_province->findByField('id',$province_id);
		$city=$this->_shop_addcity->findAll(array('province_id'=>$province_id),'id asc');
		$str="";
		foreach ($city as $c){
			$str.="<option value='".$c['id']."'>".$c['name']."(".$c['code'].")</option>";
		}
		echo $str;
		exit();
	}
        
        //获取咖啡店区域
	function actionGetShopAreaByCity() {
		$city_id = isset ( $_GET ['city_id'] ) ? $this->_common->filter($_GET ['city_id']) : '';
		//$prov=$this->_address_province->findByField('id',$province_id);
		$area=$this->_shop_addarea->findAll(array('city_id'=>$city_id),'id asc');
		$str="";
		foreach ($area as $c){
			$str.="<option value='".$c['id']."'>".$c['name']."</option>";
		}
		echo $str;
		exit();
	}
        //获取咖啡店商圈
	function actionGetShopCircleByCity() {
		$area_id = isset ( $_GET ['area_id'] ) ? $this->_common->filter($_GET ['area_id']) : '';
		//$prov=$this->_address_province->findByField('id',$province_id);
		$circle=$this->_shop_addcircle->findAll(array('area_id'=>$area_id),'id asc');
		$str="";
		foreach ($circle as $c){
			$str.="<option value='".$c['id']."'>".$c['name']."</option>";
		}
		echo $str;
		exit();
	}
        
	
}