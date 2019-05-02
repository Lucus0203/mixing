<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_PublicEvent extends FLEA_Db_TableDataGateway
{
	var $tableName = 'public_event';
	var $primaryKey = 'id';

}

?>