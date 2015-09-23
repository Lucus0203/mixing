<?php

ini_set('display_errors', 0);
header('Content-Type: application/json');
ini_set('date.timezone', 'Asia/Shanghai');
define('APP_DIR', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);
require_once APP_DIR . DS . 'apiLib' . DS . 'common.php';
$c = $_REQUEST ['c'];
if (!empty($c)) {

    switch ($c) {
        case 'index':
            include 'apiAction/index.php';
            break;
        case 'user':
            include 'apiAction/user.php';
            break;
        case 'publicEvent':
            include 'apiAction/public_event.php';
            break;
        case 'shop':
            include 'apiAction/shop.php';
            break;
        case 'userEvent':
            include 'apiAction/user_event.php';
            break;
        case 'contact':
            include 'apiAction/contact.php';
            break;
        case 'feedback':
            include 'apiAction/feedback.php';
            break;
        case 'sendmsg':
            include 'apiAction/sendmsg.php';
            break;
        case 'invitation':
            include 'apiAction/invitation.php';
            break;
        case 'base':
            include 'apiAction/base.php';
            break;
        case 'order':
            include 'apiAction/order.php';
            break;
        case 'encouter':
            include 'apiAction/encouter.php';
            break;
        case 'myCafe':
            include 'apiAction/my_cafe.php';
            break;
        case 'myOrder':
            include 'apiAction/my_order.php';
            break;
        case 'setting':
            include 'apiAction/setting.php';
            break;
        case 'diary':
            include 'apiAction/diary.php';
            break;
        default:
            break;
    }
}
?>