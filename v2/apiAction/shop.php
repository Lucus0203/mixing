<?php
$act = filter($_REQUEST['act']);
switch ($act) {
    case 'nearbyShops':
        nearbyShops(); //附近店铺
        break;
    case 'collectShops':
        collectShops(); //用户收藏的店铺
        break;
    case 'removeFavoriteShopById'://取消收藏
        removeFavoriteShopById();
        break;
    case 'shopInfo':
        shopInfo(); //店铺详情
        break;
    case 'favorites':
        favorites(); //收藏店铺
        break;
    case 'leaveMsg':
        leaveMsg(); //店铺留言
        break;
    case 'shopFeedback':
        shopFeedback(); //店铺反馈纠错
        break;
    case 'isCollect'://查看是否收藏
        isCollect();
        break;
    case 'share'://分享
        share();
        break;
    case 'depositShops'://可寄存的咖啡馆
        depositShops();
        break;
    case 'shopsMap':
        shopsMap();
        break;
    case 'diaryShops'://发布到附近的咖啡馆
        diaryShops();
        break;
//    case 'delShopImgByCity';
//        delShopImgByCity();
//        break;
//    case 'delShopMenuImgByCityCode';
//        delShopMenuImgByCityCode();
//        break;
    default:
        break;
}

//附近咖啡
function nearbyShops() {
    global $db;
    $lng = filter($_REQUEST['lng']);
    $lat = filter($_REQUEST['lat']);
    $city_code = filter($_REQUEST['city_code']);
    $area_id = filter($_REQUEST['area_id']);
    $circle_id = filter($_REQUEST['circle_id']);
    $keyword = filterSql(filter($_REQUEST['keyword']));
    $tag_ids = filter($_REQUEST['tag_ids']);
    $page_no = isset($_REQUEST ['page']) ? $_REQUEST ['page'] : 1;
    $page_size = PAGE_SIZE;
    $start = ($page_no - 1) * $page_size;
    //是否营业中,1营业中,2休息
    $isopensql = getIsopensql();
    $sql = "select shop.id,title,img,address,lng,lat,n.msg_num,e.cup_num," . $isopensql . " from " . DB_PREFIX . "shop shop "
            . "left join " . DB_PREFIX . "shop_tag shop_tag on shop_tag.shop_id=shop.id "
            . "left join (select shop_id,count(id) as msg_num from " . DB_PREFIX . "diary diary where diary.shop_view_status != 2 group by shop_id ) n on n.shop_id=shop.id "
            . "left join (select shop_id,count(id) as cup_num from " . DB_PREFIX . "encouter encouter where status=2 group by shop_id ) e on e.shop_id=shop.id "
            . "where status=2 ";
    if (!empty($city_code)) {
        $city = $db->getRow('shop_addcity', array('code' => $city_code));
        $sql.=(!empty($city['id'])) ? " and addcity_id={$city['id']} " : '';
    }
    $sql.=(!empty($area_id)) ? " and addarea_id={$area_id} " : '';
    $sql.=(!empty($circle_id)) ? " and addcircle_id={$circle_id} " : '';
    $sql.=(!empty($keyword)) ? " and ( INSTR(title,'" . addslashes($keyword) . "') or INSTR(subtitle,'" . addslashes($keyword) . "') or INSTR(address,'" . addslashes($keyword) . "') ) " : '';
    $sql.=(!empty($tag_ids)) ? " and shop_tag.tag_id in ({$tag_ids}) " : '';
    $sql .= " group by shop.id ";

    $sql.=(!empty($lng) && !empty($lat)) ? " order by sqrt(power(lng-{$lng},2)+power(lat-{$lat},2)),id " : ' order by id ';
    $sql .= " limit $start,$page_size";
    $shops = $db->getAllBySql($sql);
    foreach ($shops as $k => $v) {
        $shops[$k]['distance'] = (!empty($v['lat']) && !empty($v['lng']) && !empty($lng) && !empty($lat)) ? getDistance($lat, $lng, $v['lat'], $v['lng']) : lang_UNlOCATE;
    }
    //echo json_result(array('shops'=>$shops));
    echo json_result($shops);
}

