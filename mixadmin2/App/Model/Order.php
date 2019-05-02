<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_Order extends FLEA_Db_TableDataGateway
{
	var $tableName = 'order';
	var $primaryKey = 'id';

}

?>