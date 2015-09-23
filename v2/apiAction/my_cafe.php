<?php

require_once APP_DIR.DS.'apiLib'.DS.'ext'.DS.'Upload.php';
$act = filter($_REQUEST['act']);
switch ($act) {
    case 'depositList'://寄存咖啡
        depositList();
        break;
    case 'depositInfo'://寄存咖啡详情
        depositInfo();
        break;
    case 'receiveList'://領取
        receiveList();
        break;
    case 'receiveInfo'://領取咖啡详情
        receiveInfo();
        break;
    case 'waitList'://等候
        waitList();
        break;
    case 'waitInfo'://等候咖啡详情
        waitInfo();
        break;
    case 'waitAgain'://过期再等候
        waitAgain();
        break;
    case 'watiCafeDelImg'://删除等候照片
        watiCafeDelImg();
        break;
    case 'watiCafeUploadImg'://上传等候照片
        watiCafeUploadImg();//
        break;
    default:
        break;
}

//寄存的咖啡列表
function depositList() {
    global $db;
    $loginid = filter($_REQUEST['loginid']);
    $lng = filter($_REQUEST['lng']);
    $lat = filter($_REQUEST['lat']);
    $page_no = isset($_REQUEST ['page']) ? $_REQUEST ['page'] : 1;
    $page_size = PAGE_SIZE;
    $start = ($page_no - 1) * $page_size;
    $sql = "select encouter.id as encouter_id,encouter.product1 as menu,encouter.product_img1 as menu_img,shop.title as shop_name,shop.lng,shop.lat,encouter.created,encouter.status,count(receive.id) as num from " . DB_PREFIX . "encouter encouter "
            . "left join " . DB_PREFIX . "shop shop on shop.id=encouter.shop_id "
            . "left join " . DB_PREFIX . "encouter_receive receive on receive.encouter_id = encouter.id and receive.status=1 "
            . "where encouter.user_id = {$loginid} and encouter.type <> 5 group by encouter.id order by encouter.id desc";
    $sql .= " limit $start,$page_size";
    $data = $db->getAllBySql($sql);
    foreach ($data as $k => $v) {
        //encouter.status 1待付款2待领取3待到店领取4已领走
        $data[$k]['distance'] = (!empty($v['lat']) && !empty($v['lng']) && !empty($lng) && !empty($lat)) ? getDistance($lat, $lng, $v['lat'], $v['lng']) : lang_UNlOCATE;
    }
    echo json_result($data);
}

//寄存咖啡详情
function depositInfo(){
    global $db;
    $loginid = filter($_REQUEST['loginid']);
    $encouterid = filter($_REQUEST['encouterid']);
    $page_no = isset($_REQUEST ['page']) ? $_REQUEST ['page'] : 1;
    $page_size = PAGE_SIZE;
    $start = ($page_no - 1) * $page_size;
    $sql = "select encouter.id as encouter_id,encouter.type,encouter.product1 as menu1,encouter.product_img1 as menu_img1,encouter.price1,encouter.product2 as menu2,encouter.product_img2 as menu_img2,encouter.price2,encouter.shop_id,shop.title as shop_name,shop.img as shop_img,encouter.status,encouter.verifycode,encouter.created from " . DB_PREFIX . "encouter encouter "
            . "left join " . DB_PREFIX . "shop shop on shop.id=encouter.shop_id "
            . "where encouter.id = {$encouterid} and encouter.user_id = {$loginid}";
    $data = $db->getRowBySql($sql);
    //等待了多少秒
    if($data['status']==2){
        $data['waitingtime']=time()-strtotime($data['created']);
    }else{
        $red=$db->getRow('encouter_receive',array('encouter_id'=>$encouterid,'status'=>2));
        $data['waitingtime']=strtotime($red['created'])-strtotime($data['created']);
    }
    //领取的人
    $receiveSql = "select receive.id as receive_id,user.id as user_id,user.nick_name,user.head_photo,receive.msg,receive.created from ".DB_PREFIX."encouter_receive receive "
            . "left join ".DB_PREFIX."user user on user.id = receive.from_user "
            . "where receive.encouter_id = {$encouterid} and receive.status <> 4 and receive.status <> 99 order by receive.id desc ";
    $receiveSql .= " limit $start,$page_size";
    $receives=$db->getAllBySql($receiveSql);
    $data['receives']=$receives;
    echo json_result($data);
    
}


