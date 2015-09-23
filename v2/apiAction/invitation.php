<?php
require_once APP_DIR.DS.'apiLib'.DS.'ext'.DS.'Umeng.php';
$act=filter($_REQUEST['act']);
switch ($act){
	case 'sendInvitation':
		sendInvitation();//发送邀请函
		break;
	case 'getInvitation':
		getInvitation();//查看邀请函
		break;
	case 'acceptInvitation'://接受邀请函
		acceptInvitation();
		break;
	case 'refuseInvitation'://拒绝邀请函
		refuseInvitation();
		break;
	case 'invitationBySend'://我发出的
		invitationBySend();
		break;
	case 'invitationByAccept'://我接受的
		invitationByAccept();
		break;
	case 'cancelInvitation'://取消邀请函
		cancelInvitation();
		break;
	case 'delInvitation'://删除邀请函
		delInvitation();
		break;
	default:
		break;
}

//发送普通邀请函
function sendInvitation(){
	global $db;
	
	$userid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$to_userid=filter(!empty($_REQUEST['touserid'])?$_REQUEST['touserid']:'');
	$title=filterIlegalWord(!empty($_REQUEST['title'])?$_REQUEST['title']:'');
	$datetime=filter(!empty($_REQUEST['datetime'])?$_REQUEST['datetime']:'');
	$shopid=filter(!empty($_REQUEST['shopid'])?$_REQUEST['shopid']:'');
	$address=filter($_REQUEST['address']);
	$note=filter($_REQUEST['note']);
	$pay_type=filter($_REQUEST['pay_type']);//1我买单2请我吧3AA制

	//待接收邀约数
	$count=$db->getCount('invitation',array('status'=>1,'user_id'=>$userid));
	if($count>0){
		echo json_result(null,'2','您还有一个待对方接收的邀请');//是的,要取消,不,再耐心等等
		return;
	}
	
	//黑名单，对方暂不接受邀请
	$relation=$db->getRow('user_relation',array('user_id'=>$to_userid,'relation_id'=>$userid),array('status'));
	if(!empty($relation['status'])&&$relation['status']==2){
		echo json_result(null,'3','对方暂不接受邀请');//是的,要取消,不,再耐心等等
		return;
	}
	
	//isreaded 1已读 2未读
	$invitation=array('title'=>$title,'datetime'=>$datetime,'shop_id'=>$shopid,'address'=>$address,'note'=>$note,'pay_type'=>$pay_type,'user_id'=>$userid,'to_user_id'=>$to_userid,'isreaded_user'=>1,'isreaded_to_user'=>2,'status'=>1,'created'=>date("Y-m-d H:i:s"));
	$db->create('invitation', $invitation);
	
	$fromuser=$db->getRow('user',array('id'=>$userid),array('nick_name'));
	
// 	$Aumeng=new Umeng('Android');
// 	$Aumeng->sendAndroidCustomizedcast("invitation",$to_userid,"您有新的邀约","咖啡约我","新的邀请函","go_app","");//go_activity
	
 	$IOSumeng=new Umeng('IOS');
 	$IOSumeng->sendIOSCustomizedcast("invitation", $to_userid, '您有一条来自"'.$fromuser['nick_name'].'"的邀请函',array('notify'=>'invitation'));

	echo json_result(array('success'=>'TRUE'));

}

