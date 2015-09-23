<?php
require_once APP_DIR . DS . 'apiLib' . DS . 'ext' . DS . 'Pingxx' . DS . 'init.php';
require_once APP_DIR . DS . 'apiLib' . DS . 'ext' . DS . 'Huanxin.php';
require_once APP_DIR . DS . 'apiLib' . DS . 'ext' . DS . 'Sms.php';
require_once APP_DIR . DS . 'apiAction' . DS . 'encouter_notifymsg.php';//通知消息
$act = filter($_REQUEST['act']);
switch ($act) {
        case 'pay':
                pay(); //同步ping++支付,创建订单
                break;
        case 'refund':
                refund(); //同步ping++支付,申请退款
                break;
        case 'secondPay':
                secondPay();
                break;
        case 'webhooks':
                webhooks(); //ping++通知更新订单状态
                break;
        default:
                break;
}

//同步ping++支付,创建订单
function pay() {
        global $db;
        $input_data = json_decode(file_get_contents('php://input'), true);
        //$input_data['channel']='alipay';//wx
        //$input_data['amount']=101;
        $channel = !empty($input_data['channel']) ? strtolower(filter($input_data['channel'])) : '';
        $encouterid = !empty($input_data['encouterid']) ? filter($input_data['encouterid']) : '';
        $receiveid = !empty($input_data['receiveid']) ? filter($input_data['receiveid']) : '';
        $loginid = !empty($input_data['loginid']) ? filter($input_data['loginid']) : '';
        $encouter = $db->getRow('encouter', array('id' => $encouterid));
        $shop = $db->getRow('shop', array('id' => $encouter['shop_id']));
        if (empty($channel)) {
                echo json_result(null, '110', '请选择支付方式');return;
                
        }
        if (empty($encouterid)) {
                echo json_result(null, '111', '支付对象丢失');return;
                
        }
        //$encouter['status'] 1待付款 2待领取 3待到店领取 4已领走 5等候待付款 6等候待到店领取 7等候已领走
        //$receive['status']  1等待回复 2可领取 3被拒绝 4传递待支付 5传递已支付 6等候待支付 7等候已支付
        $orderNo = $loginid . time();
        $orderNo = $channel == 'alipay' ? '01' . $orderNo : '02' . $orderNo;
        switch ($encouter['type']) {
                case 1://爱心
                case 2://缘分
                case 3://约会
                        if($encouter['status']!=1){
                                echo json_result(null, '203', '您的订单无需再支付');return;
                        }
                        break;
                case 4://传递
                        if(empty($encouter['prev_encouter_id'])){
                                if($encouter['status']!=1){
                                        echo json_result(null, '203', '您的订单无需再支付');return;
                                }  
                        }else{
                                $prev_encouter=$db->getRow('encouter',array('id'=>$encouter['prev_encouter_id']));
                                if($prev_encouter['paylock']!=1){
                                        $remenus=floor((time()-strtotime($prev_encouter['updated'])) / 60);
                                        if($remenus<3){//3分钟锁定
                                                echo json_result(null, '204', '这杯咖啡正在等待他人操作,请稍后再来尝试');return;
                                        }
                                }
                                if($prev_encouter['status']!=2){
                                        echo json_result(null, '205', '很抱歉您晚了一步,这杯咖啡已由他人接力');return;
                                }
                                $db->update('encouter',array('paylock'=>2,'updated'=>date("Y-m-d H:i:s")),array('id'=>$encouter['prev_encouter_id']));//锁定支付
                        }
                        break;
                case 5://等候
                        if($db->getCount('encouter_receive',array('id'=>$receiveid,'from_user'=>$loginid))==0){
                                echo json_result(null, '208', '很抱歉您还未领取这杯咖啡,请重新领取');return;
                        }
                        if($encouter['paylock']!=1){
                                $remenus=floor((time()-strtotime($encouter['updated'])) / 60);
                                if($remenus<3){//3分钟锁定
                                        echo json_result(null, '206', '这杯咖啡正在等待他人操作,请稍后再来尝试');return;
                                }
                        }
                        if($encouter['status']!=5){
                                $paid_order=$db->getRow('order',array('encouter_id'=>$encouterid,'paid'=>1));
                                if($paid_order['user_id']!=$loginid){
                                        echo json_result(null, '207', '很抱歉您晚了一步,这杯咖啡已由他人买单');return;
                                }else{
                                        echo json_result(null, '203', '您的订单无需再次支付');return;
                                }
                        }
                        $db->update('encouter',array('paylock'=>2,'updated'=>date("Y-m-d H:i:s")),array('id'=>$encouterid));//锁定支付
                        break;
                default:
                        break;
        }
        $menus = array();
        $menubody = filterSql($shop['title']);
        $totalamount = 0;
        $product = filterSql($encouter['product1']);
        $price = $encouter['price1'];
        $product.=empty($encouter['product2']) ? '' : ',' . filterSql($encouter['product2']);
        $price+=empty($encouter['price2']) ? 0 : $encouter['price2'] * 1;
        $menubody .= '(' . $product . ')';
        $totalamount = $price * 100;
        //$extra 在使用某些渠道的时候，需要填入相应的参数，其它渠道则是 array() .具体见以下代码或者官网中的文档。其他渠道时可以传空值也可以不传。
        $extra = array();
        //sk_live_OJQEx4iDNUjsC0BuR7UdMbRd sk_test_SSm1OOvD8anLzLaHSOGmnzzP
        \Pingpp\Pingpp::setApiKey('sk_test_SSm1OOvD8anLzLaHSOGmnzzP');
        try {
                $ch = \Pingpp\Charge::create(
                        array(
                            "subject" => "[搅拌]订单支付",
                            "body" => $menubody,
                            "amount" => $totalamount,
                            "order_no" => $orderNo,
                            "currency" => "cny",
                            "extra" => $extra,
                            "channel" => $channel,
                            "client_ip" => $_SERVER["REMOTE_ADDR"],
                            "app" => array("id" => "app_rLSuDGnvvj9S8Ouf")
                        )
                );
                $order = array('encouter_id' => $encouter['id'], 'user_id' => $loginid, 'shop_id' => $encouter['shop_id'], 'time_created' => $ch['created'], 'paid' => 2, 'channel' => $ch['channel'], 'order_no' => $ch['order_no'], 'amount' => floor($ch['amount'] / 100), 'subject' => $ch['subject'], 'body' => $ch['body'], 'description' => $ch['description'], 'created' => date('Y-m-d H:i:s'));
                if(!empty($encouter['prev_encouter_receive_id'])){
                        $order['encouter_receive_id']=$encouter['prev_encouter_receive_id'];
                }
                if(!empty($receiveid)){
                         $order['encouter_receive_id']=$receiveid;
                }
                $orderid = $db->create('order', $order);
                if(!$orderid){
                        echo json_result(null, '209', '支付失败,请联系客服');return;
                }
                for ($i = 1; $i <= 2; $i++) {
                        if (!empty($encouter['product' . $i])) {
                                $od = array('order_id' => $orderid, 'user_id' => $loginid, 'shop_id' => $encouter['shop_id'], 'name' => $encouter['product' . $i], 'img' => $encouter['product_img' . $i], 'price' => $encouter['price' . $i], 'created' => date("Y-m-d H:i:s"));
                                $db->create('order_detail', $od);
                        }
                }
                echo $ch;//json_result($ch);
        } catch (\Pingpp\Error\Base $e) {
                header('Status: ' . $e->getHttpStatus());
                echo($e->getHttpBody());
        }
}

