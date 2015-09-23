<?php
//领取者领取成功发送消息
function sendNotifyMsgByReceive($receiveid) {
        global $db;
        $receive=$db->getRow('encouter_receive',array('id'=>$receiveid));
        //更新验证凭证
        $verifycode=encouterVerify('encouter_receive',$receive['from_user']);
        $receive['verifycode']=$verifycode;
        $db->update('encouter_receive',array('verifycode'=>$verifycode),array('id'=>$receiveid));
        //领取者
        $from = $db->getRow('user', array('id' => $receive['from_user']));
        //寄存者
        $to = $db->getRow('user', array('id' => $receive['to_user']));
        $type=$receive['type'];
        $encouter=$db->getRow('encouter', array('id' => $receive['encouter_id']));
        switch ($type) {
                case 1://爱心
                        if ($receive['from_user'] != $receive['to_user']) {
                                //发送环信消息
                                $HuanxinObj = Huanxin::getInstance();
                                $huserObj = $HuanxinObj->sendmsgToUser($from['mobile'], $to['mobile'], '我领到了你的咖啡,很高兴认识你~');
                                $huserObj = $HuanxinObj->sendmsgToUser($to['mobile'], $from['mobile'], '很高兴认识你~');
                        }
                        //发送短息
                        $shop = $db->getRow('shop', array('id' => $encouter['shop_id']));
                        $sms = new Sms();
                        $sms->sendMsg("您参与的爱心咖啡<" . $encouter['product1'] . ">验证码是:" . $receive['verifycode'] . ",请尽快到<" . $shop['title'] . ">领取!感谢是爱心的第一步~欢迎使用", $from['mobile']);
                        break;
                case 2://缘分 等待回复
                case 3://约会 等待回复
                       $IOSumeng=new Umeng('IOS');
                       $IOSumeng->sendIOSCustomizedcast("encouter", $encouter['user_id'], '有人想领取您的咖啡,等待您的回复',array('notify'=>'encouter'));
                        break;
                case 4://传递 必须寄存才可领
                        if ($receive['from_user'] != $receive['to_user']) {
                                //发送环信消息
                                $HuanxinObj = Huanxin::getInstance();
                                $huserObj = $HuanxinObj->sendmsgToUser($from['mobile'], $to['mobile'], '我领到了你的咖啡,很高兴认识你~');
                                $huserObj = $HuanxinObj->sendmsgToUser($to['mobile'], $from['mobile'], '很高兴认识你~');
                        }
                        //发送短息
                        $shop = $db->getRow('shop', array('id' => $encouter['shop_id']));
                        $sms = new Sms();
                        $sms->sendMsg("您参与的传递咖啡<" . $encouter['product1'] . ">验证码是:" . $receive['verifycode'] . ",请尽快到<" . $shop['title'] . ">领取!人生，就是去不断开启新的旅途~欢迎使用", $from['mobile']);
                        break;
                case 5://等候 必须寄存才可领
                        if ($receive['from_user'] != $receive['to_user']) {
                                //发送环信消息
                                $HuanxinObj = Huanxin::getInstance();
                                $huserObj = $HuanxinObj->sendmsgToUser($from['mobile'], $to['mobile'], '你等的咖啡我买了,很高兴认识你~');
                                $huserObj = $HuanxinObj->sendmsgToUser($to['mobile'], $from['mobile'], '我领到了你的咖啡,很高兴认识你~');
                        }
                        $db->update('encouter',array('verifycode'=>$verifycode),array('id'=>$receive['encouter_id']));
                        //发送短息
                        $shop = $db->getRow('shop', array('id' => $encouter['shop_id']));
                        $sms = new Sms();
                        $sms->sendMsg("您等候的咖啡来了,<" . $encouter['product1'] . ">验证码是:" . $receive['verifycode'] . ",请尽快到<" . $shop['title'] . ">领取!咖啡只是一个借口,我只愿遇见一个懂我的人~欢迎使用", $to['mobile']);
                        break;

                default:
                        break;
        }
        //互加好友
        makefriend($receiveid);
}

