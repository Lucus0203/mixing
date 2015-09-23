<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_BaseUserTag extends FLEA_Db_TableDataGateway
{
	var $tableName = 'base_user_tag';
	var $primaryKey = 'id';

}

?>