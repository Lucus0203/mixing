<?php
require_once APP_DIR.DS.'apiLib'.DS.'ext'.DS.'Umeng.php';
	
//活动邀请通知
function publicEventTogether($fromid,$toid,$dataid){
    global $db;
    $fromuser=$db->getRow('user',array('id'=>$fromid));
    $touser=$db->getRow('user',array('id'=>$toid));
    $msg='您有一条来自"'.$fromuser['nick_name'].'"的搭伴邀请';
    
    $sendObj=array('fromuser'=>$fromuser,'touser'=>$touser,'msg'=>$msg,'dataid'=>$dataid);
    $sendObj['type']='eventInvitation';
    sendNotify($sendObj);
        
}
//活动邀请通知被接受
function publicEventTogetherAccept($fromid,$toid,$dataid){
    global $db;
    $fromuser=$db->getRow('user',array('id'=>$fromid));
    $touser=$db->getRow('user',array('id'=>$toid));
    $msg=$fromuser['nick_name'].'"接受了您的搭伴邀请';
    
    $sendObj=array('fromuser'=>$fromuser,'touser'=>$touser,'msg'=>$msg,'dataid'=>$dataid);
    $sendObj['type']='eventInvitation';
    sendNotify($sendObj);
}
//活动邀请通知被拒绝
function publicEventTogetherRefuse($fromid,$toid,$dataid){
    global $db;
    $fromuser=$db->getRow('user',array('id'=>$fromid));
    $touser=$db->getRow('user',array('id'=>$toid));
    $msg=$fromuser['nick_name'].'"接受了您的搭伴邀请';
    
    $sendObj=array('fromuser'=>$fromuser,'touser'=>$touser,'msg'=>$msg,'dataid'=>$dataid);
    $sendObj['type']='eventInvitation';
    sendNotify($sendObj);
}

//发送普通邀请函
function sendNotifyInvitation($fromid,$toid,$dataid){
    global $db;
    $fromuser=$db->getRow('user',array('id'=>$fromid));
    $touser=$db->getRow('user',array('id'=>$toid));
    $msg=$fromuser['nick_name'].'"拒绝了您的搭伴邀请';
    
    $sendObj=array('fromuser'=>$fromuser,'touser'=>$touser,'msg'=>$msg,'dataid'=>$dataid);
    $sendObj['type']='invitation';
    sendNotify($sendObj);
}

//普通邀请函被接受
function sendNotifyInvitationAccept($fromid,$toid,$dataid){
    global $db;
    $fromuser=$db->getRow('user',array('id'=>$fromid));
    $touser=$db->getRow('user',array('id'=>$toid));
    $msg=$fromuser['nick_name'].'"接受了您的邀请函';
    
    $sendObj=array('fromuser'=>$fromuser,'touser'=>$touser,'msg'=>$msg,'dataid'=>$dataid);
    $sendObj['type']='invitation';
    sendNotify($sendObj);
}
//普通邀请函被拒绝
function sendNotifyInvitationRefuse($fromid,$toid,$dataid){
    global $db;
    $fromuser=$db->getRow('user',array('id'=>$fromid));
    $touser=$db->getRow('user',array('id'=>$toid));
    $msg=$fromuser['nick_name'].'"拒绝了您的邀请函';
    
    $sendObj=array('fromuser'=>$fromuser,'touser'=>$touser,'msg'=>$msg,'dataid'=>$dataid);
    $sendObj['type']='invitation';
    sendNotify($sendObj);
}

//发送通知给领取者
function sendNotifyToReceiver($fromid,$toid,$dataid,$msg){
    global $db;
    $fromuser=$db->getRow('user',array('id'=>$fromid));
    $touser=$db->getRow('user',array('id'=>$toid));
    $msg=empty($msg)?'你领取到了"'.$fromuser['nick_name'].'"的咖啡':$msg;
    
    $sendObj=array('fromuser'=>$fromuser,'touser'=>$touser,'msg'=>$msg,'dataid'=>$dataid);
    $sendObj['type']='receiver';
    sendNotify($sendObj);
}
//发送通知给寄存者
function sendNotifyToDepositer($fromid,$toid,$dataid,$msg){
    global $db;
    $fromuser=$db->getRow('user',array('id'=>$fromid));
    $touser=$db->getRow('user',array('id'=>$toid));
    $msg=empty($msg)?'"'.$fromuser['nick_name'].'"领取了你的咖啡':$msg;
    
    $sendObj=array('fromuser'=>$fromuser,'touser'=>$touser,'msg'=>$msg,'dataid'=>$dataid);
    $sendObj['type']='depositer';
    sendNotify($sendObj);
}

// 	$Aumeng=new Umeng('Android');
// 	$Aumeng->sendAndroidCustomizedcast("invitation",$to_userid,"您有新的邀约","搅拌","新的邀请函","go_app","");//go_activity
//发送消息并建立通知数据
function sendNotify($sendObj){
    global $db;
    $fromuser=$sendObj['fromuser'];
    $touser=$sendObj['touser'];
    $msg=$sendObj['msg'];
    $type=$sendObj['type'];
    $dataid=$sendObj['dataid'];
    $IOSumeng=new Umeng('IOS');
    $IOSumeng->sendIOSCustomizedcast("username", $touser['user_name'], $msg,array('notify'=>$type));
    
    $notify=array('user_id'=>$touser['id'],'img'=>$fromuser['head_photo'],'send_time'=>date("Y-m-d H:i:s"),'msg'=>$msg,'type'=>$type,'dataid'=>$dataid,'isread'=>1);
    $db->create('notify',$notify);
    
}