//寄存者授权成功发送消息
function sendNotifyMsgByPermiter($receiveid) {
        global $db;
        $receive=$db->getRow('encouter_receive',array('id'=>$receiveid));
        //更新验证凭证
        $verifycode=encouterVerify('encouter_receive',$receive['from_user']);
        $receive['verifycode']=$verifycode;
        $db->update('encouter_receive',array('verifycode'=>$verifycode),array('id'=>$receiveid));
        //寄存者
        $from = $db->getRow('user', array('id' => $receive['to_user']));
        //领取者
        $to = $db->getRow('user', array('id' => $receive['from_user']));
        $type=$receive['type'];
        $encouter=$db->getRow('encouter', array('id' => $receive['encouter_id']));
        switch ($type) {
                case 2://缘分
                        if ($receive['to_user'] != $receive['from_user']) {
                                //发送环信消息
                                $HuanxinObj = Huanxin::getInstance();
                                $huserObj = $HuanxinObj->sendmsgToUser($from['mobile'], $to['mobile'], '咖啡已经送达给你喽,很高兴认识你~');
                                $huserObj = $HuanxinObj->sendmsgToUser($to['mobile'], $from['mobile'], '谢谢你的咖啡,很高兴认识你~');
                        }
                        //发送短息
                        $shop = $db->getRow('shop', array('id' => $encouter['shop_id']));
                        $sms = new Sms();
                        $sms->sendMsg("您参与的缘分咖啡<" . $encouter['product1'] . ">验证码是:" . $receive['verifycode'] . ",请尽快到<" . $shop['title'] . ">领取!问答是一种形式，遇见是一次缘分~欢迎使用", $to['mobile']);
                        break;
                case 3://约会
                        if ($receive['to_user'] != $receive['from_user']) {
                                //发送环信消息
                                $HuanxinObj = Huanxin::getInstance();
                                $huserObj = $HuanxinObj->sendmsgToUser($from['mobile'], $to['mobile'], '咖啡已经送达给你喽,很期待认识你,不见不散~');
                                $huserObj = $HuanxinObj->sendmsgToUser($to['mobile'], $from['mobile'], '谢谢你的咖啡,很期待认识你,不见不散~');
                        }
                        //发送短息给领取者
                        $product_receive=($receive['choice_menu']==2)?$encouter['product2']:$encouter['product1'];//获取的咖啡
                        $shop = $db->getRow('shop', array('id' => $encouter['shop_id']));
                        $sms = new Sms();
                        $sms->sendMsg("您参与的约会咖啡<" . $product_receive . ">验证码是:" . $receive['verifycode'] . ",请尽快到<" . $shop['title'] . ">领取!邂逅一个人，可以温暖一生~欢迎使用", $to['mobile']);
                        //发送短息给寄存者
                        $product_deposit=($receive['choice_menu']==2)?$encouter['product1']:$encouter['product2'];//获取的咖啡
                        //寄存者验证码
                        $verifycode=encouterVerify('encouter',$receive['to_user']);
                        $db->update('encouter',array('verifycode'=>$verifycode),array('id'=>$receive['encouter_id']));
                        $sms->sendMsg("您参与的约会咖啡<" . $product_deposit . ">验证码是:" . $verifycode . ",请尽快到<" . $shop['title'] . ">领取!邂逅一个人，可以温暖一生~欢迎使用", $to['mobile']);
                        break;
                default:
                        break;
        }
        //互加好友
        makefriend($receiveid);
}

//领取凭证码
function encouterVerify($type='encouter_receive',$user){
        global $db;
        if($type=='encouter'){
                $num=$db->getCount('encouter', array('user_id' => $user));
        }else{
                $num=$db->getCount('encouter_receive', array('from_user' => $user));
        }
        return $num.$user.rand(10000, 99999);
}

//互加好友
function makefriend($receiveid){
        global $db;
        $receive=$db->getRow('encouter_receive',array('id'=>$receiveid));
        $from_user=$receive['from_user'];
        $to_user=$receive['to_user'];
	if($db->getCount('user_relation',array('user_id'=>$from_user,'relation_id'=>$to_user))==0){//没关注
		$nickname=$db->getRow('user',array('id'=>$to_user),array('nick_name','pinyin'));
                $rinfo=array('user_id'=>$from_user,'relation_id'=>$to_user,'status'=>1,'relation_name'=>$nickname['nick_name'],'relation_pinyin'=>$nickname['pinyin']);
		$db->create('user_relation', $rinfo);//关注
	}
	if($db->getCount('user_relation',array('user_id'=>$to_user,'relation_id'=>$from_user))==0){//没关注
		$nickname=$db->getRow('user',array('id'=>$from_user),array('nick_name','pinyin'));
                $rinfo=array('user_id'=>$to_user,'relation_id'=>$from_user,'status'=>1,'relation_name'=>$nickname['nick_name'],'relation_pinyin'=>$nickname['pinyin']);
		$db->create('user_relation', $rinfo);//关注
	}
        
        
}