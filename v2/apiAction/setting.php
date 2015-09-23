<?php
$act=filter($_REQUEST['act']);
switch ($act){
	case 'security':
		security();//安全隐私
		break;
	case 'allowFindByNick':
		allowFindByNick();//允许通过昵称搜索我
		break;
	case 'allowFindByMobile':
		allowFindByMobile();//允许通过手机号搜索我
		break;
	case 'allowSeeNews':
		allowSeeNews();//允许陌生人查看8条动态
		break;
	default:
		break;
}

//安全隐私
function security(){
    global $db;
    $loginid = filter($_REQUEST['loginid']);
    if(empty($loginid)){
            echo json_result(null,'2','请重新登录');
            return;
    }
    $security=$db->getRow('user',array('id'=>$loginid),array('allow_find_nick','allow_find_mobile','allow_news'));
    echo json_result(array('security'=>$security));
}

//允许通过昵称搜索我
function allowFindByNick(){
    global $db;
    $loginid = filter($_REQUEST['loginid']);
    $allow = filter($_REQUEST['allow']);//1允许2不允许
    if(empty($loginid)){
            echo json_result(null,'2','请重新登录');
            return;
    }
    $db->update('user',array('allow_find_nick'=>$allow),array('id'=>$loginid));
    
    echo json_result(array('success'=>'TRUE'));
    
}

//允许通过手机号搜索我
function allowFindByMobile(){
    global $db;
    $loginid = filter($_REQUEST['loginid']);
    $allow = filter($_REQUEST['allow']);//1允许2不允许
    if(empty($loginid)){
            echo json_result(null,'2','请重新登录');
            return;
    }
    $db->update('user',array('allow_find_mobile'=>$allow),array('id'=>$loginid));
    
    echo json_result(array('success'=>'TRUE'));
}

//允许陌生人查看8条动态
function allowSeeNews(){
    global $db;
    $loginid = filter($_REQUEST['loginid']);
    $allow = filter($_REQUEST['allow']);//1允许2不允许
    if(empty($loginid)){
            echo json_result(null,'2','请重新登录');
            return;
    }
    $db->update('user',array('allow_news'=>$allow),array('id'=>$loginid));
    
    echo json_result(array('success'=>'TRUE'));
}