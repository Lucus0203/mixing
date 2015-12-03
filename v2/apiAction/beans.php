<?php
require_once APP_DIR . DS . 'apiLib' . DS . 'constant_beans.php';
$act=filter($_REQUEST['act']);
switch ($act){
	case 'getLoginBeans':
		getLoginBeans();//登录获取豆子
		break;
	default:
		break;
}

//登录获取豆子
function getLoginBeans(){
        global $db;
        $num=BEANS_NUM_LOGIN;
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
        $sql="select id from ".DB_PREFIX."beans_log beans_log where created>='".date("Y-m-d")." 00:00:00' and created<='".date("Y-m-d")." 23:59:59' ";
        if($db->getCountBySql($sql)<=0){
            $beanlog=array('user_id'=>$loginid,'content'=>'每日登录获得豆子'.$num.'颗','num'=>$num,'type'=>1);
            $db->create('beans_log',$beanlog);
            //增加用户豆子
            $updateBeansSql="update ".DB_PREFIX."user user set beans=beans+".$num." where id = ".$loginid;
            $db->excuteSql($updateBeansSql);
        }
        echo json_result(array('success'=>'TRUE'));
}