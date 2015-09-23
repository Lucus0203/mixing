<?php
FLEA::loadClass('FLEA_Db_TableDataGateway');
class Model_ShopMaster extends FLEA_Db_TableDataGateway
{
	var $tableName = 'shop_master';
	var $primaryKey = 'id';

}

?>