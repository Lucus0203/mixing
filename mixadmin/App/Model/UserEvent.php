<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_UserEvent extends FLEA_Db_TableDataGateway
{
	var $tableName = 'user_event';
	var $primaryKey = 'id';
}

?>