<?php
$act=filter($_REQUEST['act']);
switch ($act){
	case 'getVer':
		getVer();//获取版本
		break;
	case 'getHotShopCity'://获取热门筛选城市
		getHotShopCity();
		break;
	case 'getShopCity'://获取所有筛选城市
		getShopCity();
		break;
	case 'getShopCityAreaCircle'://获取筛选商圈
		getShopCityAreaCircle();
		break;
	case 'getShopTag'://获取店铺标签数据
		getShopTag();
		break;
        case 'getUserTag'://获取人物个性标签
                getUserTag();
                break;
        case 'getTopic'://获取话题
                getTopic();
                break;
        case 'getQuestion'://获取问题
                getQuestion();
                break;
	case 'getCountryCityArea':
		getCountryCityArea();//获取全国区域数据
		break;
	default:
		break;
}
//获取版本
function getVer(){
	echo json_result(
                array('HotShopCity'=>'1.0',
                    'ShopCity'=>'1.0',
                    'ShopCityAreaCircle'=>'1.0',
                    'ShopTag'=>'1.0',
                    'UserTag'=>'1.0',
                    'Topic'=>'1.0',
                    'Question'=>'1.0',
                    'CountryCityArea'=>'1.0'
                    ));
}

//获取热门城市
function getHotShopCity(){
	global $db;
        $hotcity=array('北京','广州','杭州','厦门','大连');
        $hs='';
        foreach ($hotcity as $c) {
            $hs.=" or name='$c'";
        }
        $data=array();
	$sql="select id,name,pinyin,code from ".DB_PREFIX."shop_addcity city where name='上海' {$hs} ";
	$data=$db->getAllBySql($sql);
	echo json_result($data);
}


//筛选城市
function getShopCity(){
	global $db;
        $data=array();
	$sql="select id,name,pinyin,code from ".DB_PREFIX."shop_addcity city where 1=1 ";
	
	$z='A';
	for($i=1;$i<=26;$i++){
		$s=$sql." and pinyin='{$z}' ORDER BY convert(name using gbk) ";
                if($db->getCountBySql($s)>0){
                    $data[$z]=$db->getAllBySql($s);
                }
                ++$z;
	}
        
	echo json_result($data);
}

//获取筛选商圈
function getShopCityAreaCircle(){
	global $db;
	$circlefile=APP_DIR. '/upload/city_circle.db';
	$circledata = file_get_contents($circlefile);
        $data=array();
	if(empty($circledata)){
                $city=$db->getAll('shop_addcity',array(),array('id as city_id','code as city_code'));
		foreach ($city as $ck=>$c){
                        $areadata['citycircle']=$db->getAll('shop_addcircle',array('city_id'=>$c['city_id'],'type'=>2),array('id as circle_id','name as circle'));//热门商圈
                        $area=$db->getAll('shop_addarea',array('city_id'=>$c['city_id']),array('id as area_id','name as area'));
			foreach ($area as $ak=>$a){
				$circle=$db->getAll('shop_addcircle',array('area_id'=>$a['area_id']),array('id as circle_id','name as circle'));//区域商圈
                                if(count($circle)>0){
                                        $area[$ak]['circle']=$circle;
                                }
			}
                        $areadata['area']=$area;
                        if(!empty($areadata['citycircle'])||!empty($areadata['area'])){
                                $data[$c['city_code']]=$areadata;
                        }
		}
		$circledata=json_result($data);
		file_put_contents($circlefile, $circledata);
	}
	
        echo $circledata;
        
}

//获取店铺标签数据
function getShopTag(){
	global $db;
        $team=$db->getAll('base_shop_tag_team',array(),array('id as team_id','name as team'));
        foreach ($team as $k=>$v){
                $tags=$db->getAll('base_shop_tag',array('team_id'=>$v['team_id']),array('id as tag_id','name as tag_name'));
                $team[$k]['tags']=$tags;
        }
        echo json_result($team);
}

//获取人物个性标签
function getUserTag(){
        global $db;
        $team=$db->getAll('base_user_tag_team',array(),array('id as team_id','name as team'));
        foreach ($team as $k=>$v){
                $tags=$db->getAll('base_user_tag',array('team_id'=>$v['team_id']),array('id as tag_id','name as tag_name'));
                $team[$k]['tags']=$tags;
        }
        echo json_result($team);
}

//话题数据
function getTopic(){
        global $db;
        $data = $db->getAll('base_topic',array(),array()," order by recommend desc ");
        echo json_result($data);
}

//问题数据
function getQuestion(){
        global $db;
        $data = $db->getAll('base_question',array(),array()," order by recommend desc ");
        echo json_result($data);
}

//获取全国区域数据
function getCountryCityArea($return=false){
	global $db;
	$areafile=APP_DIR. '/upload/city_area.db';
	$ctime = filectime($areafile);
	$areadata = file_get_contents($areafile);
	if(empty($areadata)||(time() - $ctime)>=60*60*24*5){//五天
		$sql="select p.id,p.name from ".DB_PREFIX."address_province p ";
		$province=$db->getAllBySql($sql);
		foreach ($province as $pk=>$p){
			$sql="select c.id,c.name from ".DB_PREFIX."address_city c where c.province_id = {$p['id']} order by code asc ";
			$city=$db->getAllBySql($sql);
			foreach ($city as $ck=>$c){
                                //$pinyinsql="update ".DB_PREFIX."address_city set pinyin='".getFirstCharter($c['name'])."' where id={$c['id']} ";
                                //$db->getAllBySql($pinyinsql);
				$sql="select t.id,t.name from ".DB_PREFIX."address_town t where t.city_id = {$c['id']} order by code asc ";
				$town=$db->getAllBySql($sql);
				$city[$ck]['town']=$town;
			}
			$province[$pk]['city']=$city;
		}
		$res['province']=$province;
		$areadata=json_result($res);
		file_put_contents($areafile, $areadata);
	}
	
	if(!$return){
		echo $areadata;
	}else{
		return $areadata;
	}
}
