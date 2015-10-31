<?php
$act=filter($_REQUEST['act']);
switch ($act){
	case 'maindata':
		maindata();//数据
		break;
	default:
		break;
}

//首页数据
function maindata(){
	global $db;
        //首页图片文字
        $filed=array('title','img','note','type','dataid');
        if($db->getCount('app_main',array('status'=>1,'datetime'=>date("Y-m-d")))>0){
            $data=$db->getRow('app_main',array('status'=>1,'datetime'=>date("Y-m-d")),$filed,'id desc');
        }else{
            $data=$db->getRow('app_main',array('status'=>1),$filed,'id desc');
        }
        //寄存人数
        $sql="select id from ".DB_PREFIX."encouter where status>=1 and status <=8 ";
        $data['encounterCount']=$db->getCountBySql($sql);
        //领取人数
        $sql="select id from ".DB_PREFIX."encouter_receive where status=2 or status = 7 ";
        $data['receiveCount']=$db->getCountBySql($sql);
	echo json_result(array('data'=>$data));
}