//申请退款
function refund(){
        global $db;
        //$input_data = json_decode(file_get_contents('php://input'), true);
        $input_data = $_POST;
        $orderid = !empty($input_data['orderid']) ? filter($input_data['orderid']) : '';
        $loginid = !empty($input_data['loginid']) ? filter($input_data['loginid']) : '';
        if (empty($loginid)) {
                echo json_result(null, '2', '请您先登录');
                return;
        }
        if ($db->getCount('order',array('id'=>$orderid,'user_id'=>$loginid))<=0){
                echo json_result(null, '3', '订单获取失败');
                return;
        }
        $order = $db->getRow('order', array('id' => $orderid));
        if($order['status']==3){
                echo json_result(null, '4', '您的订单已申请退款,正在处理中');
                return;
        }
        $encouter=$db->getRow('encouter',array('id'=>$order['encouter_id']));
        $days=floor( time() - strtotime($encouter['created']) / 60 / 60 / 24 );
        if($encouter['days']==0 || $days < $encouter['days']){
                echo json_result(null, '5', '您的咖啡还未过期');
                return;
        }
        \Pingpp\Pingpp::setApiKey('sk_test_SSm1OOvD8anLzLaHSOGmnzzP');
        $ch = \Pingpp\Charge::retrieve($order['charge_id']);
        try {
                $ch->refunds->create(
                    array(
                        'amount' => $order['pay_amount'],
                        'description' => '[搅拌]申请退款'
                    )
                );
                $db->excuteSql("begin;"); //使用事务查询状态并改变
                $db->update('order',array('refunded'=>1,'status'=>3),array('id'=>$orderid));//申请退款中
                $db->update('encouter',array('status'=>8),array('id'=>$order['encouter_id']));//取消寄存
                $db->update('encouter_receive',array('status'=>3),array('encouter_id'=>$order['encouter_id']));//拒绝领取者
                $db->excuteSql("commit;");
                echo json_result(array('success'=>'TURE'));
        } catch (\Pingpp\Error\Base $e) {
                echo json_result(null,'6','退款失败,状态:'.$e->getHttpStatus());
        }

}