//领取的咖啡列表
function receiveList() {
    global $db;
    $loginid = filter($_REQUEST['loginid']);
    $lng = filter($_REQUEST['lng']);
    $lat = filter($_REQUEST['lat']);
    $page_no = isset($_REQUEST ['page']) ? $_REQUEST ['page'] : 1;
    $page_size = PAGE_SIZE;
    $start = ($page_no - 1) * $page_size;
    $sql = "select receive.id as receive_id,receive.encouter_id,if(choice_menu=2,encouter.product2,encouter.product1) as menu,if(choice_menu=2,encouter.product_img2,product_img1) as menu_img,shop.title as shop_name,shop.lng,shop.lat,receive.created,receive.status from " . DB_PREFIX . "encouter_receive receive "
            . "left join " . DB_PREFIX . "encouter encouter on receive.encouter_id = encouter.id "
            . "left join " . DB_PREFIX . "shop shop on shop.id=encouter.shop_id "
            . "where receive.from_user = {$loginid} and receive.type <> 5 and receive.status <> 4 and receive.status <> 99 order by receive.id desc ";
    $sql .= " limit $start,$page_size";
    $data = $db->getAllBySql($sql);
    foreach ($data as $k => $v) {
        //receive.status 1等待回复2可领取3被拒绝4待支付到账
        $data[$k]['distance'] = (!empty($v['lat']) && !empty($v['lng']) && !empty($lng) && !empty($lat)) ? getDistance($lat, $lng, $v['lat'], $v['lng']) : lang_UNlOCATE;
    }
    echo json_result($data);
}


//领取详情
function receiveInfo(){
    global $db;
    $loginid = filter($_REQUEST['loginid']);
    $receiveid=filter($_REQUEST['receiveid']);
    $sql = "select encouter.shop_id,user.nick_name,user.head_photo,receive.encouter_id,receive.type,if(choice_menu=2,encouter.product2,encouter.product1) as menu,if(choice_menu=2,encouter.product_img2,product_img1) as menu_img,if(choice_menu=2,encouter.price2,price1) as price,encouter.shop_id,shop.title as shop_name,receive.verifycode,encouter.created,receive.status from " . DB_PREFIX . "encouter_receive receive "
            . "left join " .DB_PREFIX . "encouter encouter on encouter.id = receive.encouter_id "
            . "left join " . DB_PREFIX . "shop shop on shop.id = encouter.shop_id "
            . "left join " . DB_PREFIX . "user user on user.id = encouter.user_id "
            . "where receive.id = {$receiveid} and receive.from_user = {$loginid}";
    $data = $db->getRowBySql($sql);
    if($data['status']!=2){//等待回复
        $data['waitingtime']=time()-strtotime($data['created']);
    }else{
        $red=$db->getRow('encouter_receive',array('encouter_id'=>$data['encouter_id'],'status'=>2));
        $data['waitingtime']=strtotime($red['created'])-strtotime($data['created']);
    }
    echo json_result($data);
}


//等候的咖啡
function waitList(){
    global $db;
    $loginid = filter($_REQUEST['loginid']);
    $lng = filter($_REQUEST['lng']);
    $lat = filter($_REQUEST['lat']);
    $page_no = isset($_REQUEST ['page']) ? $_REQUEST ['page'] : 1;
    $page_size = PAGE_SIZE;
    $start = ($page_no - 1) * $page_size;
    $sql = "select encouter.id as wait_id,encouter.product1 as menu,encouter.product_img1 as menu_img,shop.title as shop_name,shop.lng,shop.lat,encouter.created,if(TIMESTAMPDIFF(DAY,encouter.created,now())>encouter.days && encouter.status=5,8,encouter.status) as status from " . DB_PREFIX . "encouter encouter "
            . "left join " . DB_PREFIX . "shop shop on shop.id=encouter.shop_id "
            . "where encouter.user_id = {$loginid} and encouter.type = 5 order by encouter.id desc";
    $sql .= " limit $start,$page_size";
    $data = $db->getAllBySql($sql);
    foreach ($data as $k => $v) {
        //encouter.status 5等候待付款6等候待到店领取7等候已领走8已过期
        $data[$k]['distance'] = (!empty($v['lat']) && !empty($v['lng']) && !empty($lng) && !empty($lat)) ? getDistance($lat, $lng, $v['lat'], $v['lng']) : lang_UNlOCATE;
    }
    echo json_result($data);
    
}


//等候咖啡详情
function waitInfo(){
    global $db;
    $loginid = filter($_REQUEST['loginid']);
    $encouterid=filter($_REQUEST['encouterid']);
    //$encouter['status'] 1待付款 2待领取 3待到店领取 4已领走 5等候待付款 6等候待到店领取 7等候已领走
    //$receive['status'] 1等待回复2可领取3被拒绝4等候待支付5等候已支付
    $sql = "select user.id as user_id,user.nick_name,user.head_photo,encouter.id as encouter_id,encouter.product1 as menu,encouter.product_img1 as menu_img,encouter.price1 as price,encouter.shop_id,shop.title as shop_name,encouter.verifycode,encouter.created,encouter.status from " . DB_PREFIX . "encouter encouter "
            . "left join " .DB_PREFIX . "encouter_receive receive on encouter.id = receive.encouter_id and receive.status = 5 "
            . "left join " . DB_PREFIX . "shop shop on shop.id = encouter.shop_id "
            . "left join " . DB_PREFIX . "user user on user.id = receive.from_user "
            . "where encouter.id = {$encouterid} and encouter.user_id = {$loginid}";
    $data = $db->getRowBySql($sql);
    if($data['status']==5){//等候中
        $data['waitingtime']=time()-strtotime($data['created']);
    }else{
        $red=$db->getRow('encouter_receive',array('encouter_id'=>$data['encouter_id'],'status'=>2));
        $data['waitingtime']=strtotime($red['created'])-strtotime($data['created']);
    }
    echo json_result($data);
}

