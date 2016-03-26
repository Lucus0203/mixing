<?php
//www.showapi.com 易源接口 kfyw021
class GetCityByMobile{
	var $_showapi_appid;
	var $_showapi_sign;
	function __construct() {
		$this->_showapi_appid="14315";
		$this->_showapi_sign="d90dce94c92d48cb81d3ee3379604520";
	}
	
	private function __clone() {
	}
        
        
	function getCity($mobile){
            $showapi_timestamp = date('YmdHis');
            $paramArr = array(
                 'showapi_appid'=> $this->_showapi_appid,
                 'num' => $mobile ,
                 'showapi_timestamp' => $showapi_timestamp
                // other parameter
            );
            $sign = $this->createSign($paramArr);
            $strParam = $this->createStrParam($paramArr);
            $strParam .= 'showapi_sign='.$sign;
            $url = 'http://route.showapi.com/6-1?'.$strParam;
            $result = file_get_contents($url);
            $result = json_decode($result);
            return $result;
        }
	
	

        function createSign ($paramArr) {
             $sign = "";
             ksort($paramArr);
             foreach ($paramArr as $key => $val) {
                 if ($key != '' && $val != '') {
                     $sign .= $key.$val;
                 }
             }
             $sign.=$this->_showapi_sign;
             $sign = strtoupper(md5($sign));
             return $sign;
        }
        function createStrParam ($paramArr) {
             $strParam = '';
             foreach ($paramArr as $key => $val) {
             if ($key != '' && $val != '') {
                     $strParam .= $key.'='.urlencode($val).'&';
                 }
             }
             return $strParam;
        }
        
        
}


 
?>
