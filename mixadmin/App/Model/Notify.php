<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_Notify extends FLEA_Db_TableDataGateway
{
	var $tableName = 'notify';
	var $primaryKey = 'id';

}

?>