//再次等候
function waitAgain(){
        global $db;
        $encouterid = filter(!empty($_REQUEST['encouterid']) ? $_REQUEST['encouterid'] : '');
        $userid = filter(!empty($_REQUEST['loginid']) ? $_REQUEST['loginid'] : '');
        $days = filter(!empty($_REQUEST['days']) ? $_REQUEST['days'] : '');
        $msg = filter(!empty($_REQUEST['msg']) ? $_REQUEST['msg'] : '');
        //status 1待付款2待领取3待到店领取4已领走5等候待付款6等候待到店领取7等候已领走


        $data = array();
        if (empty($userid)) {
                echo json_result(null, '2', '请您先登录');
                return;
        }
        if ($db->getCount('encouter',array('id'=>$encouterid,'user_id'=>$userid))<=0){
                echo json_result(null, '3', '这不是您寄存的咖啡');
                return;
        }
        $old_encouter=$db->getRow('encouter',array('id'=>$encouterid));
        $type=$old_encouter['type'];
        if($type!=5){
                echo json_result(null, '4', '您操作的不是等候咖啡');
                return;
        }
        if (empty($days)) {
                echo json_result(null, '5', '请选择寄存天数');
                return;
        } else {
                $data['days'] = $days;
        }
        if (empty($msg)) {
                echo json_result(null, '6', '请输入你的寄语');
                return;
        } else {
                $data['msg'] = $msg;
        }
        if ($db->getCount('encouter_img',array('encouter_id'=>$encouterid,'user_id'=>$userid))<=0){
                echo json_result(null, '7', '请上传至少一张图片');
                return;
        }
        $data['created'] = date("Y-m-d H:i:s");
        $db->update('encouter', $data ,array('id'=>$encouterid));
        echo json_result(array('success' => 'TRUE'));
}

//删除等候的图片
function watiCafeDelImg(){
        global $db;
        $encouterid = filter(!empty($_REQUEST['encouterid']) ? $_REQUEST['encouterid'] : '');
        $userid = filter(!empty($_REQUEST['loginid']) ? $_REQUEST['loginid'] : '');
        $imgid = filter(!empty($_REQUEST['imgid']) ? $_REQUEST['imgid'] : '');
        if (empty($userid)) {
                echo json_result(null, '2', '请您先登录');
                return;
        }
        if ($db->getCount('encouter_img',array('id'=>$imgid,'encouter_id'=>$encouterid,'user_id'=>$userid))<=0){
                echo json_result(null, '3', '您没有这张照片');
                return;
        }
        $img=$db->getRow('encouter_img',array('id'=>$imgid));
        $img['img'];
        $path=str_replace(APP_SITE, "", $img['img']);
	unlink($path);
        $db->delete('encouter_img',array('id'=>$imgid));
        echo json_result(array('success'=>'TRUE'));
}

//上传等候的图片
function watiCafeUploadImg(){
        global $db;
        $encouterid = filter(!empty($_REQUEST['encouterid']) ? $_REQUEST['encouterid'] : '');
        $userid = filter(!empty($_REQUEST['loginid']) ? $_REQUEST['loginid'] : '');
        if (empty($userid)) {
                echo json_result(null, '2', '请您先登录');
                return;
        }
        if ($db->getCount('encouter',array('id'=>$encouterid,'user_id'=>$userid))<=0){
                echo json_result(null, '3', '这不是您等候的咖啡');
                return;
        }
        $upload = new UpLoad();
        $folder = "upload/encouterPhoto/";
        if (!file_exists($folder)) {
                mkdir($folder, 0777);
        }
        $upload->setDir($folder . date("Ymd") . "/");
        $upload->setPrefixName('user' . $userid);
        $file = $upload->upLoad('photo'); //$_File['photo'.$i]
        if ($file['status'] != 1) {
                echo json_result(null, '701', $file['errMsg']);
                return;
        }else{
            $photo=array();
            $photo['img'] = APP_SITE . $file['file_path'];
            $photo['user_id'] = $userid;
            $photo['encouter_id'] = $encouterid;
            $photo['created'] = date("Y-m-d H:i:s");
            $photo['id']=$db->create('encouter_img', $photo);
        }
        echo json_result(array('photo'=>$photo));
        
}