//店铺地图
function shopsMap() {
    global $db;
    $clng = filter($_REQUEST['clng']);
    $clat = filter($_REQUEST['clat']);
    $lng = !empty($clng)?$clng:filter($_REQUEST['lng']);
    $lat = !empty($clat)?$clat:filter($_REQUEST['lat']);
    $clng = filter($_REQUEST['clng']);
    $clat = filter($_REQUEST['clat']);
    $city_code = filter($_REQUEST['city_code']);
    $area_id = filter($_REQUEST['area_id']);
    $circle_id = filter($_REQUEST['circle_id']);
    $keyword = filterSql(filter($_REQUEST['keyword']));
    $tag_ids = filter($_REQUEST['tag_ids']);
    //$zoomlevel = filter($_REQUEST['zoomlevel']);
    $zoom = filter($_REQUEST['zoom']);
    //$zoomarea = array(10,20,50,100,200,500,1000,2*1000,5*1000,10*1000,20*1000,25*1000,50*1000);
    
    if (empty($city_code)) {
        echo json_result(null, '2', '获取不到您所在的城市');
        return;
    }else{
        $city = $db->getRow('shop_addcity', array('code' => $city_code));
    }
    if(empty($lng) || empty($lat)){
        $loc=getLngFromBaidu($city['name']);
        $lng=$loc['lng'];
        $lat=$loc['lat'];
    }
    if(empty($zoom)){
        echo json_result(null, '3', '获取不到您的范围');
        return;
    }
    //$zoomlevel=($zoomlevel-3)<0?0:$zoomlevel-3;
    //$zoom=$zoomlevel>11?$zoomarea[11]:$zoomarea[$zoomlevel];
    //是否营业中,1营业中,2休息
    $isopensql = getIsopensql();
    $sql = "select shop.id,title,img,address,lng,lat,n.msg_num,e.cup_num," . $isopensql . " from " . DB_PREFIX . "shop shop "
            . "left join " . DB_PREFIX . "shop_tag shop_tag on shop_tag.shop_id=shop.id "
            . "left join (select shop_id,count(id) as msg_num from " . DB_PREFIX . "diary diary group by shop_id ) n on n.shop_id=shop.id "
            . "left join (select shop_id,count(id) as cup_num from " . DB_PREFIX . "encouter encouter where status=2 group by shop_id ) e on e.shop_id=shop.id "
            . "where status=2 ";
    $sql.=(!empty($city['id'])) ? " and addcity_id={$city['id']} " : '';
    $sql.=(!empty($area_id)) ? " and addarea_id={$area_id} " : '';
    $sql.=(!empty($circle_id)) ? " and addcircle_id={$circle_id} " : '';
    $sql.=(!empty($keyword)) ? " and ( INSTR(title,'" . addslashes($keyword) . "') or INSTR(subtitle,'" . addslashes($keyword) . "') or INSTR(address,'" . addslashes($keyword) . "') ) " : '';
    $sql.=(!empty($tag_ids)) ? " and shop_tag.tag_id in ({$tag_ids}) " : '';
    $sql.=" and round(6378.138*2*asin(sqrt(pow(sin( ($lat*pi()/180-shop.lat*pi()/180)/2),2)+cos($lat*pi()/180)*cos(shop.lat*pi()/180)* pow(sin( ($lng*pi()/180-shop.lng*pi()/180)/2),2)))*1000) <= ".($zoom * 20);
    //$squarePoint = returnSquarePoint($lng, $lat, $zoom * 20);//RANGE_KILO 20倍的范围
    //$sql .= " shop.lng >= ".$squarePoint['leftTop']['lng']." and shop.lng <= ".$squarePoint['rightTop']['lng']." and shop.lat >= ".$squarePoint['leftBottom']['lat']." and shop.lat <= ".$squarePoint['leftTop']['lat'];
    $sql .= " group by shop.id ";
    $sql .= " order by id ";
    $shops = $db->getAllBySql($sql);
    //echo $db->getCountBySql($sql);
     
    $point=array();
    $z=$zoom>50?$zoom*2:$zoom;
    foreach ($shops as $k => $v) {
        $v['distance'] = getDistance($lat, $lng, $v['lat'], $v['lng']);
        if(empty($point)){
            $point[]=array('lng'=>$v['lng'],'lat'=>$v['lat'],'num'=>1,'shop'=>$v);
        }else{
            $addflag=false;
            foreach ($point as $pk => $p){
                if((getDistance($p['lat'], $p['lng'], $v['lat'], $v['lng'])*1000)<$z){
                    $point[$pk]['num']++;
                    $addflag=true;
                    break;
                }
            }
            if(!$addflag){
                $point[]=array('lng'=>$v['lng'],'lat'=>$v['lat'],'num'=>1,'shop'=>$v);
            }
        }
    }
    
    echo json_result(array('points'=>$point));
}

