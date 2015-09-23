<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_Admin extends FLEA_Db_TableDataGateway
{
	var $tableName = 'admin';
	var $primaryKey = 'id';

}

?>