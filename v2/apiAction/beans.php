<?php
require_once APP_DIR . DS . 'apiLib' . DS . 'constant_beans.php';
$act=filter($_REQUEST['act']);
switch ($act){
	case 'getLoginBeans':
		getLoginBeans();//登录获取豆子
		break;
        case 'shopRegist':
                shopRegist();//店铺签到
                break;
        case 'userBeansCount':
                userBeansCount();//用户拥有豆子
                break;
	default:
		break;
}

//登录获取豆子
function getLoginBeans(){
        global $db;
        $num=BEANS_NUM_LOGIN;
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
        $sql="select id from ".DB_PREFIX."beans_log beans_log where user_id=$loginid and type=1 and created>='".date("Y-m-d")." 00:00:00' and created<='".date("Y-m-d")." 23:59:59' ";
        if($db->getCountBySql($sql)<=0){
            $beanlog=array('user_id'=>$loginid,'content'=>'每日登录获得豆子'.$num.'颗','num'=>$num,'type'=>1);
            $db->create('beans_log',$beanlog);
            //增加用户豆子
            $updateBeansSql="update ".DB_PREFIX."user user set beans=beans+".$num." where id = ".$loginid;
            $db->excuteSql($updateBeansSql);
        }
        echo json_result(array('success'=>'TRUE'));
}

//店铺签到
function shopRegist(){
        global $db;
        $num=BEANS_NUM_SHOPREGIST;
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$shopid=filter(!empty($_REQUEST['shopid'])?$_REQUEST['shopid']:'');
	$lng=filter(!empty($_REQUEST['lng'])?$_REQUEST['lng']:'');
	$lat=filter(!empty($_REQUEST['lat'])?$_REQUEST['lat']:'');
        $shopinfo=$db->getRow('shop',array('id'=>$shopid),array('id','lng','lat'));
        if(empty($loginid)){
		echo json_result(null,'2','请先登录');
		return;
	}
        if(empty($shopid)){
		echo json_result(null,'3','请选择要签到的店');
		return;
        }
        if(empty($lat)||empty($lng)){
		echo json_result(null,'4','未获取到您现在的位置');
		return;
        }
        if(getDistance($lat, $lng, $shopinfo['lat'], $shopinfo['lng'])>100){
		echo json_result(null,'5','您距离这家咖啡馆有点远');
		return;
        }
        $sql="select id from ".DB_PREFIX."beans_log beans_log where user_id=$loginid and shop_id=$shopid and type=2 and created>='".date("Y-m-d")." 00:00:00' and created<='".date("Y-m-d")." 23:59:59' ";
        if($db->getCountBySql($sql)<=0){
            $beanlog=array('user_id'=>$loginid,'shop_id'=>$shopid,'content'=>'签到获取豆子'.$num.'颗','num'=>$num,'type'=>2);
            $db->create('beans_log',$beanlog);
            //增加用户豆子
            $updateBeansSql="update ".DB_PREFIX."user user set beans=beans+".$num." where id = ".$loginid;
            $db->excuteSql($updateBeansSql);
        }
        echo json_result(array('success'=>'TRUE'));
}

//用户拥有的豆子
function userBeansCount(){
        global $db;
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
        if(empty($loginid)){
		echo json_result(null,'2','请先登录');
		return;
	}
        $beans=$db->getRow('user',array('id'=>$loginid),array('beans'));
        echo json_result(array('num'=>$beans['beans']));
}