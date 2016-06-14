<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_Diary extends FLEA_Db_TableDataGateway
{
	var $tableName = 'diary';
	var $primaryKey = 'id';

}

?>