//咖啡店铺详情
function shopInfo() {
    global $db;
    $shopid = filter($_REQUEST['shopid']);
    $loginid = filter($_REQUEST['loginid']);
    $lng = filter($_REQUEST['lng']);
    $lat = filter($_REQUEST['lat']);
    //$page_no = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : 1;
    //$page_size = PAGE_SIZE;
    //$start = ($page_no - 1) * $page_size;
    if (!empty($shopid)) {
        $shop = $db->getRow('shop', array('id' => $shopid), array('id', 'title', 'img', 'tel', 'address', 'feature', 'introduction', 'hours', 'hours1', 'hours2', 'holidayflag', 'holidays', 'holidayhours1', 'holidayhours2', 'lng', 'lat','ispassed'));
        $shop['tel'] = trim($shop['tel']);
        $shop['distance'] = (!empty($shop['lat']) && !empty($shop['lng']) && !empty($lng) && !empty($lat)) ? getDistance($lat, $lng, $shop['lat'], $shop['lng']) : lang_UNlOCATE;
        //店内消息数
        $shop['msg_num'] = $db->getCount('diary', array('shop_id' => $shopid ."' and shop_view_status != '2"));
        //店内咖啡数
        $shop['cup_num'] = $db->getCount('encouter', array('shop_id' => $shopid, 'status' => 2));
        //店内菜品
        $menusql = "select menu_id,title,img from " . DB_PREFIX . "shop_menu_price menu_price left join " . DB_PREFIX . "shop_menu menu on menu.id=menu_price.menu_id where menu.shop_id={$shopid} and menu.status = 2 group by menu_price.menu_id ";
        $menus = $db->getAllBySql($menusql);
        foreach ($menus as $k => $m) {
            $menus[$k]['prices'] = $db->getAll('shop_menu_price', array('menu_id' => $m['menu_id']), array('id menuprice_id', 'type', 'price'));
        }
        $shop['menus'] = $menus;
        $shop['introduction'] = empty($shop['introduction']) ? '        信息正在更新中...' : $shop['introduction'];
        //特色
        $shoptagsql = "select tag.id,base_tag.name from " . DB_PREFIX . "shop_tag tag left join " . DB_PREFIX . "base_shop_tag base_tag  on tag.tag_id = base_tag.id where tag.shop_id={$shopid} ";
        $features = $db->getAllBySql($shoptagsql);
        foreach ($features as $f) {
            if (!empty($f['name'])) {
                $shop['features'][] = $f['name'];
            } else {
                $db->delete('shop_tag', array('id' => $f['id']));
            }
        }
        //店铺图片
        $imgsql = "select id,img,width,height from " . DB_PREFIX . "shop_img where shop_id = $shopid and img <> '{$shop['img']}' ";
        $shop['imgs'] = $db->getAllBySql($imgsql);
        $first_img_sql = "select id,img,width,height from " . DB_PREFIX . "shop_img where shop_id = $shopid and img = '{$shop['img']}' ";
        $first_img = $db->getRowBySql($first_img_sql);
        if (!empty($first_img)) {
            array_unshift($shop['imgs'], $first_img);
        }
        if (!empty($shop['hours1'])) {
            $hours = $shop['hours1'] . '~' . $shop['hours2'];
            $holiday = "";
            if ($shop['holidayflag'] != '1') {
                if (strpos($shop['holidays'], '1') !== false) {
                    $holiday.='一';
                }
                if (strpos($shop['holidays'], '2') !== false) {
                    $holiday.= empty($holiday) ? '二' : ',二';
                }
                if (strpos($shop['holidays'], '3') !== false) {
                    $holiday.= empty($holiday) ? '三' : ',三';
                }
                if (strpos($shop['holidays'], '4') !== false) {
                    $holiday.= empty($holiday) ? '四' : ',四';
                }
                if (strpos($shop['holidays'], '5') !== false) {
                    $holiday.= empty($holiday) ? '五' : ',五';
                }
                if (strpos($shop['holidays'], '6') !== false) {
                    $holiday.= empty($holiday) ? '六' : ',六';
                }
                if (strpos($shop['holidays'], '0') !== false) {
                    $holiday.= empty($holiday) ? '日' : ',日';
                }
            }
            if ($shop['holidayflag'] == '3') {
                $holiday = !empty($holiday) ? ' 周' . $holiday . ':' . $shop['holidayhours1'] . '~' . $shop['holidayhours2'] : '';
            } elseif ($shop['holidayflag'] == '2') {
                $holiday = !empty($holiday) ? ' 休:周' . $holiday : '';
            }
            $shop['hours'] = $hours . $holiday;
        }
        //是否营业中 1营业中2休息
        if ($shop['holidayflag'] != 1) {
            if (strpos($shop['holidays'], date("w")) !== false) {
                if ($shop['holidayflag'] == 3) {
                    $holidayhours1 = $shop['holidayhours1'];
                    $holidayhours2 = $shop['holidayhours2'];
                    if ($holidayhours2 <= $holidayhours1) {
                        if ($holidayhours1 <= date("H:i") || date("H:i") <= $holidayhours2) {
                            $shop['isopen'] = 1;
                        } else {
                            $shop['isopen'] = 2;
                        }
                    } else {
                        if ($holidayhours1 <= date("H:i") && date("H:i") <= $holidayhours2) {
                            $shop['isopen'] = 1;
                        } else {
                            $shop['isopen'] = 2;
                        }
                    }
                } else {
                    $shop['isopen'] = 2;
                }
            } else {
                $hours1 = $shop['hours1'];
                $hours2 = $shop['hours2'];
                if ($hours2 <= $hours1) {
                    if ($hours1 <= date("H:i") || date("H:i") <= $hours2) {
                        $shop['isopen'] = 1;
                    } else {
                        $shop['isopen'] = 2;
                    }
                } else {
                    if ($hours1 <= date("H:i") && date("H:i") <= $hours2) {
                        $shop['isopen'] = 1;
                    } else {
                        $shop['isopen'] = 2;
                    }
                }
            }
        } else {
            $hours1 = $shop['hours1'];
            $hours2 = $shop['hours2'];
            if ($hours2 <= $hours1) {
                if ($hours1 <= date("H:i") || date("H:i") <= $hours2) {
                    $shop['isopen'] = 1;
                } else {
                    $shop['isopen'] = 2;
                }
            } else {
                if ($hours1 <= date("H:i") && date("H:i") <= $hours2) {
                    $shop['isopen'] = 1;
                } else {
                    $shop['isopen'] = 2;
                }
            }
        }
        //是否收藏
        if ($db->getCount('shop_users', array('user_id' => $loginid, 'shop_id' => $shopid)) > 0) {
            $shop['iscollect'] = 1; //已收藏
        } else {
            $shop['iscollect'] = 2; //未收藏
        }

        $menusql = " select menu.shop_id from " . DB_PREFIX . "shop_menu menu left join " . DB_PREFIX . "shop_menu_price menu_price on menu.id=menu_price.menu_id where menu.status=2 and menu_price.id is not null and menu.shop_id = $shopid ";
        if ($db->getCountBySql($menusql) > 0) {
        //if ($loginid==5) {
            $shop['isDepositShop'] = 1;
        } else {
            $shop['isDepositShop'] = 2;
        }
        
        //$shop['isDepositShop']=$shopid==2052?2:1;

        $shop['regist_num']=$db->getCount('beans_log',array('shop_id'=>$shopid,'type'=>3));//1登录2发布漫生活3签到
        if(!empty($loginid)){
            $shop['registed']=1;//1没签到,2签到过了
            $sql="select id from ".DB_PREFIX."beans_log beans_log where user_id=$loginid and shop_id=$shopid and type=3 and created>='".date("Y-m-d")." 00:00:00' and created<='".date("Y-m-d")." 23:59:59' ";
            if($db->getCountBySql($sql)>0){
                $shop['registed']=2;
            }
        }

        echo json_result($shop);
    } else {
        echo json_result(null, '22', '店铺不存在');
    }
}

