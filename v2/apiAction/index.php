<?php
$act=filter($_REQUEST['act']);
switch ($act){
	case 'banner':
		banner();//banner轮播
		break;
	default:
		break;
}

//附近咖啡
function banner(){
	global $db;
	$banners=$db->getAll('banner');
	echo json_result($banners);
}
