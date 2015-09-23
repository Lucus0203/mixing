<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_Shop extends FLEA_Db_TableDataGateway
{
	var $tableName = 'shop';
	var $primaryKey = 'id';

}

?>