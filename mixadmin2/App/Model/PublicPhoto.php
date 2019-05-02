<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_PublicPhoto extends FLEA_Db_TableDataGateway
{
	var $tableName = 'public_photo';
	var $primaryKey = 'id';

}

?>