//未支付的订单再次支付
function secondPay(){
        global $db;
        $input_data = json_decode(file_get_contents('php://input'), true);
        $channel = !empty($input_data['channel']) ? strtolower(filter($input_data['channel'])) : '';
        $orderid = !empty($input_data['orderid']) ? filter($input_data['orderid']) : '';
        $loginid = !empty($input_data['loginid']) ? filter($input_data['loginid']) : '';
        $old_order = $db->getRow('order', array('id' => $orderid));
        $encouterid = $old_order['encouter_id'];
        $receiveid = $old_order['encouter_receive_id'];
        $encouter = $db->getRow('encouter', array('id' => $old_order['encouter_id']));
        $shop = $db->getRow('shop', array('id' => $encouter['shop_id']));
        if($old_order['user_id']!=$loginid){
                echo json_result(null, '201', '您无此订单,请核对');return;
        }
        if ($old_order['paid'] == 1) {
                echo json_result(null, '202', '您的订单已支付');return;
        }
        if ($old_order['status'] == 2) {
                echo json_result(null, '203', '您的订单已失效');return;
        }
        if ($old_order['status'] == 3) {
                echo json_result(null, '204', '您的订单已过期');return;
        }
        $orderNo = $old_order['order_no'];
        switch ($encouter['type']) {
                case 1://爱心
                case 2://缘分
                case 3://约会
                        if($encouter['status']!=1){
                                echo json_result(null, '205', '您的订单无需再支付');return;
                        }
                        break;
                case 4://传递
                        if(empty($encouter['prev_encouter_id'])){
                                if($encouter['status']!=1){
                                        echo json_result(null, '206', '您的订单无需再支付');return;
                                }  
                        }else{
                                $prev_encouter=$db->getRow('encouter',array('id'=>$encouter['prev_encouter_id']));
                                if($prev_encouter['paylock']!=1){
                                        $remenus=floor((time()-strtotime($prev_encouter['updated'])) / 60);
                                        if($remenus<3){//3分钟锁定
                                                echo json_result(null, '207', '这杯咖啡正在等待他人操作,请稍后再来尝试');return;
                                        }
                                }
                                if($prev_encouter['status']!=2){
                                        echo json_result(null, '208', '很抱歉您晚了一步,这杯咖啡已由他人接力');return;
                                }
                                $db->update('encouter',array('paylock'=>2,'updated'=>date("Y-m-d H:i:s")),array('id'=>$encouter['prev_encouter_id']));//锁定支付
                        }
                        break;
                case 5://等候
                        if($db->getCount('encouter_receive',array('id'=>$receiveid,'from_user'=>$loginid))==0){
                                echo json_result(null, '209', '很抱歉您还未领取这杯咖啡,请重新领取');return;
                        }
                        if($encouter['paylock']!=1){
                                $remenus=floor((time()-strtotime($encouter['updated'])) / 60);
                                if($remenus<3){//3分钟锁定
                                        echo json_result(null, '210', '这杯咖啡正在等待他人操作,请稍后再来尝试');return;
                                }
                        }
                        if($encouter['status']!=5){
                                $paid_order=$db->getRow('order',array('encouter_id'=>$encouterid,'paid'=>1));
                                if($paid_order['user_id']!=$loginid){
                                        echo json_result(null, '211', '很抱歉您晚了一步,这杯咖啡已由他人买单');return;
                                }else{
                                        echo json_result(null, '212', '您的订单无需再次支付');return;
                                }
                        }
                        $db->update('encouter',array('paylock'=>2,'updated'=>date("Y-m-d H:i:s")),array('id'=>$encouterid));//锁定支付
                        break;
                default:
                        break;
        }
        $menus = array();
        $menubody = filterSql($shop['title']);
        $totalamount = 0;
        $product = filterSql($encouter['product1']);
        $price = $encouter['price1'];
        $product.=empty($encouter['product2']) ? '' : ',' . filterSql($encouter['product2']);
        $price+=empty($encouter['price2']) ? 0 : $encouter['price2'] * 1;
        $menubody .= '(' . $product . ')';
        $totalamount = $price * 100;
        //$extra 在使用某些渠道的时候，需要填入相应的参数，其它渠道则是 array() .具体见以下代码或者官网中的文档。其他渠道时可以传空值也可以不传。
        $extra = array();
        //sk_live_OJQEx4iDNUjsC0BuR7UdMbRd sk_test_SSm1OOvD8anLzLaHSOGmnzzP
        \Pingpp\Pingpp::setApiKey('sk_test_SSm1OOvD8anLzLaHSOGmnzzP');
        try {
                $ch = \Pingpp\Charge::create(
                        array(
                            "subject" => "[搅拌]订单支付",
                            "body" => $menubody,
                            "amount" => $totalamount,
                            "order_no" => $orderNo,
                            "currency" => "cny",
                            "extra" => $extra,
                            "channel" => $channel,
                            "client_ip" => $_SERVER["REMOTE_ADDR"],
                            "app" => array("id" => "app_rLSuDGnvvj9S8Ouf")
                        )
                );
                $order = array('encouter_id' => $encouter['id'], 'user_id' => $loginid, 'shop_id' => $encouter['shop_id'], 'time_created' => $ch['created'], 'paid' => 2, 'channel' => $ch['channel'], 'order_no' => $ch['order_no'], 'amount' => floor($ch['amount'] / 100), 'subject' => $ch['subject'], 'body' => $ch['body'], 'description' => $ch['description'], 'created' => date('Y-m-d H:i:s'));
                $db->update('order', $order, array('id' => $old_order['id']));
                echo $ch;
        } catch (\Pingpp\Error\Base $e) {
                header('Status: ' . $e->getHttpStatus());
                echo($e->getHttpBody());
        }
}