//收藏店铺
function favorites() {
    global $db;
    $shopid = filter($_REQUEST['shopid']);
    $loginid = filter($_REQUEST['loginid']);
    if (!empty($shopid) && !empty($loginid)) {
        if ($db->getCount('shop_users', array('user_id' => $loginid, 'shop_id' => $shopid)) == 0) {
            $up = array('user_id' => $loginid, 'shop_id' => $shopid, 'created' => date("Y-m-d H:i:s"));
            $db->create('shop_users', $up);
        }
        echo json_result('success');
    } else {
        echo json_result(null, '23', '用户未登录或者该店铺已删除');
    }
}

//取消收藏的店铺
function removeFavoriteShopById() {
    global $db;
    $loginid = filter(!empty($_REQUEST['loginid']) ? $_REQUEST['loginid'] : '');
    $shopid = filter(!empty($_REQUEST['shopid']) ? $_REQUEST['shopid'] : '');
    if (!empty($shopid) && !empty($loginid)) {
        $up = array('user_id' => $loginid, 'shop_id' => $shopid);
        $db->delete('shop_users', $up);
        echo json_result('success');
    } else {
        echo json_result(null, '23', '用户未登录或者该店铺已删除');
    }
}

//收藏的店铺
function collectShops() {
    global $db;
    $loginid = filter($_REQUEST['loginid']);
    if (empty($loginid)) {
        echo json_result(null, '21', '用户未登录');
        return;
    }
    $lng = filter($_REQUEST['lng']);
    $lat = filter($_REQUEST['lat']);
    $page_no = isset($_REQUEST ['page']) ? $_REQUEST ['page'] : 1;
    $page_size = PAGE_SIZE;
    $start = ($page_no - 1) * $page_size;

    $isopensql = getIsopensql();
    $sql = "select shop.id as shop_id,shop.title,shop.subtitle,shop.hours,shop.img,shop.lng,shop.lat," . $isopensql . " from " . DB_PREFIX . "shop shop left join " . DB_PREFIX . "shop_users shopuser on shop.id=shopuser.shop_id where shopuser.user_id=" . $loginid . " and status=2 ";
    $sql.=(!empty($lng) && !empty($lat)) ? " order by sqrt(power(lng-{$lng},2)+power(lat-{$lat},2))" : '';

    $sql .= " limit $start,$page_size";
    $shops = $db->getAllBySql($sql);
    foreach ($shops as $k => $v) {
        $shops[$k]['distance'] = (!empty($v['lat']) && !empty($v['lng']) && !empty($lng) && !empty($lat)) ? getDistance($lat, $lng, $v['lat'], $v['lng']) : lang_UNlOCATE;
    }
    //echo json_result(array('shops'=>$shops));
    echo json_result(array('shops' => $shops));
}

