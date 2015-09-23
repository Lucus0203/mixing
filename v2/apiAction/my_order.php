<?php

$act = filter($_REQUEST['act']);
switch ($act) {
    case 'orderlist'://消费的咖啡
        orderlist();
        break;
    case 'depositAgain':
        depositAgain();
        break;
    default:
        break;
}

//消费的咖啡
function orderlist(){
        global $db;
	$loginid = filter($_REQUEST['loginid']);
	if(empty($loginid)){
		echo json_result(null,'21','用户未登录');
		return;
	}
	$type = filter($_REQUEST['type']);//1已付2未付3过期
        $type = empty($type)?1:$type;
	$page_no = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
        $sql="select od.id as order_id,od.encouter_id,encouter.type as encouter_type,encouter.product_img1,encouter.product1,encouter.price1,encouter.product_img2,encouter.product2,encouter.price2,shop.title as shop,od.amount,od.created from ".DB_PREFIX."order od "
                . "left join ".DB_PREFIX."shop shop on shop.id=od.shop_id "
                . "left join ".DB_PREFIX."encouter encouter on encouter.id=od.encouter_id "
                . "where od.user_id=".$loginid;
        if($type==1){
                $sql.=" and paid=1 and od.status=1 ";
        }elseif ($type==2) {
                $sql.=" and paid=2 and od.status=1 ";
        }else{
                $sql.=" and TIMESTAMPDIFF(DAY,encouter.created,now())>encouter.days and encouter.days!=0 and encouter.status=2 ";
        }
	$sql .=" order by od.id desc ";
	$sql .= " limit $start,$page_size ";
	$list=$db->getAllBySql($sql);
        echo json_result(array('orders'=>$list));
        
}


//续存
function depositAgain(){
        global $db;
        $encouterid = filter(!empty($_REQUEST['encouterid']) ? $_REQUEST['encouterid'] : '');
        $userid = filter(!empty($_REQUEST['loginid']) ? $_REQUEST['loginid'] : '');
        $days = filter(!empty($_REQUEST['days']) ? $_REQUEST['days'] : '');
        $people_num = filter(!empty($_REQUEST['people_num']) ? $_REQUEST['people_num'] : '');
        $question = filter(!empty($_REQUEST['question']) ? $_REQUEST['question'] : '');
        $topic = filter(!empty($_REQUEST['topic']) ? $_REQUEST['topic'] : '');
        $msg = filter(!empty($_REQUEST['msg']) ? $_REQUEST['msg'] : '');
        $tag_ids = filter(!empty($_REQUEST['tag_ids']) ? $_REQUEST['tag_ids'] : '');
        $tag_sex = filter(!empty($_REQUEST['tag_sex']) ? $_REQUEST['tag_sex'] : '');
        //status 1待付款2待领取3待到店领取4已领走5等候待付款6等候待到店领取7等候已领走


        $data = array();
        if (empty($userid)) {
                echo json_result(null, '2', '请您先登录');
                return;
        }
        if ($db->getCount('encouter',array('id'=>$encouterid,'user_id'=>$userid))<=0){
                echo json_result(null, '3', '这不是您寄存的咖啡,不可续存');
                return;
        }
        $old_encouter=$db->getRow('encouter',array('id'=>$encouterid));
        $type=$old_encouter['type'];
        if ($type!=4&&empty($days)) {
                echo json_result(null, '5', '请选择寄存天数');
                return;
        } else {
                $data['days'] = $days;
        }
        if(!empty($tag_sex)){
                $data['tag_sex']=$tag_sex;
        }
        switch ($type) {
                case 2://缘分咖啡
                        if (empty($question)) {
                                echo json_result(null, '7', '请输入你想问的问题');
                                return;
                        } else {
                                $data['question'] = $question;
                        }
                        break;
                case 3://约会咖啡
                        break;
                case 4://传递 寄存结束传递
                        if (empty($people_num)) {
                                echo json_result(null, '9', '请选择想要传递的人数');
                                return;
                        } else {
                                $data['people_num'] = $people_num;
                        }
                        if (empty($topic)) {
                                echo json_result(null, '10', '请输入你的话题');
                                return;
                        } else {
                                $data['topic'] = $topic;
                        }
                        break;
                default :
                        break;
        }
        
        if (empty($msg)) {
                echo json_result(null, '11', '请输入你的寄语');
                return;
        } else {
                $data['msg'] = $msg;
        }
        $data['created'] = date("Y-m-d H:i:s");
        $db->update('encouter', $data , array('id'=>$encouterid));
        //插入人物标签
        if (!empty($tag_ids)) {
                $db->delete('encouter_usertag',array('encouter_id'=>$encouterid));
                $tags = explode(",", $tag_ids);
                $tgsql = "";
                foreach ($tags as $tg) {
                        $tgsql.=",(NULL, '" . $encouterid . "', '" . $tg . "')";
                }
                $tgsql = substr($tgsql, 1);
                $insertTag = "INSERT INTO cofe_encouter_usertag (`id`, `encouter_id`, `tag_id`) VALUES {$tgsql};";
                $db->excuteSql($insertTag);
        }
        echo json_result(array('success' => 'TRUE'));
}