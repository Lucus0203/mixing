<?php
ini_set('display_errors',0);
//session_set_cookie_params(1800); 
//ini_set('session.gc_maxlifetime',5);
//ini_set("max_execution_time", 6000);


define ('APP_DIR',dirname(__FILE__));
//框架引用

require('lib/FLEA/FLEA.php');
require('lib/Smarty/Smarty.class.php');
require('const.php');

//配置文件引用
FLEA::loadAppInf(dirname(__FILE__) .  DS . 'App' . DS . 'Config' . DS . 'config.php');

//程序代码目录引用
FLEA::import(dirname(__FILE__). DS . 'App');
FLEA::import(dirname(__FILE__). DS . 'lib');
//echo dirname(__FILE__);
date_default_timezone_set("Asia/ShangHai");
FLEA::runMVC();
?>