//是否收藏
function isCollect() {
    global $db;
    $shopid = filter($_REQUEST['shopid']);
    $loginid = filter($_REQUEST['loginid']);
    if (!empty($shopid) && !empty($loginid)) {
        if ($db->getCount('shop_user', array('user_id' => $loginid, 'shop_id' => $shopid)) > 0) {
            echo json_result('1'); //已收藏
        } else {
            echo json_result('2'); //未收藏
        }
    } else {
        echo json_result(null, '20', '用户未登录或者该店铺已删除');
    }
}

//店铺反馈
function shopFeedback() {
    global $db;
    $shopid = filter($_REQUEST['shopid']);
    $loginid = filter($_REQUEST['loginid']);
    $content = filterIlegalWord($_REQUEST['content']);
    $feedback = array('shop_id' => $shopid, 'content' => $content, 'type' => 'shop', 'created' => date("Y-m-d H:i:s"));
    if (!empty($loginid)) {
        $feedback['user_id'] = $loginid;
    }
    $db->create('feedback', $feedback);
    echo json_result('success');
}

//店铺分享
function share() {
    global $db;
    $shopid = filter($_REQUEST['shopid']);
    if (empty($shopid)) {
        echo json_result(null, '2', '请选择你要分享的店铺');
        return;
    }
    $shop = $db->getRow('shop', array('id' => $shopid));
    $url = WEB_SITE . 'shop/info.html?s=' . base64_encode($shopid);
    $title = $shop['title'];
    $img = $shop['img'];
    $share = array('url' => $url, 'title' => $title, 'img' => $img);
    echo json_result(array('share' => $share));
}

