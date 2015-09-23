<?php
define ('DEFAUT_TITLE',"搅拌管理后台");
define('SERVERROOT',dirname(__FILE__) );
$httpsflag=isset($_SERVER['HTTPS'])?$_SERVER['HTTPS']:"";
if(empty($httpsflag)){
	$urlprefix="http://";
}else{
	$urlprefix="http://";
}
define('APP_SITE', 'http://v2.mixing.win/');
define('SITE',$urlprefix.$_SERVER['HTTP_HOST'].substr($_SERVER['PHP_SELF'],0,strrpos ($_SERVER['PHP_SELF'],'/')+1));
define('BAIDU_AK', 'ho6LXkYw6eWBzWFlPvcMpLhR');

?>