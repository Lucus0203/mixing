<?php
return array(

	'dbDSN' => array(//the database config

		'driver' => 'mysql',

		'host' => 'rdsg15050y16nv5xags9.mysql.rds.aliyuncs.com',

		'login' => 'kfyw',

		'password' =>'kfyw6688',

		'database' => 'mixing',

		'prefix'=>'cofe_',

	),

	'view' => 'FLEA_View_Smarty',//the template config

    	'viewConfig' => array(

        'smartyDir'         => APP_DIR. DS . 'lib' . DS . 'Smarty',

        'template_dir'      => APP_DIR . DS . 'App' . DS . 'Template',

        'compile_dir'       => APP_DIR . DS . 'App' . DS . 'Templates_c',

        'left_delimiter'    => '{',

        'right_delimiter'   => '}',

        'caching'=>false,

    ),
    'internalCacheDir'=>APP_DIR. DS . 'lib' . DS . 'Cache',
    'dbMetaCached'=>true,
	'displayErrors'=>false
);

?>