function depositShops() {
    global $db;
    $lng = filter($_REQUEST['lng']);
    $lat = filter($_REQUEST['lat']);
    $city_code = filter($_REQUEST['city_code']);
    $area_id = filter($_REQUEST['area_id']);
    $circle_id = filter($_REQUEST['circle_id']);
    $keyword = filterSql(filterSql(filter($_REQUEST['keyword'])));
    $tag_ids = filter($_REQUEST['tag_ids']);
    $encouterid = isset($_REQUEST ['encouterid']) ? $_REQUEST ['encouterid'] : ''; //过滤咖啡馆用
    $page_no = isset($_REQUEST ['page']) ? $_REQUEST ['page'] : 1;
    $page_size = PAGE_SIZE;
    $start = ($page_no - 1) * $page_size;
    $shopsql = "select shop.id,title,img,lng,lat from " . DB_PREFIX . "shop shop left join " . DB_PREFIX . "shop_tag shop_tag on shop_tag.shop_id=shop.id where shop.status=2 and shop.ispassed=1 ";
    if (!empty($city_code)) {
        $city = $db->getRow('shop_addcity', array('code' => $city_code));
        $shopsql.=(!empty($city['id'])) ? " and addcity_id={$city['id']} " : '';
    }
    $shopsql.=(!empty($area_id)) ? " and addarea_id={$area_id} " : '';
    $shopsql.=(!empty($circle_id)) ? " and addcircle_id={$circle_id} " : '';
    $shopsql.=(!empty($keyword)) ? " and ( INSTR(title,'" . addslashes($keyword) . "') or INSTR(subtitle,'" . addslashes($keyword) . "') or INSTR(address,'" . addslashes($keyword) . "') ) " : '';
    $shopsql.=(!empty($tag_ids)) ? " and shop_tag.tag_id in ({$tag_ids}) " : '';
    if (!empty($encouterid)) {
        $encouter = $db->getRow('encouter', array('id' => $encouterid), array('transfer_encouterids'));
        if (!empty($encouter['transfer_encouterids'])) {
            $transfer_encouterids = explode(',', $encouter['transfer_encouterids']);
            $firstEncouterId = $transfer_encouterids[0];
        } else {
            $firstEncouterId = $encouterid;
        }
        $encouter = $db->getRow('encouter', array('id' => $firstEncouterId), array('shop_id'));
        $shopsql.=" and shop.id={$encouter['shop_id']} ";
    }
    $shopsql .= " group by shop.id ";

    $menusql = " select menu.shop_id from " . DB_PREFIX . "shop_menu menu left join " . DB_PREFIX . "shop_menu_price menu_price on menu.id=menu_price.menu_id where menu.status=2 and menu_price.id is not null group by menu.shop_id ";
    $sql = "select * from ($shopsql) s left join ($menusql) m on s.id = m.shop_id where m.shop_id is not null ";

    $sql.=(!empty($lng) && !empty($lat)) ? " order by sqrt(power(lng-{$lng},2)+power(lat-{$lat},2)),id " : ' order by id ';
    $sql .= " limit $start,$page_size";
    $shops = $db->getAllBySql($sql);
    foreach ($shops as $k => $v) {
        $shops[$k]['distance'] = (!empty($v['lat']) && !empty($v['lng']) && !empty($lng) && !empty($lat)) ? getDistance($lat, $lng, $v['lat'], $v['lng']) : lang_UNlOCATE;
    }
    //echo json_result(array('shops'=>$shops));
    echo json_result($shops);
}

