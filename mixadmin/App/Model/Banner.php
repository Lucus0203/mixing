<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_Banner extends FLEA_Db_TableDataGateway
{
	var $tableName = 'banner';
	var $primaryKey = 'id';

}

?>