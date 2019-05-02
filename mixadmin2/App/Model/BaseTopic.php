<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_BaseTopic extends FLEA_Db_TableDataGateway
{
	var $tableName = 'base_topic';
	var $primaryKey = 'id';

}

?>