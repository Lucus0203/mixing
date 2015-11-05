<?php
define('APP_DIR', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);
require_once 'db.php';
$db = db::getInstance();

//过期咖啡通知
$notifysql="insert into ".DB_PREFIX."notify (user_id,img,send_time,msg,type,dataid,isread) select encouter.user_id,'http://www.xn--8su10a.com/img/default_head.png',date_format(NOW(),'%Y-%c-%d %H:%i:%s'),'有过期的咖啡需要处理','expired',encouter.id,1 from ".DB_PREFIX."encouter encouter where TIMESTAMPDIFF(DAY,encouter.created,now())>encouter.days and encouter.days!=0 and encouter.status=2 ";
$db->excuteSql($notifysql);

