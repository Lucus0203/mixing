<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_AppMain extends FLEA_Db_TableDataGateway
{
	var $tableName = 'app_main';
	var $primaryKey = 'id';

}

?>