//查看邀请函
function getInvitation(){
	global $db;
	$invt_id=filter(!empty($_REQUEST['invtid'])?$_REQUEST['invtid']:'');//邀请函id
        $invt_type=filter(!empty($_REQUEST['invt_type'])?$_REQUEST['invt_type']:'');//邀请函类型1普通2活动
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');//登录者id
        if(empty($loginid)){
		echo json_result(null,'2','请重新登录');
		return;
	}
	if(empty($invt_id)||empty($invt_type)){
		echo json_result(null,'3','邀请函数据请求错误');
		return;
        }
        if($invt_type==1){//普通邀请函
            $sql="select invitation.id as inviteid,1 as invt_type,invitation.title,invitation.datetime,invitation.address,invitation.shop_id,invitation.pay_type,invitation.note,invitation.status,invitation.user_id,invitation.to_user_id,from_user.id as left_user_id,from_user.head_photo as left_head_photo,to_user.id as right_user_id,to_user.head_photo as right_head_photo from ".DB_PREFIX."invitation invitation "
                    . "left join ".DB_PREFIX."user from_user on from_user.id=invitation.user_id "
                    . "left join ".DB_PREFIX."user to_user on to_user.id=invitation.to_user_id "
                    . "where invitation.id=$invt_id ";
            $invitation=$db->getRowBySql($sql);
            if($invitation['user_id']==$loginid){
                    $db->update('invitation', array('isreaded_user'=>1),array('id'=>$invt_id));
            }
            unset($invitation['user_id']);
            if($invitation['to_user_id']==$loginid){
                    $db->update('invitation', array('isreaded_to_user'=>1),array('id'=>$invt_id));
            }
            unset($invitation['to_user_id']);
        }else{//活动邀请函
            $sql="select invitation.id as inviteid,2 as invt_type,invitation.title,invitation.datetime,invitation.address,invitation.lng,invitation.lat,invitation.public_event_id as event_id,invitation.pay_type,invitation.note,invitation.status,invitation.user_id,invitation.other_id,from_user.id as left_user_id,from_user.head_photo as left_head_photo,to_user.id as right_user_id,to_user.head_photo as right_head_photo from ".DB_PREFIX."public_event_together_others invitation "
                    . "left join ".DB_PREFIX."user from_user on from_user.id=invitation.other_id "
                    . "left join ".DB_PREFIX."user to_user on to_user.id=invitation.user_id "
                    . "where invitation.id=$invt_id ";
            $invitation=$db->getRowBySql($sql);
            if($invitation['user_id']==$loginid){
                    $db->update('public_event_together_others', array('isreaded_user'=>1),array('id'=>$invt_id));
            }
            unset($invitation['user_id']);
            if($invitation['other_id']==$loginid){
                    $db->update('public_event_together_others', array('isreaded_other'=>1),array('id'=>$invt_id));
            }
            unset($invitation['other_id']);
        }
        
	$res['invitation']=$invitation;
	
	echo json_result($res);
	
}

//接受
function acceptInvitation(){
	global $db;
	$invt_id=filter(!empty($_REQUEST['invtid'])?$_REQUEST['invtid']:'');//邀请函id
        $invt_type=filter(!empty($_REQUEST['invt_type'])?$_REQUEST['invt_type']:'');//邀请函类型1普通2活动
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');//登录者id
        $user=$db->getRow('user',array('id'=>$loginid),array('nick_name'));
        if($invt_type==1){//普通邀请函
            if ($db->getCount('invitation',array('id'=>$invt_id,'to_user_id'=>$loginid))<=0){
                    echo json_result(null,'2','数据不符,您不能接受不属于您的邀请函');
                    return;
            }
            $db->update('invitation', array('isreaded_user'=>2,'isreaded_to_user'=>1,'status'=>2),array('id'=>$invt_id));
            //通知
            $inv=$db->getRow('invitation',array('id'=>$invt_id),array('user_id'));
            $IOSumeng=new Umeng('IOS');
            $IOSumeng->sendIOSCustomizedcast("invitation", $inv['user_id'], '"'.$user['nick_name'].'"接受了您的邀请函',array('notify'=>'invitation'));
        }else{//活动邀请函
            if ($db->getCount('public_event_together_others',array('id'=>$invt_id,'user_id'=>$loginid))<=0){
                    echo json_result(null,'2','数据不符,您不能接受不属于您的邀请函');
                    return;
            }
            $db->update('public_event_together_others', array('isreaded_user'=>1,'isreaded_other'=>2,'status'=>2),array('id'=>$invt_id));
            //通知
            $inv=$db->getRow('public_event_together_others',array('id'=>$invt_id),array('other_id'));
            $IOSumeng=new Umeng('IOS');
            $IOSumeng->sendIOSCustomizedcast("invitation", $inv['other_id'], '"'.$user['nick_name'].'"接受了您的活动邀请',array('notify'=>'invitation'));
        }
 	echo json_result(array('success'=>'TRUE'));
	
}

//拒绝
function refuseInvitation(){
	global $db;
	$invt_id=filter(!empty($_REQUEST['invtid'])?$_REQUEST['invtid']:'');//邀请函id
        $invt_type=filter(!empty($_REQUEST['invt_type'])?$_REQUEST['invt_type']:'');//邀请函类型1普通2活动
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');//登录者id
        $user=$db->getRow('user',array('id'=>$loginid),array('nick_name'));
        if($invt_type==1){//普通邀请函
            if ($db->getCount('invitation',array('id'=>$invt_id,'to_user_id'=>$loginid))<=0){
                    echo json_result(null,'2','数据不符,您不能拒绝不属于您的邀请函');
                    return;
            }
            $db->update('invitation', array('isreaded_user'=>2,'isreaded_to_user'=>1,'status'=>3),array('id'=>$invt_id));
            //通知
            $inv=$db->getRow('invitation',array('id'=>$invt_id),array('user_id'));
            $IOSumeng=new Umeng('IOS');
            $IOSumeng->sendIOSCustomizedcast("invitation", $inv['user_id'], '"'.$user['nick_name'].'"拒绝了您的邀请函',array('notify'=>'invitation'));
        }else{//活动邀请函
            if ($db->getCount('public_event_together_others',array('id'=>$invt_id,'user_id'=>$loginid))<=0){
                    echo json_result(null,'2','数据不符,您不能接受不属于您的邀请函');
                    return;
            }
            $db->update('public_event_together_others', array('isreaded_user'=>1,'isreaded_other'=>2,'status'=>3),array('id'=>$invt_id));
            //通知
            $inv=$db->getRow('public_event_together_others',array('id'=>$invt_id),array('other_id'));
            $IOSumeng=new Umeng('IOS');
            $IOSumeng->sendIOSCustomizedcast("invitation", $inv['other_id'], '"'.$user['nick_name'].'"拒绝了您的活动邀请',array('notify'=>'invitation'));
        }
	
	
	echo json_result(array('success'=>'TRUE'));
	
}

