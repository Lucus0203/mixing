<?php
$act=filter($_REQUEST['act']);
switch ($act){
	case 'nearbyShops':
		nearbyShops();//附近店铺
		break;
	case 'collectShops':
		collectShops();//用户收藏的店铺
		break;
	case 'removeFavoriteShopById'://取消收藏
		removeFavoriteShopById();
		break;
	case 'shopInfo':
		shopInfo();//店铺详情
		break;
	case 'favorites':
		favorites();//收藏店铺
		break;
	case 'leaveMsg':
		leaveMsg();//店铺留言
		break;
	case 'shopFeedback':
		shopFeedback();//店铺反馈纠错
		break;
	case 'isCollect'://查看是否收藏
		isCollect();
		break;
        case 'share'://分享
                share();
                break;
	default:
		break;
}

//附近咖啡
function nearbyShops(){
	global $db;
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$city_code=filter($_REQUEST['city_code']);
	$area_id=filter($_REQUEST['area_id']);
	$circle_id=filter($_REQUEST['circle_id']);
	$keyword=filter($_REQUEST['keyword']);
	$tag_ids=filter($_REQUEST['tag_ids']);
	$page_no = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	//是否营业中,1营业中,2休息
	$isopensql=getIsopensql();
	$sql="select shop.id,title,img,lng,lat,".$isopensql." from ".DB_PREFIX."shop shop left join ".DB_PREFIX."shop_tag shop_tag on shop_tag.shop_id=shop.id where status=2 ";
        if(!empty($city_code)){
                $city=$db->getRow('shop_addcity',array('code'=>$city_code));
                $sql.=(!empty($city['id']))?" and addcity_id={$city['id']} ":'';
        }
        $sql.=(!empty($area_id))?" and addarea_id={$area_id} ":'';
        $sql.=(!empty($circle_id))?" and addcircle_id={$circle_id} ":'';
        $sql.=(!empty($keyword))?" and ( INSTR(title,'".addslashes($keyword)."') or INSTR(subtitle,'".addslashes($keyword)."') or INSTR(address,'".addslashes($keyword)."') ) ":'';
        $sql.=(!empty($tag_ids))?" and shop_tag.tag_id in ({$tag_ids}) ":'';
        $sql .= " group by shop.id ";
        
        $sql.=(!empty($lng)&&!empty($lat))?" order by sqrt(power(lng-{$lng},2)+power(lat-{$lat},2)),id ":' order by id ';
	$sql .= " limit $start,$page_size";
	$shops=$db->getAllBySql($sql);
	foreach ($shops as $k=>$v){
		$shops[$k]['distance']=(!empty($v['lat'])&&!empty($v['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$v['lat'],$v['lng']):lang_UNlOCATE;
	}
	//echo json_result(array('shops'=>$shops));
	echo json_result($shops);
}

//咖啡店铺详情
function shopInfo(){
	global $db;
	$shopid=filter($_REQUEST['shopid']);
	$loginid=filter($_REQUEST['loginid']);
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	//$page_no = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : 1;
	//$page_size = PAGE_SIZE;
	//$start = ($page_no - 1) * $page_size;
	if(!empty($shopid)){
		$shop=$db->getRow('shop',array('id'=>$shopid),array('id','title','img','tel','address','feature','introduction','hours','hours1','hours2','holidayflag','holidays','holidayhours1','holidayhours2','lng','lat'));
		$shop['tel']=trim($shop['tel']);
		$shop['distance']=(!empty($shop['lat'])&&!empty($shop['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$shop['lat'],$shop['lng']):lang_UNlOCATE;
                //店内咖啡
                $menusql="select menu_id,title,img from ".DB_PREFIX."shop_menu_price menu_price left join ".DB_PREFIX."shop_menu menu on menu.id=menu_price.menu_id where menu.shop_id={$shopid} and menu.status = 2 group by menu_price.menu_id ";
                $menus=$db->getAllBySql($menusql);
                foreach ($menus as $k=>$m){
                        $menus[$k]['prices']=$db->getAll('shop_menu_price',array('menu_id'=>$m['menu_id']),array('id menuprice_id','type','price'));
                }
		$shop['menus']=$db->getAll('shop_menu',array('shop_id'=>$shopid,'status'=>2),array('title','img'));
		$shop['introduction']=empty($shop['introduction'])?'        信息正在更新中...':$shop['introduction'];
		//特色
                $shoptagsql="select tag.id,base_tag.name from ".DB_PREFIX."shop_tag tag left join ".DB_PREFIX."base_shop_tag base_tag  on tag.tag_id = base_tag.id where tag.shop_id={$shopid} ";
		$features=$db->getAllBySql($shoptagsql);
                foreach ($features as $f){
                        if(!empty($f['name'])){
                                $shop['features'][]=$f['name'];
                        }else{
                              $db->delete('shop_tag',array('id'=>$f['id']));
                        }
                }
		//店铺图片
                $imgsql="select id,img,width,height from ".DB_PREFIX."shop_img where shop_id = $shopid and img <> '{$shop['img']}' ";
		$shop['imgs']=$db->getAllBySql($imgsql);
                $first_img_sql="select id,img,width,height from ".DB_PREFIX."shop_img where shop_id = $shopid and img = '{$shop['img']}' ";
                $first_img=$db->getRowBySql($first_img_sql);
                if(!empty($first_img)){
                    array_unshift($shop['imgs'],$first_img);
                }
		//是否营业中 1营业中2休息
		if($shop['holidayflag']!=1){
			if(strpos($shop['holidays'] , date("w"))!==false){
				if($shop['holidayflag']==3){
					$holidayhours1=$shop['holidayhours1'];
					$holidayhours2=$shop['holidayhours2'];
					if($holidayhours2<=$holidayhours1){
						if($holidayhours1<=date("H:i")||date("H:i")<=$holidayhours2){
							$shop['isopen']=1;
						}else{
							$shop['isopen']=2;
						}
					}else{
						if($holidayhours1<=date("H:i")&&date("H:i")<=$holidayhours2){
							$shop['isopen']=1;
						}else{
							$shop['isopen']=2;
						}
					}
				}else{
					$shop['isopen']=2;
				}
			}else{
				$hours1=$shop['hours1'];
				$hours2=$shop['hours2'];
				if($hours2<=$hours1){
					if($hours1<=date("H:i")||date("H:i")<=$hours2){
						$shop['isopen']=1;
					}else{
						$shop['isopen']=2;
					}
				}else{
					if($hours1<=date("H:i")&&date("H:i")<=$hours2){
						$shop['isopen']=1;
					}else{
						$shop['isopen']=2;
					}
				}
			}
		}
		//是否收藏
		if($db->getCount('shop_users',array('user_id'=>$loginid,'shop_id'=>$shopid))>0){
			$shop['iscollect']=1;//已收藏
		}else{
			$shop['iscollect']=2;//未收藏
		}
		
		//$bbs_sql="select up.path,u.nick_name,u.user_name,bbs.user_id,bbs.shop_id,CONCAT(bbs.num,'楼:',bbs.content) as content,bbs.created from ".DB_PREFIX."shop_bbs bbs left join ".DB_PREFIX."user u on u.id=bbs.user_id left join ".DB_PREFIX."user_photo up on up.id=u.head_photo_id where bbs.allow=1 and bbs.shop_id=$shopid";
		//$shop['bbsCount']=$db->getCountBySql($bbs_sql);
		//$bbs_sql.=" order by bbs.id desc limit $start,$page_size";
		//$shop['bbs']=$db->getAllBySql($bbs_sql);
		
		
		echo json_result($shop);
	}else{
		echo json_result(null,'22','店铺不存在');
	}
	
}

//收藏店铺
function favorites(){
	global $db;
	$shopid=filter($_REQUEST['shopid']);
	$loginid=filter($_REQUEST['loginid']);
	if(!empty($shopid)&&!empty($loginid)){
		if($db->getCount('shop_users',array('user_id'=>$loginid,'shop_id'=>$shopid))==0){
			$up=array('user_id'=>$loginid,'shop_id'=>$shopid,'created'=>date("Y-m-d H:i:s"));
			$db->create('shop_users', $up);
		}
		echo json_result('success');
	}else{
		echo json_result(null,'23','用户未登录或者该店铺已删除');
	}
}

//取消收藏的店铺
function removeFavoriteShopById(){
	global $db;
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$shopid=filter(!empty($_REQUEST['shopid'])?$_REQUEST['shopid']:'');
	if(!empty($shopid)&&!empty($loginid)){
		$up=array('user_id'=>$loginid,'shop_id'=>$shopid);
		$db->delete('shop_users', $up);
		echo json_result('success');
	}else{
		echo json_result(null,'23','用户未登录或者该店铺已删除');
	}

}


//收藏的店铺
function collectShops(){
	global $db;
	$loginid=filter($_REQUEST['loginid']);
	if(empty($loginid)){
		echo json_result(null,'21','用户未登录');
		return;
	}
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$page_no = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;

	$isopensql=getIsopensql();
	$sql="select shop.id as shop_id,shop.title,shop.subtitle,shop.hours,shop.img,shop.lng,shop.lat,".$isopensql." from ".DB_PREFIX."shop shop left join ".DB_PREFIX."shop_users shopuser on shop.id=shopuser.shop_id where shopuser.user_id=".$loginid." and status=2 ";
	$sql.=(!empty($lng)&&!empty($lat))?" order by sqrt(power(lng-{$lng},2)+power(lat-{$lat},2))":'';

	$sql .= " limit $start,$page_size";
	$shops=$db->getAllBySql($sql);
	foreach ($shops as $k=>$v){
		$shops[$k]['distance']=(!empty($v['lat'])&&!empty($v['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$v['lat'],$v['lng']):lang_UNlOCATE;
	}
	//echo json_result(array('shops'=>$shops));
	echo json_result(array('shops'=>$shops));
}

//是否收藏
function isCollect(){
	global $db;
	$shopid=filter($_REQUEST['shopid']);
	$loginid=filter($_REQUEST['loginid']);
	if(!empty($shopid)&&!empty($loginid)){
		if($db->getCount('shop_user',array('user_id'=>$loginid,'shop_id'=>$shopid))>0){
			echo json_result('1');//已收藏
		}else{
			echo json_result('2');//未收藏
		}
	}else{
		echo json_result(null,'20','用户未登录或者该店铺已删除');
	}
}


//店铺反馈
function shopFeedback(){
	global $db;
	$shopid=filter($_REQUEST['shopid']);
	$loginid=filter($_REQUEST['loginid']);
	$content=filterIlegalWord($_REQUEST['content']);
	$feedback=array('shop_id'=>$shopid,'content'=>$content,'type'=>'shop','created'=>date("Y-m-d H:i:s"));
	if(!empty($loginid)){
		$feedback['user_id']=$loginid;
	}
	$db->create('feedback', $feedback);
	echo json_result('success');
}

//店铺分享
function share(){
	global $db;
	$shopid=filter($_REQUEST['shopid']);
        if(empty($shopid)){
            echo json_result(null,'2','请选择你要分享的店铺');
            return ;
        }
        $shop=$db->getRow('shop',array('id'=>$shopid));
        $url=WEB_SITE.'shopDetail.html?s='.base64_decode($shopid);
        $title=$shop['title'];
        $img=$shop['img'];
        $share=array('url'=>$url,'title'=>$title,'img'=>$img);
        echo json_result(array('share'=>$share));
        
}

function getIsopensql(){
        $isopensql=" if(holidayflag = '3' , 
			if(locate(dayofweek(now())-1,holidays) > 0,
				if(holidayhours2<holidayhours1,
					if(holidayhours1 <= DATE_FORMAT(now(),'%H:%i') or holidayhours2 >= DATE_FORMAT(now(),'%H:%i'),1,2),
					if(holidayhours1 <= DATE_FORMAT(now(),'%H:%i') and DATE_FORMAT(now(),'%H:%i') <= holidayhours2,1,2)
				),
			if(hours2 <= hours1,
				if(hours1 <= DATE_FORMAT(now(),'%H:%i') or hours2 >= DATE_FORMAT(now(),'%H:%i'),1,2),
				if(hours1 <= DATE_FORMAT(now(),'%H:%i') and DATE_FORMAT(now(),'%H:%i') <= hours2,1,2)
			)),
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
		))) as isopen ";
        return $isopensql;
}

