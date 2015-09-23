<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_User extends FLEA_Db_TableDataGateway
{
	var $tableName = 'user';
	var $primaryKey = 'id';
}

?>