//ping++通知更新订单状态
function webhooks() {
        global $db;
        $event = json_decode(file_get_contents("php://input"));
        // 对异步通知做处理
        if (!isset($event->type)) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
                exit("fail");
        }
        switch ($event->type) {
                case "charge.succeeded":
                        // 开发者在此处加入对支付异步通知的处理代码
                        $chargeid = $event->data->object->id;
                        $paid = $event->data->object->paid; //支付状态1支付2未付
                        $order_no = $event->data->object->order_no; //订单号
                        $amount = $event->data->object->amount; //订单金额
                        $time_paid = $event->data->object->time_paid; //支付时间戳
                        $transaction_no = $event->data->object->transaction_no; //支付渠道返回流水号
                        if ($paid) {
                                $orderCondition = array('order_no' => $order_no);
                                $order = $db->getRow('order', $orderCondition);
                                $db->update('order', array('charge_id' => $chargeid, 'paid' => 1,'pay_amount'=>$amount, 'time_paid' => $time_paid,'transaction_no'=>$transaction_no), $orderCondition);
                                updateOrderEncouter($order);
                        }
                        header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
                        break;
                case "refund.succeeded":
                        /**
                         * 
                         * alipay
支付宝发起退款会返回一个 status 字段为 pending 的 Refund 对象，并在里面的 Refund 对象中的 failure_msg 字段里提供支付宝退款所需的退款页面的 URL，你需要打开该链接输入支付密码、确认并且完成退款，完成退款时你会收到退款成功的 Webhooks 通知。
                         * 
                         * wx
微信发起退款后返回 Refund 对象，不过分为两种场景：
对于微信旧渠道的零钱袋支付的订单，会返回 succeed 字段为 true 、status 为 succeeded 的 Refund 对象，你也会收到 Ping++ 的异步通知；对于微信新渠道的零钱袋支付的订单，会返回 succeed 字段为 false 、status 为 pending 的 Refund 对象，Ping++ 推送的 Webhooks 中返回 succeed 字段为 true 、status 为 succeeded；
微信使用银行卡支付的订单，返回的是 succeed 字段为 false 、status 为 pending 的 Refund 对象，直到微信完成退款到银行卡的动作后你才会收到 Webhooks 通知，此时事件中 Refund 对象内的 status 为 succeeded。通常退款到银行卡所需的时间会比较长：借记卡是 1~3 个工作日，信用卡是 3~7 个工作日。
                         * 
                         */
                        $data['refund_id'] = $event->data->object->id;
                        $data['status']= $event->data->object->status; //支付状态1支付2未付
                        $data['order_no'] = $event->data->object->order_no; //订单号
                        $data['pay_amount'] = $event->data->object->amount; //支付金额
                        $data['amount'] = floor($data['pay_amount']/100);
                        $data['failure_msg'] = $event->data->object->failure_msg;
                        $data['charge_id'] = $event->data->object->charge;
                        $data['description']=$event->data->object->description;
                        $data['failure_code']=$event->data->object->failure_code;
                        $data['failure_msg']=$event->data->object->failure_msg;//支付宝退款链接
                        if($db->getCount('order_refund',array('refund_id'=>$data['refund_id']))>0){
                                $db->update('order_refund',array('refund_id'=>$refundid),$data);
                        }else{
                                $db->create('order_refund',$data);
                        }
                        if($data['status']=='succeeded'){
                                $db->update('order',array('order_no'=>$data['order_no']),array('status'=>4));//已退款
                        }
                        header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
                        break;
                default:
                        header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
                        break;
        }
}

