<?php

require_once 'k3apiLib/common.php';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

	global $db;
	$data=$db->getAll('freecup');
	$act=@$_POST['act'];
	$nick_name=filterSql(@$_POST['nick_name']);
	$msg='';
	if($act=='getcode'){
		if($db->getCount("freecup",array('nick_name'=>$nick_name))>0){
			$code=$db->getRow("freecup",array('nick_name'=>$nick_name),array('code'));
			$msg="已领取过验证码:<br>".$code['code'];
		}else{
			$code=checkcode();
			$db->create('freecup',array('nick_name'=>$nick_name,'code'=>$code));
			$msg="验证码是:<br>".$code;
		}   
	}
	function checkcode(){
		global $db;
		$flag=1;
		$code='0000';
		while($flag){
			$code=rand(1000, 9999);
			if($db->getCount("freecup",array('code'=>$code))>0){
				continue;
			}else{
				$flag=0;
			}
		}
		return $code;
	}
?>
<html>
<body>
<?php echo $msg; ?>
<form action="" method="post">
<input type="hidden" name="act" value="getcode" />
<input type="text" name="nick_name" value="" />
</form>
</body>
</html>