//发布到附近的咖啡馆
function diaryShops(){
    global $db;
    $lng = filter($_REQUEST['lng']);
    $lat = filter($_REQUEST['lat']);
    $loginid = filter($_REQUEST['loginid']);
    $shopid = filter($_REQUEST['shopid']);
    $city_code = filter($_REQUEST['city_code']);
    $page_no = isset($_REQUEST ['page']) ? $_REQUEST ['page'] : 1;
    $page_size = PAGE_SIZE;
    $start = ($page_no - 1) * $page_size;
    $shopsql = "select shop.id,title,img,lng,lat,hours1,hours2,holidays,holidayflag,holidayhours1,holidayhours2 from " . DB_PREFIX . "shop shop left join " . DB_PREFIX . "shop_tag shop_tag on shop_tag.shop_id=shop.id where shop.status=2 ";
    
    if(empty($loginid)){ //未登录
        $shopsql = "select shop.id,title,img,lng,lat,hours1,hours2,holidays,holidayflag,holidayhours1,holidayhours2 from " . DB_PREFIX . "shop shop where shop.status=2 ";
    }else{
        $shopsql = "select shop.id,title,img,lng,lat,hours1,hours2,holidays,holidayflag,holidayhours1,holidayhours2,if(su.id<>'',if(su.id is not null,1,2),2) as likeshopflag from " . DB_PREFIX . "shop shop left join ".DB_PREFIX."shop_users su on su.shop_id = shop.id and su.user_id=$loginid where shop.status=2 ";
    }
    if (!empty($city_code)) {
        $city = $db->getRow('shop_addcity', array('code' => $city_code));
        $shopsql.=(!empty($city['id'])) ? " and addcity_id={$city['id']} " : '';
    }
    $shopsql.=(!empty($shopid)) ? " and shop.id={$shopid} " : '';
    $shopsql .= " group by shop.id ";
    if(empty($loginid)){ //未登录
        $shopsql.=(!empty($lng) && !empty($lat)) ? " order by sqrt(power(lng-{$lng},2)+power(lat-{$lat},2)),id " : ' order by shop.id ';
    }else{
        $shopsql.=(!empty($lng) && !empty($lat)) ? " order by likeshopflag,sqrt(power(lng-{$lng},2)+power(lat-{$lat},2)),shop.id " : ' order by shop.id ';
    }
    $shopsql .= " limit $start,$page_size";
    $shops = $db->getAllBySql($shopsql);
    foreach ($shops as $k => $v) {
        $shops[$k]['distance'] = (!empty($v['lat']) && !empty($v['lng']) && !empty($lng) && !empty($lat)) ? getDistance($lat, $lng, $v['lat'], $v['lng']) : lang_UNlOCATE;
        if (!empty($v['hours1'])) {
            $hours = $v['hours1'] . '~' . $v['hours2'];
            $holiday = "";
            if ($v['holidayflag'] != '1') {
                if (strpos($v['holidays'], '1') !== false) {
                    $holiday.='一';
                }
                if (strpos($v['holidays'], '2') !== false) {
                    $holiday.= empty($holiday) ? '二' : ',二';
                }
                if (strpos($v['holidays'], '3') !== false) {
                    $holiday.= empty($holiday) ? '三' : ',三';
                }
                if (strpos($v['holidays'], '4') !== false) {
                    $holiday.= empty($holiday) ? '四' : ',四';
                }
                if (strpos($v['holidays'], '5') !== false) {
                    $holiday.= empty($holiday) ? '五' : ',五';
                }
                if (strpos($v['holidays'], '6') !== false) {
                    $holiday.= empty($holiday) ? '六' : ',六';
                }
                if (strpos($v['holidays'], '0') !== false) {
                    $holiday.= empty($holiday) ? '日' : ',日';
                }
            }
            if ($v['holidayflag'] == '3') {
                $holiday = !empty($holiday) ? ' 周' . $holiday . ':' . $v['holidayhours1'] . '~' . $v['holidayhours2'] : '';
            } elseif ($v['holidayflag'] == '2') {
                $holiday = !empty($holiday) ? ' 休:周' . $holiday : '';
            }
            $shops[$k]['hours'] = $hours . $holiday;
        }
    }
    //echo json_result(array('shops'=>$shops));
    echo json_result($shops);
    
}