//领取者付款时数据同步
function updateOrderEncouter($order){
        global $db;
        $encouter=$db->getRow('encouter',array('id'=>$order['encouter_id']));
        //$encouter['status'] 1待付款 2待领取 3待到店领取 4已领走 5等候待付款 6等候待到店领取 7等候已领走
        //$receive['status'] 1等待回复 2可领取 3被拒绝 4传递待支付 5传递已支付 6等候待支付 7等候已支付
        $db->excuteSql("begin;"); //使用事务查询状态并改变
        switch ($encouter['type']) {
                case 1://爱心
                case 2://缘分
                case 3://约会
                        $db->update('encouter', array('status' => 2), array('id' => $order['encouter_id']));
                        //其他订单失效
                        $updateOrderSql="update ".DB_PREFIX."order set status='2' where id <> ".$order['id']." and encouter_id = ".$order['encouter_id'];
                        $db->excuteSql($updateOrderSql);
                        break;
                case 4://传递
                        if(!empty($encouter['prev_encouter_id'])){
                               $receiveid=$encouter['prev_encouter_receive_id'];
                               //传递的咖啡可到店领取
                               $db->update('encouter', array('paylock' => '1','status' => '3'), array('id' => $encouter['prev_encouter_id']));
                               //其他用户传递咖啡失效
                               $db->update('encouter', array('status' => '99'), array('prev_encouter_id' => $encouter['prev_encouter_id']));
                               //其他用户领取传递咖啡失效
                               $db->update('encouter_receive', array('status' => '99'), array('encouter_id' => $encouter['prev_encouter_id']));
                               //其他用户的订单失效
                               $updateOrderSql="update ".DB_PREFIX."order o left join ".DB_PREFIX."encouter encouter on o.encouter_id = encouter.id set o.status='2' where o.id <> ".$order['id']." and encouter.prev_encouter_id = ".$encouter['prev_encouter_id'];
                               $db->excuteSql($updateOrderSql);
                               //可领取
                               $db->update('encouter_receive',array('status'=>2),array('id'=>$receiveid));
                        }else{
                                $maxusers=empty($encouter['people_num'])?200:$encouter['people_num'];
                                $user=$db->getRow('user',array('id'=>$encouter['user_id']));
                                //环信创建群组
                                $HuanxinObj=Huanxin::getInstance();
                                $huserObj=$HuanxinObj->createGroup($encouter['topic'],$encouter['topic'],$maxusers,$user['user_name']);
                                        //addNewAppUser(strtolower($mobile), md5($user_pass));
                                $hxgroupid=$huserObj->data->groupid;
                                if(!empty($groupid)){
                                        $db->create('chatgroup',array('hx_group_id'=>$hxgroupid,'user_id'=>$user['id'],'encouter_id'=>$order['encouter_id'],'name'=>$encouter['topic']));
                                }
                        }
                        //自己寄存的咖啡改变为可领取状态
                        $db->update('encouter', array('status' => 2), array('id' => $order['encouter_id']));
                        break;
                case 5://等候
                        $receiveid=$order['encouter_receive_id'];
                        //可到店领取
                        $db->update('encouter', array('paylock' => '1','status' => 6), array('id' => $order['encouter_id']));
                        //其他用户的订单失效
                        $updateOrderSql="update ".DB_PREFIX."order set status='2' where id <> ".$order['id']." and encouter_id = ".$order['encouter_id'];
                        $db->excuteSql($updateOrderSql);
                        //可领取
                        $db->update('encouter_receive', array('status'=>7), array('id'=>$order['encouter_receive_id']));
                        //其他用户的领取失效
                        $updateOrderSql="update ".DB_PREFIX."encouter_receive erc set erc.status='99' where erc.id <> ".$order['encouter_receive_id']." and erc.encouter_id = ".$order['encouter_id'];
                        $db->excuteSql($updateOrderSql);
                        break;

                default:
                        break;
        }
        $db->excuteSql("commit;");
        if(!empty($receiveid)){
                sendNotifyMsgByReceive($receiveid);//领取成功发送消息
        }
}