//取消邀请函
function cancelInvitation(){
	global $db;
	$invt_id=filter(!empty($_REQUEST['invtid'])?$_REQUEST['invtid']:'');//邀请函id
        $invt_type=filter(!empty($_REQUEST['invt_type'])?$_REQUEST['invt_type']:'');//邀请函类型1普通2活动
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');//登录者id
        if(empty($loginid)){
                echo json_result(null,'2','请重新登录');
                return;
        }
        if($invt_type==1){//普通邀请函
            if($db->getCount('invitation',array('id'=>$invt_id,'user_id'=>$loginid))<=0){
                    echo json_result(null,'3','请选择您发出的邀请函');
                    return;
            }
            $db->update('invitation', array('status'=>4),array('id'=>$invt_id,'user_id'=>$loginid));
        }else{//活动邀请函
            if ($db->getCount('public_event_together_others',array('id'=>$invt_id,'other_id'=>$loginid))<=0){
                    echo json_result(null,'3','请选择您发出的邀请函');
                    return;
            }
            $db->update('public_event_together_others', array('status'=>4),array('id'=>$invt_id,'other_id'=>$loginid));
        }
	echo json_result(array('success'=>'TRUE'));
}

//删除邀请函邀请函
function delInvitation(){
	global $db;
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');//登录者id
	$invt_id=filter(!empty($_REQUEST['invtid'])?$_REQUEST['invtid']:'');//邀请函id
        $invt_type=filter(!empty($_REQUEST['invt_type'])?$_REQUEST['invt_type']:'');//邀请函类型1普通2活动
	if(empty($loginid)){
		echo json_result(null,'2','请重新登录');
		return;
	}
        if(empty($invt_id)||empty($invt_type)){
		echo json_result(null,'3','邀请函数据请求错误');
		return;
        }
        if($invt_type==1){
            $inv=$db->getRow('invitation',array('id'=>$invt_id),array('status','user_id','to_user_id'));
            $touser=$db->getRow('user',array('id'=>$inv['to_user_id']),array('nick_name'));
            //发起者删除
            $condition=array('id'=>$invt_id,'user_id'=>$loginid);
            $count=$db->getCount('invitation',$condition);
            if($count>0){
                    $data=array('del_user'=>'1');
                    if($inv['status']==1&&$inv['isreaded_to_user']==2){
                            echo json_result(null,'4','请等待对方回应或取消');
                            return;
                    }elseif($inv['status']==1){
                            $data['status']=4;
                    }
                    $condition=array('id'=>$invt_id,'user_id'=>$loginid);
                    $db->update('invitation', $data,$condition);
            }
            //接受者删除
            $condition=array('id'=>$invt_id,'to_user_id'=>$loginid);
            $count=$db->getCount('invitation',$condition);
            if($count>0){
                    $data=array('del_to_user'=>'1');
                    if($inv['status']==1){
                            $data['status']=3;
                            $IOSumeng=new Umeng('IOS');
                            $IOSumeng->sendIOSCustomizedcast("invitation", $inv['user_id'], '"'.$touser['nick_name'].'"拒绝了您的邀请函',array('notify'=>'invitation'));

                    }
                    $condition=array('id'=>$invt_id,'to_user_id'=>$loginid);
                    $db->update('invitation', $data , $condition);
            }
        }else{
            $inv=$db->getRow('public_event_together_others',array('id'=>$invt_id),array('status','user_id','other_id'));
            $touser=$db->getRow('user',array('id'=>$inv['user_id']),array('nick_name'));
            //发起者删除
            $condition=array('id'=>$invt_id,'other_id'=>$loginid);
            $count=$db->getCount('public_event_together_others',$condition);
            if($count>0){
                    $data=array('del_other'=>'1');
                    if($inv['status']==1&&$inv['isreaded_user']==2){
                            echo json_result(null,'4','请等待对方回应或取消');
                            return;
                    }elseif($inv['status']==1){
                            $data['status']=4;
                    }
                    $condition=array('id'=>$invt_id,'other_id'=>$loginid);
                    $db->update('public_event_together_others', $data,$condition);
            }
            //接受者删除
            $condition=array('id'=>$invt_id,'user_id'=>$loginid);
            $count=$db->getCount('public_event_together_others',$condition);
            if($count>0){
                    $data=array('del_user'=>'1');
                    if($inv['status']==1){
                            $data['status']=3;
                            $IOSumeng=new Umeng('IOS');
                            $IOSumeng->sendIOSCustomizedcast("invitation", $inv['other_id'], '"'.$touser['nick_name'].'"拒绝了您的邀请函',array('notify'=>'invitation'));

                    }
                    $condition=array('id'=>$invt_id,'user_id'=>$loginid);
                    $db->update('public_event_together_others', $data , $condition);
            }
        }
	echo json_result(array('success'=>'TRUE'));
}


