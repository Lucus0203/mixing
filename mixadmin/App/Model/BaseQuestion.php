<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_BaseQuestion extends FLEA_Db_TableDataGateway
{
	var $tableName = 'base_question';
	var $primaryKey = 'id';

}

?>