function getIsopensql() { 
    $isopensql = " if(holidayflag = '3' , 
			if(locate(dayofweek(now())-1,holidays) > 0,
				if(holidayhours2<holidayhours1,
					if(holidayhours1 <= DATE_FORMAT(now(),'%H:%i') or holidayhours2 >= DATE_FORMAT(now(),'%H:%i'),1,2),
					if(holidayhours1 <= DATE_FORMAT(now(),'%H:%i') and DATE_FORMAT(now(),'%H:%i') <= holidayhours2,1,2)
				),
                                if(hours2 <= hours1,
                                        if(hours1 <= DATE_FORMAT(now(),'%H:%i') or hours2 >= DATE_FORMAT(now(),'%H:%i'),1,2),
                                        if(hours1 <= DATE_FORMAT(now(),'%H:%i') and DATE_FORMAT(now(),'%H:%i') <= hours2,1,2)
                                )
                        ),
                        if(holidayflag = '2',
                                if(locate(dayofweek(now())-1,holidays) = 0,
                                        if(hours2<hours1,
                                                if(hours1 <= DATE_FORMAT(now(),'%H:%i') or hours2 >= DATE_FORMAT(now(),'%H:%i'),1,2),
                                                if(hours1 <= DATE_FORMAT(now(),'%H:%i') and DATE_FORMAT(now(),'%H:%i') <= hours2,1,2)
                                        ),
                                2),
                                if(hours2 <= hours1,
                                        if(hours1 <= DATE_FORMAT(now(),'%H:%i') or hours2 >= DATE_FORMAT(now(),'%H:%i'),1,2),
                                        if(hours1 <= DATE_FORMAT(now(),'%H:%i') and DATE_FORMAT(now(),'%H:%i') <= hours2,1,2)
                                )
                        )
                    ) as isopen ";
    return $isopensql;
}


function delShopImgByCity(){
    global $db;
    $city_code = filter($_REQUEST['city_code']);
    $city=$db->getRow('shop_addcity',array('code'=>$city_code));
    echo $db->getCount('shop',array('addcity_id'=>$city['id']));
    $shops=$db->getAll('shop',array('addcity_id'=>$city['id']));
    foreach ($shops as $s){
        $imgs=$db->getAll('shop_img',array('shop_id'=>$s['id']));
        foreach ($imgs as $m){
            $path=str_replace(APP_SITE, "", $m['img']);
            if(file_exists($path)){
                unlink($path);//删除图片
            }
            $db->delete('shop_img',array('id'=>$m['id']));
        }
        $pimg=str_replace(APP_SITE, "", $s['img']);
        if(file_exists($pimg)){
            unlink($pimg);//删除图片
        }
    }
}

function delShopMenuImgByCityCode(){
    global $db;
    $city_code = filter($_REQUEST['city_code']);
    $city=$db->getRow('shop_addcity',array('code'=>$city_code));
    echo $db->getCount('shop',array('addcity_id'=>$city['id']));
    $shops=$db->getAll('shop',array('addcity_id'=>$city['id']));
    foreach ($shops as $s){
        $imgs=$db->getAll('shop_menu',array('shop_id'=>$s['id']));
        foreach ($imgs as $m){
            $path=str_replace(APP_SITE, "", $m['img']);
            if(file_exists($path)){
                unlink($path);//删除图片
            }
            $db->delete('shop_menu',array('id'=>$m['id']));
        }
    }
}