//我发出的邀请函
function invitationBySend(){
	global $db;
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');//登录者id
	$page_no = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : 1;
	$page_size = 10;
	$start = ($page_no - 1) * $page_size;
	
	if(empty($loginid)){
		echo json_result(null,'2','请重新登录');
		return;
	}
        //1等待2接受3拒绝4取消
	$sql1="select inv.id as invt_id,1 as invt_type,inv.to_user_id,tu.nick_name as to_nick_name,tu.head_photo as to_head_photo,inv.status,inv.isreaded_user as isreaded,inv.isreaded_to_user as to_isreaded,inv.created from ".DB_PREFIX."invitation inv 
                left join ".DB_PREFIX."user u on inv.user_id = u.id 
                left join ".DB_PREFIX."user tu on inv.to_user_id = tu.id where 1=1 "
                . " and inv.user_id=$loginid and inv.del_user <> '1' ";
        
        $sql2="select pto.id as invt_id,2 as invt_type,pto.user_id as to_user_id,p_tu.nick_name as to_nick_name,p_tu.head_photo as to_head_photo,pto.status,pto.isreaded_other as isreaded,pto.isreaded_user as to_isreaded,pto.created from ".DB_PREFIX."public_event_together_others pto "
                . "left join ".DB_PREFIX."user p_u on pto.other_id = p_u.id "
                . "left join ".DB_PREFIX."user p_tu on pto.user_id = p_tu.id where 1=1 "
                . " and pto.other_id=$loginid and pto.del_other <> '1' ";
        $sql="select * from ( $sql1 union all $sql2 ) s order by created desc limit $start,$page_size";
	$data=$db->getAllBySql($sql);
	echo json_result(array('list'=>$data));
}

//我接受的邀请函
function invitationByAccept(){
	global $db;
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');//登录者id
	$page_no = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : 1;
	$page_size = 10;
	$start = ($page_no - 1) * $page_size;
	
	if(empty($loginid)){
		echo json_result(null,'2','请重新登录');
		return;
	}
        //1等待2接受3拒绝4取消
	$sql1="select inv.id as invt_id,1 as invt_type,inv.user_id as from_user_id,u.nick_name as from_nick_name,u.head_photo as from_head_photo,inv.status,inv.isreaded_user as from_isreaded,inv.isreaded_to_user as isreaded,inv.created from ".DB_PREFIX."invitation inv "
                . "left join ".DB_PREFIX."user u on inv.user_id = u.id "
                . "left join ".DB_PREFIX."user tu on inv.to_user_id = tu.id where 1=1 "
                . " and inv.to_user_id=$loginid and inv.del_to_user <> '1' ";
        
        $sql2="select pto.id as invt_id,2 as invt_type,pto.other_id as from_user_id,p_u.nick_name as from_nick_name,p_u.head_photo as from_head_photo,pto.status,pto.isreaded_other as from_isreaded,pto.isreaded_user as isreaded,pto.created from ".DB_PREFIX."public_event_together_others pto "
                . "left join ".DB_PREFIX."user p_u on pto.other_id = p_u.id "
                . "left join ".DB_PREFIX."user p_tu on pto.user_id = p_tu.id where 1=1 "
                . " and pto.user_id=$loginid and pto.del_user <> '1' ";
        $sql="select * from ( $sql1 union all $sql2 ) s order by created desc limit $start,$page_size";
	$data=$db->getAllBySql($sql);
	echo json_result(array('list'=>$data));
	
}

