<?php
require_once APP_DIR . DS . 'apiLib' . DS . 'ext' . DS . 'Upload.php';
require_once APP_DIR . DS . 'apiLib' . DS . 'ext' . DS . 'Huanxin.php';
require_once APP_DIR . DS . 'apiLib' . DS . 'ext' . DS . 'Sms.php';
require_once APP_DIR . DS . 'apiLib' . DS . 'ext' . DS . 'Umeng.php';
require_once APP_DIR . DS . 'apiAction' . DS . 'encouter_notifymsg.php';//通知消息

$act = filter($_REQUEST['act']);
switch ($act) {
        case 'deposit'://寄存咖啡
                deposit();
                break;
        case 'nearCafe'://附近邂逅咖啡
                nearCafe();
                break;
        case 'cafeInfo'://咖啡详情
                cafeInfo();
                break;
        case 'receive'://领取咖啡
                receive();
                break;
        case 'shopList':
                shopList();
                break;
        case 'menuList':
                menuList();
                break;
        case 'permit':
                permit();
                break;
        case 'getgroupid':
                getGroupID();
                break;
        default:
                break;
}


//寄存咖啡店列表
function shopList(){
        global $db;
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$city_code=filter($_REQUEST['city_code']);
	$page_no = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	//是否营业中,1营业中,2休息
	$sql="select shop.id,title,img,holidayflag,hours1,hours2,holidays,holidayhours1,holidayhours2,lng,lat from ".DB_PREFIX."shop shop where status=2 ";
        if(!empty($city_code)){
                $city=$db->getRow('shop_addcity',array('code'=>$city_code));
                $sql.=(!empty($city['id']))?" and addcity_id={$city['id']} ":'';
        }
        $sql.=(!empty($lng)&&!empty($lat))?" order by sqrt(power(lng-{$lng},2)+power(lat-{$lat},2)),id ":' order by id ';
	$sql .= " limit $start,$page_size";
	$shops=$db->getAllBySql($sql);
	foreach ($shops as $k=>$v){
                if(strpos(1,$v['holidays'])!==false){
                     $holidays.='一';
                }
                if(strpos(2,$v['holidays'])!==false){
                     $holidays.='二';
                }
                if(strpos(3,$v['holidays'])!==false){
                     $holidays.='三';
                }
                if(strpos(4,$v['holidays'])!==false){
                     $holidays.='四';
                }
                if(strpos(5,$v['holidays'])!==false){
                     $holidays.='五';
                }
                if(strpos(6,$v['holidays'])!==false){
                     $holidays.='六';
                }
                if(strpos(0,$v['holidays'])!==false){
                     $holidays.='日';
                }
                if($v['holidayflag']=='1'){//无休
                        $shops[$k]['hours']=$v['hours1'].'~'.$v['hours2'];
                }elseif ($v['holidayflag']=='2') {//休息日
                        $shops[$k]['hours']=$v['hours1'].'~'.$v['hours2'];
                        $shops[$k]['hours'].='<br/>休息日:';
                        $shops[$k]['hours'].=$holidays;
                }else{//休息日营业
                        $shops[$k]['hours']='平日:'.$v['hours1'].'~'.$v['hours2'];
                        $shops[$k]['hours'].='<br/>'.$holidays.':';
                        $shops[$k]['hours'].=$v['holidayhours1'].'~'.$v['holidayhours2'];
                }
		$shops[$k]['distance']=(!empty($v['lat'])&&!empty($v['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$v['lat'],$v['lng']):lang_UNlOCATE;
	}
	//echo json_result(array('shops'=>$shops));
	echo json_result($shops);
}

//咖啡店菜品列表
function menuList(){
        global $db;
	$shopid=filter($_REQUEST['shopid']);
        $sql="select menu_id,title,img,min(menu_price.price) as price_min,max(menu_price.price) as price_max from ".DB_PREFIX."shop_menu_price menu_price left join ".DB_PREFIX."shop_menu menu on menu.id=menu_price.menu_id where menu.shop_id={$shopid} and menu.status = 2 group by menu_price.menu_id ";
        $menus=$db->getAllBySql($sql);
        foreach ($menus as $k=>$m){
                $menus[$k]['prices']=$db->getAll('shop_menu_price',array('menu_id'=>$m['menu_id']),array('id menuprice_id','type','price'));
        }
        echo json_result($menus);
}

//寄存/等候咖啡
function deposit() {
        global $db;
        $userid = filter(!empty($_REQUEST['loginid']) ? $_REQUEST['loginid'] : '');
        $type = filter(!empty($_REQUEST['type']) ? $_REQUEST['type'] : ''); //1爱心2缘分3约会4传递5等候
        $shopid = filter(!empty($_REQUEST['shopid']) ? $_REQUEST['shopid'] : '');
        $days = filter(!empty($_REQUEST['days']) ? $_REQUEST['days'] : '');
        $people_num = filter(!empty($_REQUEST['people_num']) ? $_REQUEST['people_num'] : '');
        $menuprice1_id = filter(!empty($_REQUEST['menuprice1_id']) ? $_REQUEST['menuprice1_id'] : '');
        $menuprice2_id = filter(!empty($_REQUEST['menuprice2_id']) ? $_REQUEST['menuprice2_id'] : '');
        $question = filter(!empty($_REQUEST['question']) ? $_REQUEST['question'] : '');
        $topic = filter(!empty($_REQUEST['topic']) ? $_REQUEST['topic'] : '');
        $msg = filter(!empty($_REQUEST['msg']) ? $_REQUEST['msg'] : '');
        $tag_ids = filter(!empty($_REQUEST['tag_ids']) ? $_REQUEST['tag_ids'] : '');
        $tag_sex = filter(!empty($_REQUEST['tag_sex']) ? $_REQUEST['tag_sex'] : '');
        $prev_encouter_id = filter(!empty($_REQUEST['prev_encouter_id']) ? $_REQUEST['prev_encouter_id'] : '');
        $prev_encouter_receive_id = filter(!empty($_REQUEST['receive_id']) ? $_REQUEST['receive_id'] : '');
        //status 1待付款2待领取3待到店领取4已领走5等候待付款6等候待到店领取7等候已领走


        $data = array();
        if (empty($userid)) {
                echo json_result(null, '2', '请您先登录');
                return;
        } else {
                $data['user_id'] = $userid;
        }
        if (empty($type)) {
                echo json_result(null, '3', '请选择主题');
                return;
        } else {
                $data['type'] = $type;
        }
        if (empty($shopid)) {
                echo json_result(null, '4', '请选择店铺');
                return;
        } else {
                $data['shop_id'] = $shopid;
        }
        if ($type!=4&&empty($days)) {
                echo json_result(null, '5', '请选择寄存天数');
                return;
        } else {
                $data['days'] = $days;
        }
        if (empty($menuprice1_id)) {
                echo json_result(null, '6', '请选择寄存咖啡');
                return;
        } else {
                $menuprice = $db->getRow('shop_menu_price', array('id' => $menuprice1_id));
                if ($menuprice['shop_id'] != $shopid) {
                        echo json_result(null, '61', '您选择的咖啡店发生改变,请重新选择咖啡');
                        return;
                }
                $data['menuprice1_id'] = $menuprice1_id;
                $data['menu1_id'] = $menuprice['menu_id'];
                $data['price1'] = $menuprice['price'];
                $menu = $db->getRow('shop_menu', array('id' => $menuprice['menu_id']));
                $data['product1'] = $menu['title'];
                $data['product_img1'] = $menu['img'];
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
                        if (empty($menuprice2_id)) {
                                echo json_result(null, '8', '请选择第二杯寄存咖啡');
                                return;
                        } else {
                                $menuprice = $db->getRow('shop_menu_price', array('id' => $menuprice2_id));
                                if ($menuprice['shop_id'] != $shopid) {
                                        echo json_result(null, '81', '您选择的咖啡店发生改变,请重新选择咖啡');
                                        return;
                                }
                                $data['menuprice2_id'] = $menuprice2_id;
                                $data['menu2_id'] = $menuprice['menu_id'];
                                $data['price2'] = $menuprice['price'];
                                $menu = $db->getRow('shop_menu', array('id' => $menuprice['menu_id']));
                                $data['product2'] = $menu['title'];
                                $data['product_img2'] = $menu['img'];
                        }
                        break;
                case 4://传递 寄存结束传递
                        $data['transfer_num'] = 1;
                        if (!empty($prev_encouter_id)) {
                                if($db->getCount('encouter',array('id'=>$prev_encouter_id))==0){
                                        echo json_result(null, '11', '参数错误,请返回上一步');return;
                                }
                                if($db->getCount('encouter_receive',array('id'=>$prev_encouter_receive_id,'from_user'=>$userid))==0){
                                        echo json_result(null, '12', '您未点击开始传递按钮,请返回上一步');return;
                                }
                                $prev_encouter = $db->getRow('encouter', array('id' => $prev_encouter_id), array('transfer_num','people_num','topic'));
                                $data['transfer_num'] = $prev_encouter['transfer_num'] + 1;
                                $data['prev_encouter_id'] = $prev_encouter_id;
                                $data['prev_encouter_receive_id'] = $prev_encouter_receive_id;
                                $data['people_num'] = $prev_encouter['people_num'];
                                $data['topic'] = $prev_encouter['topic'];
                        }else{
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
                        }
                        break;
                case 5://上传三张图片
                        $flag = false;
                        $upload = new UpLoad();
                        $folder = "upload/encouterPhoto/";
                        if (!file_exists($folder)) {
                                mkdir($folder, 0777);
                        }
                        $upload->setDir($folder . date("Ymd") . "/");
                        $upload->setPrefixName('user' . $userid);
                        $file = $upload->uploadFiles('photos'); //$_File['photo'.$i]
                        if ($file['status'] != 0 && $file['status'] != 1) {
                                echo json_result(null, '701', $file['errMsg']);
                                return;
                        }
                        if ($file['status'] == 1) {
                                $flag = true;
                        }
                        if (!$flag) {
                                echo json_result(null, '11', '请上传至少一张图片');
                                return;
                        }
                        $data['status']=5;//等候待付款
                        break;
                default :
                        break;
        }
        
        if (empty($msg)) {
                echo json_result(null, '10', '请输入你的寄语');
                return;
        } else {
                $data['msg'] = $msg;
        }
        $data['created'] = date("Y-m-d H:i:s");
        $encouterid = $db->create('encouter', $data);
        //插入人物标签
        if (!empty($tag_ids)) {
                $tags = explode(",", $tag_ids);
                $tgsql = "";
                foreach ($tags as $tg) {
                        $tgsql.=",(NULL, '" . $encouterid . "', '" . $tg . "')";
                }
                $tgsql = substr($tgsql, 1);
                $insertTag = "INSERT INTO cofe_encouter_usertag (`id`, `encouter_id`, `tag_id`) VALUES {$tgsql};";
                $db->excuteSql($insertTag);
        }
        //插入图片数据
        if ($type == 5 && $flag) {
                foreach ($file['filepaths'] as $path) {
                        $photo['img'] = APP_SITE . $path;
                        $photo['user_id'] = $userid;
                        $photo['encouter_id'] = $encouterid;
                        $photo['created'] = date("Y-m-d H:i:s");
                        $db->create('encouter_img', $photo);
                }
        }
        echo json_result(array('encouter_id' => $encouterid));
}

//附近的邂逅咖啡
function nearCafe() {
        global $db;
        $lng = filter($_REQUEST['lng']);
        $lat = filter($_REQUEST['lat']);
        $city_code = filter($_REQUEST['city_code']);
        $area_id = filter($_REQUEST['area_id']);
        $circle_id = filter($_REQUEST['circel_id']);
        $tag_ids = filter($_REQUEST['tag_ids']);
        $tag_sex = filter($_REQUEST['tag_sex']);//1男2女
        $type = filter($_REQUEST['type']);
        $page_no = isset($_REQUEST ['page']) ? $_REQUEST ['page'] : 1;
        $page_size = PAGE_SIZE;

        $sql = "select encouter.id,encouter.user_id,encouter.type,user.head_photo as img "
                . "from " . DB_PREFIX . "encouter encouter "
                . "left join " . DB_PREFIX . "shop shop on encouter.shop_id=shop.id "
                . "left join " . DB_PREFIX . "user user on encouter.user_id=user.id "
                . "left join " . DB_PREFIX . "user_tag user_tag on user.id=user_tag.user_id "
                . "where (TIMESTAMPDIFF(DAY,encouter.created,now())>encouter.days or encouter.days=0) and (encouter.status=2 or encouter.status=5) "; //1待付款2待领取3待到店领取4已领走4等候待付款5等候待到店领取6等候已领走
        if (!empty($city_code)) {
                $city = $db->getRow('shop_addcity', array('code' => $city_code));
                $sql.=(!empty($city['id'])) ? " and addcity_id={$city['id']} " : '';
        }
        $sql.=(!empty($area_id)) ? " and addarea_id={$area_id} " : '';
        $sql.=(!empty($circle_id)) ? " and addcircle_id={$circle_id} " : '';
        $sql.=(!empty($tag_sex)) ? " and tag_sex={$tag_sex} " : '';
        $sql.=(!empty($tag_ids)) ? " and user_tag.tag_id in ({$tag_ids}) " : '';
        if(!empty($type)){
                $typeCond='';
                $types=explode(',', $type);
                foreach ($types as $t){
                        $typeCond.=($typeCond!='')?" or encouter.type = {$t} ":" encouter.type = {$t} ";
                }
                $sql.=" and ({$typeCond}) ";
        }

        $sql.=(!empty($lng) && !empty($lat)) ? " order by sqrt(power(shop.lng-{$lng},2)+power(shop.lat-{$lat},2)),id " : ' order by id ';
        $total = $db->getCountBySql($sql);
        
        $page_total = floor($total/$page_size) + 1;
        $page_no = ($page_no % $page_total);
        $page_no = $page_no==0?$page_total:$page_no;
        $start = ($page_no - 1) * $page_size;
        $sql .= " limit $start,$page_size";
        //$sql = "select * from ($sql) s group by s.user_id";
        $data = $db->getAllBySql($sql);
        //echo json_result(array('shops'=>$shops));
        echo json_result($data);
}

//查看邂逅咖啡信息
function cafeInfo() {
        global $db;
        $id = filter($_REQUEST['id']);
        $sql = "select encouter.id as encouter_id,encouter.type,encouter.user_id,user.head_photo,user.nick_name,prev_user.head_photo as prev_head_photo,prev_user.nick_name as prev_nick_name,encouter.shop_id,shop.title as shop_title,shop.img as shop_img,shop.lng,shop.lat,prev_shop.lng as prev_lng,prev_shop.lat as prev_lat,encouter.days,encouter.people_num,encouter.transfer_num,encouter.product1 as cafe1,encouter.product_img1 as cafe_img1,encouter.price1,encouter.product2 as cafe2,encouter.product_img2 as cafe_img2,encouter.price2,encouter.msg,encouter.question,encouter.topic,encouter.tag_sex,encouter.status from " . DB_PREFIX . "encouter encouter "
                . "left join " . DB_PREFIX . "user user on encouter.user_id=user.id "
                . "left join " . DB_PREFIX . "shop shop on encouter.shop_id=shop.id "
                . "left join " . DB_PREFIX . "encouter prev_encouter on prev_encouter.id=encouter.prev_encouter_id "
                . "left join " . DB_PREFIX . "user prev_user on prev_encouter.user_id=user.id "
                . "left join " . DB_PREFIX . "shop prev_shop on prev_encouter.shop_id=prev_shop.id "
                . "where encouter.id = {$id}";
        $data = $db->getRowBySql($sql);
        $tagsql = "select tag.id as tag_id,tag.name as tag_name from " . DB_PREFIX . "encouter_usertag usertag "
                . "left join " . DB_PREFIX . "base_user_tag tag on usertag.tag_id=tag.id "
                . "where usertag.encouter_id={$id}";
        $data['tags'] = $db->getAllBySql($tagsql);
        $data['user_imgs'] = $db->getAll('encouter_img', array('encouter_id' => $id), array('id','img'));
        echo json_result($data);
}

//领取咖啡
function receive() {
        global $db;
        $userid = filter(!empty($_REQUEST['loginid']) ? $_REQUEST['loginid'] : '');
        $encouterid = filter(!empty($_REQUEST['encouterid']) ? $_REQUEST['encouterid'] : '');
        $msg = filter(!empty($_REQUEST['msg']) ? $_REQUEST['msg'] : '');
        $encouter = $db->getRow('encouter', array('id' => $encouterid));
        $type = $encouter['type']; //1爱心2缘分3约会4传递5等候
        //$encouter['status'] 1待付款 2待领取 3待到店领取 4已领走 5等候待付款 6等候待到店领取 7等候已领走
        //$receive['status']  1等待回复 2可领取 3被拒绝 4传递待支付 5传递已支付 6等候待支付 7等候已支付
        if(empty($userid)){
                echo json_result(null,'2','请您先登录');return;
        }
        if(empty($encouterid)){
                echo json_result(null,'3','请求参数错误');return;
        }
        switch ($type) {
                case 1://爱心
                        $isreceived = true;
                        $db->excuteSql("begin;"); //使用事务查询状态并改变
                        if ($encouter['status'] == 2) {
                                $isreceived = false;
                                $receive = array('from_user' => $userid, 'encouter_id' => $encouterid, 'type' => $encouter['type'], 'msg' => $msg, 'to_user' => $encouter['user_id'], 'status' => 2, 'created' => date("Y-m-d H:i:s"));
                                $receiveid=$db->create('encouter_receive', $receive);
                                $db->update('encouter', array('status' => 3), array('id' => $encouterid));
                        }
                        $db->excuteSql("commit;");
                        if ($isreceived) {
                                echo json_result(null, '203', '很抱歉您晚了一步');
                                return;
                        }
                        sendNotifyMsgByReceive($receiveid);//通知领取
                        break;
                case 2://缘分
                        if ($encouter['status'] != 2) {
                                echo json_result(null, '203', '很抱歉您晚了一步');return;
                        }
                        if (empty($msg)){
                                echo json_result(null, '210', '请输入您的答案');return;
                        }
                        if($db->getCount('encouter_receive',array('encouter_id'=>$encouterid,'from_user'=>$userid,'status'=>1))>0){
                                echo json_result(null, '211', '您已经领取过了,请等待回复');return;
                        }
                        $receive = array('from_user' => $userid, 'encouter_id' => $encouterid, 'type' => $encouter['type'], 'msg' => $msg, 'to_user' => $encouter['user_id'], 'status' => 1, 'created' => date("Y-m-d H:i:s"));
                        $receiveid=$db->create('encouter_receive', $receive);
                        sendNotifyMsgByReceive($receiveid);//通知等候
                        break;
                case 3://3约会
                        if ($encouter['status'] != 2) {
                                echo json_result(null, '203', '很抱歉您晚了一步');return;
                        }
                        $choice_menu = filter(!empty($_REQUEST['choice_menu']) ? $_REQUEST['choice_menu'] : '');
                        if (empty($choice_menu)) {
                                echo json_result(null, '204', '请选择一杯咖啡');
                                return;
                        }
                        $datetime = filter(!empty($_REQUEST['datetime']) ? $_REQUEST['datetime'] : '');
                        if (empty($datetime)) {
                                echo json_result(null, '205', '请选择应约时间');
                                return;
                        }
                        if($db->getCount('encouter_receive',array('encouter_id'=>$encouterid,'from_user'=>$userid,'status'=>1))>0){
                                echo json_result(null, '211', '您已经领取过了,请等待回复');return;
                        }
                        $receive = array('from_user' => $userid, 'encouter_id' => $encouterid, 'type' => $encouter['type'], 'msg' => $msg, 'to_user' => $encouter['user_id'], 'status' => 1, 'datetime' => $datetime, 'choice_menu' => $choice_menu, 'created' => date("Y-m-d H:i:s"));
                        $receiveid=$db->create('encouter_receive', $receive);
                        sendNotifyMsgByReceive($receiveid);//通知等候
                        break;
                case 4://4传递-----开始传递
                        if ($encouter['status'] != 2) {
                                echo json_result(null, '203', '很抱歉您晚了一步');return;
                        }
                        if($encouter['paylock']!=1){
                                $remenus=floor((time()-strtotime($encouter['updated'])) / 60);
                                if($remenus<3){//8分钟锁定
                                        json_result(null, '206', '这杯咖啡正在等待他人操作,请稍后再来尝试');return;
                                }
                        }
                        if($encouter['status']!=2){
                                json_result(null, '207', '很抱歉,这杯咖啡已由他人接力');return;
                        }
                        if($db->getCount('encouter_receive',array('encouter_id'=>$encouterid,'from_user'=>$userid))==0){
                                $receive = array('from_user' => $userid, 'encouter_id' => $encouterid, 'type' => $encouter['type'], 'to_user' => $encouter['user_id'], 'status' => 4, 'created' => date("Y-m-d H:i:s"));
                                $receiveid=$db->create('encouter_receive', $receive);
                        }else{
                                $receive = $db->getRow('encouter_receive',array('from_user' => $userid, 'encouter_id' => $encouterid),array('id'));
                                $receiveid = $receive['id'];
                                $db->update('encouter_receive', array('created'=> date("Y-m-d H:i:s")),array('id' => $receiveid));
                        }
                        break;
                case 5://5等候 为Ta买单
                        if ($encouter['status'] != 5) {
                                echo json_result(null, '203', '很抱歉您晚了一步');return;
                        }
                        if($encouter['paylock']!=1){
                                $remenus=floor((time()-strtotime($encouter['updated'])) / 60);
                                if($remenus<3){//8分钟锁定
                                        json_result(null, '208', '这杯咖啡正在等待他人操作,请稍后再来尝试');return;
                                }
                        }
                        if($encouter['status']!=5){
                                json_result(null, '209', '很抱歉,您晚了一步');return;
                        }
                        if($db->getCount('encouter_receive',array('encouter_id'=>$encouterid,'from_user'=>$userid))==0){
                                $receive = array('from_user' => $userid, 'encouter_id' => $encouterid, 'type' => $encouter['type'], 'to_user' => $encouter['user_id'], 'status' => 6, 'created' => date("Y-m-d H:i:s"));
                                $receiveid=$db->create('encouter_receive', $receive);
                        }else{
                                $receive = $db->getRow('encouter_receive',array('from_user' => $userid, 'encouter_id' => $encouterid),array('id'));
                                $receiveid = $receive['id'];
                                $db->update('encouter_receive', array('created'=> date("Y-m-d H:i:s")),array('id' => $receiveid));
                        }
                        break;
                default :
                        break;
        }
        //发送通知等待寄存者回复授权
        $receive=$db->getRow('encouter_receive',array('id'=>$receiveid));
        if(empty($receive)){
                echo json_result(null,'4','数据返回错误');return;
        }
        echo json_result(array('receiveid' => $receiveid));
}

//寄存者同意 type 2缘分 3约会
function permit() {
        global $db;
        $userid = filter(!empty($_REQUEST['loginid']) ? $_REQUEST['loginid'] : '');
        $receiveid = filter(!empty($_REQUEST['receiveid']) ? $_REQUEST['receiveid'] : '');//领取id
        $receive=$db->getRow('encouter_receive',array('id'=>$receiveid));
        $encouter=$db->getRow('encouter',array('id'=>$receive['encouter_id']));
        if($encouter['status']!=2){
                echo json_result(null, '2', '寄存咖啡已同意');
                return;
        }
        if($encouter['user_id']!=$userid){
                echo json_result(null, '3', '非本人寄存的咖啡');
                return;
        }
        $db->excuteSql('begin');
        $db->update('encouter_receive',array('status'=>2),array('id'=>$receiveid));//可领取
        //拒绝其他
        $updateOrderSql="update ".DB_PREFIX."encouter_receive set status = 3 where id <> ".$receiveid." and encouter_id = ".$receive['encouter_id'];
        $db->excuteSql($updateOrderSql);
        $db->update('encouter',array('status'=>3),array('id'=>$receive['encouter_id']));//待到店领取
        $db->excuteSql('commit');
        //发送授权通知
        sendNotifyMsgByPermiter($receiveid);
        echo json_result(array('success' => 'TRUE'));
        
}

//获取群组id
function getGroupID($encouter_id){
        $chatgroup=$db->getRow('chatgroup',array('encouter_id'=>$encouter_id));
        echo json_result(array('hx_groupid' => $chatgroup['hx_groupid']));
}