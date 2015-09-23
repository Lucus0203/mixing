<?php
class Controller_Shop extends FLEA_Controller_Action {
	/**
	 *
	 * Enter description here ...
	 * @var Class_Common
	 */
	var $_common;
	var $_user;
	var $_shop;
	var $_shop_bbs;
	var $_shop_menu;
	var $_shop_menu_price;
	var $_shop_img;
	var $_admin;
	var $_adminid;
	var $_tags;
	var $_address_city;
	var $_address_province;
	var $_address_town;
	var $_shop_addcity;
	var $_shop_addarea;
	var $_shop_addcircle;
        var $_base_shop_tag;
        var $_base_shop_tag_team;
        var $_shop_tag;

	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );

		$this->_user = get_singleton ( "Model_User" );
		$this->_shop = get_singleton ( "Model_Shop" );
		$this->_shop_bbs = get_singleton ( "Model_ShopBbs" );
		$this->_shop_menu = get_singleton ( "Model_ShopMenu" );
		$this->_shop_menu_price = get_singleton ( "Model_ShopMenuPrice" );
		$this->_shop_img = get_singleton ( "Model_ShopImg" );
		$this->_address_city = get_singleton ( "Model_AddressCity" );
		$this->_address_province = get_singleton ( "Model_AddressProvince" );
		$this->_address_town = get_singleton ( "Model_AddressTown" );
		$this->_shop_addcity = get_singleton ( "Model_ShopAddcity" );
		$this->_shop_addarea = get_singleton ( "Model_ShopAddarea" );
		$this->_shop_addcircle = get_singleton ( "Model_ShopAddcircle" );
		$this->_base_shop_tag = get_singleton ( "Model_BaseShopTag" );
		$this->_base_shop_tag_team = get_singleton ( "Model_BaseShopTagTeam" );
		$this->_shop_tag = get_singleton ( "Model_ShopTag" );
		$this->_adminid = isset ( $_SESSION ['loginuserid'] ) ? $_SESSION ['loginuserid'] : "";
		$this->_tags=array('休闲小憩','情侣约会','随便吃吃','朋友聚餐','可以刷卡','有下午茶',
							'家庭聚会','无线上网','供应早餐','有露天位','免费停车','有无烟区',
							'可送外卖','有景观位','是老字号','商务宴请','生日聚会','节目表演',
							'古典风格',
							'时尚风格',
							'运动主题',
							'萌喵主题',
							'女仆主题',
							'执事主题',
							'嘉宾驻唱',
							'有露天位',
							'购物中心',
							'大屏电影',
							'典雅古镇',
							'特色街道',
							'有景观位',
							'可送外卖',
							'可以刷卡',
							'供应早餐',
							'免费停车',
							'临时办公',
							'临近大学',
							'桌游',
							'书吧',
							'影吧',
							'简餐');
		if(empty($_SESSION ['loginuserid'])){
			$url=url("Default","Login");
			redirect($url);
		}
	}

	/**
	 * 店铺管理
	 *
	 */
	function actionIndex() {
		$pageparm = array ();
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
		$title = isset ( $_GET ['title'] ) ? trim($_GET ['title']) : '';
		$province_id = isset ( $_GET ['province_id'] ) ? $this->_common->filter($_GET ['province_id']) : '';
		$city_id = isset ( $_GET ['city_id'] ) ? $this->_common->filter($_GET ['city_id']) : '';
		$town_id = isset ( $_GET ['town_id'] ) ? $this->_common->filter($_GET ['town_id']) : '';
		$addprovince_id = isset ( $_GET ['addprovince_id'] ) ? $this->_common->filter($_GET ['addprovince_id']) : '';
		$addcity_id = isset ( $_GET ['addcity_id'] ) ? $this->_common->filter($_GET ['addcity_id']) : '';
		$addarea_id = isset ( $_GET ['addarea_id'] ) ? $this->_common->filter($_GET ['addarea_id']) : '';
		$addcircle_id = isset ( $_GET ['addcircle_id'] ) ? $this->_common->filter($_GET ['addcircle_id']) : '';

		$conditions=array();
		if(!empty($title)){
			$conditions[]=" (INSTR(title,'".addslashes($title)."') or INSTR(subtitle,'".addslashes($title)."') or INSTR(address,'".addslashes($title)."') )";
			$pageparm['title']=$title;
		}
		if(!empty($province_id)){
			$conditions['province_id']=$province_id;
			$pageparm['province_id']=$province_id;
		}
		if(!empty($city_id)){
			$conditions['city_id']=$city_id;
			$pageparm['city_id']=$city_id;
		}
		if(!empty($town_id)){
			$conditions['town_id']=$town_id;
			$pageparm['town_id']=$town_id;
		}
		if(!empty($addprovince_id)){
			$pageparm['addprovince_id']=$province_id;
		}
		if(!empty($addcity_id)){
			$conditions['addcity_id']=$addcity_id;
			$pageparm['addcity_id']=$addcity_id;
		}
		if(!empty($addarea_id)){
			$conditions['addarea_id']=$addarea_id;
			$pageparm['addarea_id']=$addarea_id;
		}
		if(!empty($addcircle_id)){
			$conditions['addcircle_id']=$addcircle_id;
			$pageparm['addcircle_id']=$addcircle_id;
		}

		$total=$this->_shop->findCount($conditions);

		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "Shop", "Index" );
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;

		$list=$this->_shop->findAll($conditions," status desc , recommend,id desc limit $start,$page_size");

		
		$provinces=$this->_address_province->findAll();
		$prov=$this->_address_province->findByField('id',$province_id);
		$city=$this->_address_city->findAll(array('provinceCode'=>$prov['code']));
		$ctow=$this->_address_city->findByField('id',$city_id);
		$towns=$this->_address_town->findAll(array('cityCode'=>$ctow['code']));
                
		$addcity=$this->_shop_addcity->findAll(array('province_id'=>$addprovince_id));
                $addarea=$this->_shop_addarea->findAll(array('city_id'=>$addcity_id));
                $addcircle=$this->_shop_addcircle->findAll(array('area_id'=>$addarea_id));
		$this->_common->show ( array ('main' => 'shop/shop_list.tpl','list'=>$list,'page'=>$page,'title'=>$title,'province_id'=>$province_id,'city_id'=>$city_id,'town_id'=>$town_id,'addprovince_id'=>$addprovince_id,'addcity_id'=>$addcity_id,'addarea_id'=>$addarea_id,'addcircle_id'=>$addcircle_id,'provinces'=>$provinces,'city'=>$city,'towns'=>$towns,'addcity'=>$addcity,'addarea'=>$addarea,'addcircle'=>$addcircle) );
	}

	function actionAdd(){
		$act=isset ( $_POST ['act'] ) ? $_POST ['act'] : '';
		if($act=='add'){
			$data=$_POST;
// 			//水印
// 			$ImgWaterMark= & get_singleton ( "Service_ImgWaterMark" );
// 			$waterpath=SERVERROOT.'/resource/images/watermark.png';
// 			$waterpath_menu=SERVERROOT.'/resource/images/watermark_menu.png';
			
// 			$Upload=$this->getUploadObj('shop');
// 			$img=$Upload->upload('img');
// 			if($img['status']==1){
// 				if($data['iswatermark']=='1'){
// 					$path=str_replace(APP_SITE,'../', $img['file_path']);
// 					$ImgWaterMark->imageWaterMark($path,9,$waterpath);
// 				}
// 				$data['img']=$img['file_path'];
// 			}
			//判断经纬度
			if(empty($data['lng'])||empty($data['lat'])){
				$lng=$this->_common->getLngFromBaidu($data['address']);
				$data['lng']=$lng['lng'];
				$data['lat']=$lng['lat'];
			}
                        
			$data ['hours1'] = $data ['hours1'].':'.$data ['minutes1'];
			$data ['hours2'] = $data ['hours2'].':'.$data ['minutes2'];
			$data ['holidays'] = implode(',', $data ['holidays']);
			$data ['holidayhours1'] = $data ['holidayhours1'].':'.$data ['holidayminutes1'];
			$data ['holidayhours2'] = $data ['holidayhours2'].':'.$data ['holidayminutes2'];
                        //营业时间
                        if(!empty($data['hours1'])){
                                $hours=$data['hours1'].'~'.$data['hours2'];
                                $holiday="";
                                if($data['holidayflag']!='1'){
                                        if(strpos($data['holidays'] , '1')!==false){
                                                $holiday.='一';
                                        }
                                        if(strpos($data['holidays'] , '2')!==false){
                                                $holiday.= empty($holiday)?'二':',二';
                                        }
                                        if(strpos($data['holidays'] , '3')!==false){
                                                $holiday.= empty($holiday)?'三':',三';
                                        }
                                        if(strpos($data['holidays'] , '4')!==false){
                                                $holiday.= empty($holiday)?'四':',四';
                                        }
                                        if(strpos($data['holidays'] , '5')!==false){
                                                $holiday.= empty($holiday)?'五':',五';
                                        }
                                        if(strpos($data['holidays'] , '6')!==false){
                                                $holiday.= empty($holiday)?'六':',六';
                                        }
                                        if(strpos($data['holidays'] , '0')!==false){
                                                $holiday.= empty($holiday)?'日':',日';
                                        }
                                }
                                if($data['holidayflag']=='2'){
                                        $holiday = !empty($holiday)?'  休息日:周'.$holiday:'';
                                }elseif($data['holidayflag']=='3'){
                                        $holiday = !empty($holiday)?'  休息日:周'.$holiday.' 时间:'.$data['holidayhours1'].'~'.$data['holidayhours2']:'';
                                }
                        }
                        $data['hours']=$hours.$holiday;
                        if(empty($data['province_id']))
                            $data['province_id']=NULL;
                        if(empty($data['city_id']))
                            $data['city_id']=NULL;
                        if(empty($data['town_id']))
                            $data['town_id']=NULL;
                        if(empty($data['addcity_id']))
                            $data['addcity_id']=NULL;
                        if(empty($data['addarea_id']))
                            $data['addarea_id']=NULL;
                        if(empty($data['addcircle_id']))
                            $data['addcircle_id']=NULL;
			$id=$this->_shop->create($data);
			
			//特色
                        foreach ($data['tags'] as $tag_id){
                            $tag=array('shop_id'=>$id,'tag_id'=>$tag_id);
                            $this->_shop_tag->create($tag);
                        }
			//更多店铺图片
// 			$imgs=$Upload->uploadFiles('shop_img');
// 			if($imgs['status']==1){
// 				foreach ($imgs['filepaths'] as $k => $p){
// 					if($data['iswatermark']=='1'){
// 						$path=str_replace(APP_SITE,'../', $p);
// 						$ImgWaterMark->imageWaterMark($path,9,$waterpath);
// 					}
// 					$shopimg=array('shop_id'=>$id,'img'=>$p);
// 					$this->_shop_img->create($shopimg);
// 				}
// 			}
			
// 			//菜单
// 			$Upload=$this->getUploadObj('shopMenu');
// 			$files=$Upload->uploadFiles('menu_img');
// 			if($files['status']==1){
// 				foreach ($files['filepaths'] as $k => $p){
// 					if($data['iswatermark']=='1'){
// 						$path=str_replace(APP_SITE,'../', $p);
// 						$ImgWaterMark->imageWaterMark($path,9,$waterpath_menu);
// 					}
// 					$mu=array('shop_id'=>$id,'title'=>$data['menu_title'][$k],'img'=>$p,'created'=>date("Y-m-d H:i:s"));
// 					$this->_shop_menu->create($mu);
// 				}
// 			}
			$url=url('Shop','Edit',array('id'=>$id));
			redirect($url);
		}
		
		$provinces=$this->_address_province->findAll();
		$prov=$this->_address_province->findByField('id',11);//广州
		$city=$this->_address_city->findAll(array('provinceCode'=>$prov['code']));
		$ctow=$this->_address_city->findByField('id',91);//广州
		$towns=$this->_address_town->findAll(array('cityCode'=>$ctow['code']),'code asc');
                $tagteam=$this->_base_shop_tag_team->findAll();
                foreach ($tagteam as $k=>$tm){
                    $tagteam[$k]['tags']=$this->_base_shop_tag->findAll(array('team_id'=>$tm['id']));
                }
                $basetags=$this->_base_shop_tag->findAll();
		
		$this->_common->show ( array ('main' => 'shop/shop_add.tpl','tags'=>$this->_tags,'tagteam'=>$tagteam,'provinces'=>$provinces,'city'=>$city,'towns'=>$towns) );
	}
	
	function actionEdit(){
		$id=isset ( $_GET ['id'] ) ? $_GET ['id'] : '';
		$act=isset ( $_POST ['act'] ) ? $_POST ['act'] : '';
		$msg='';
		if($act=='edit'){
			$data=$_POST;
			//判断经纬度
			if(empty($data['lng'])||empty($data['lat'])){
				$lng=$this->_common->getLngFromBaidu($data['address']);
				$data['lng']=$lng['lng'];
				$data['lat']=$lng['lat'];
			}
			//特色
			$feats=implode(",", $data['features']);
			$data['feature']=$feats;
			$data ['hours1'] = $data ['hours1'].':'.$data ['minutes1'];
			$data ['hours2'] = $data ['hours2'].':'.$data ['minutes2'];
			$data ['holidays'] = implode(',', $data ['holidays']);
			$data ['holidayhours1'] = $data ['holidayhours1'].':'.$data ['holidayminutes1'];
			$data ['holidayhours2'] = $data ['holidayhours2'].':'.$data ['holidayminutes2'];
                        //营业时间
                        if(!empty($data['hours1'])){
                                $hours=$data['hours1'].'~'.$data['hours2'];
                                $holiday="";
                                if($data['holidayflag']!='1'){
                                        if(strpos($data['holidays'] , '1')!==false){
                                                $holiday.='一';
                                        }
                                        if(strpos($data['holidays'] , '2')!==false){
                                                $holiday.= empty($holiday)?'二':',二';
                                        }
                                        if(strpos($data['holidays'] , '3')!==false){
                                                $holiday.= empty($holiday)?'三':',三';
                                        }
                                        if(strpos($data['holidays'] , '4')!==false){
                                                $holiday.= empty($holiday)?'四':',四';
                                        }
                                        if(strpos($data['holidays'] , '5')!==false){
                                                $holiday.= empty($holiday)?'五':',五';
                                        }
                                        if(strpos($data['holidays'] , '6')!==false){
                                                $holiday.= empty($holiday)?'六':',六';
                                        }
                                        if(strpos($data['holidays'] , '0')!==false){
                                                $holiday.= empty($holiday)?'日':',日';
                                        }
                                }
                                if($data['holidayflag']=='2'){
                                        $holiday = !empty($holiday)?'  休息日:周'.$holiday:'';
                                }elseif($data['holidayflag']=='3'){
                                        $holiday = !empty($holiday)?'  休息日:周'.$holiday.' 时间:'.$data['holidayhours1'].'~'.$data['holidayhours2']:'';
                                }
                        }
                        $data['hours']=$hours.$holiday;
                
                        if(empty($data['province_id']))
                            $data['province_id']=NULL;
                        if(empty($data['city_id']))
                            $data['city_id']=NULL;
                        if(empty($data['town_id']))
                            $data['town_id']=NULL;
                        if(empty($data['addcity_id']))
                            $data['addcity_id']=NULL;
                        if(empty($data['addarea_id']))
                            $data['addarea_id']=NULL;
                        if(empty($data['addcircle_id']))
                            $data['addcircle_id']=NULL;
                        
                        //特色
                        $this->_shop_tag->removeByConditions(array('shop_id'=>$id));
                        foreach ($data['tags'] as $tag_id){
                            $tag=array('shop_id'=>$id,'tag_id'=>$tag_id);
                            $this->_shop_tag->create($tag);
                        }
			$this->_shop->update($data);
			$msg="更新成功!";
		}
		$data=$this->_shop->findByField('id',$id);
		$menu=$this->_shop_menu->findAll(array('shop_id'=>$id));
		$shopimg=$this->_shop_img->findAll(array('shop_id'=>$id));
		//特色标签
		$feats=explode(',', $data['feature']);
		$feats=array_flip($feats);
		$tags=$this->_tags;
		foreach ($tags as $k=>$t){
			$tag=array('tag'=>$t,'checked'=>'');
			if(array_key_exists($t, $feats)){
				$tag['checked']='checked';
			}
			$tags[$k]=$tag;
		}
		//营业时间
		if(!empty($data['hours1'])){
			$hours=explode ( ':', $data ['hours1'] );
			$data ['hours1']=$hours[0];
			$data ['minutes1']=$hours[1];
		}
		if(!empty($data['hours2'])){
			$hours=explode ( ':', $data ['hours2'] );
			$data ['hours2']=$hours[0];
			$data ['minutes2']=$hours[1];
		}
		//休息日营业时间
		if(!empty($data['holidayhours1'])){
			$hours=explode ( ':', $data ['holidayhours1'] );
			$data ['holidayhours1']=$hours[0];
			$data ['holidayminutes1']=$hours[1];
		}
		if(!empty($data['holidayhours2'])){
			$hours=explode ( ':', $data ['holidayhours2'] );
			$data ['holidayhours2']=$hours[0];
			$data ['holidayminutes2']=$hours[1];
		}
		$data['province_id']=empty($data['province_id'])?19:$data['province_id'];
		$data['city_id']=empty($data['city_id'])?200:$data['city_id'];
		$provinces=$this->_address_province->findAll();
		$prov=$this->_address_province->findByField('id',$data['province_id']);//广州
		$city=$this->_address_city->findAll(array('provinceCode'=>$prov['code']));
		$ctow=$this->_address_city->findByField('id',$data['city_id']);//广州
		$towns=$this->_address_town->findAll(array('cityCode'=>$ctow['code']),'code asc');
                
		$addcity=$this->_shop_addcity->findAll(array('province_id'=>$data['province_id']));
		$addarea=$this->_shop_addarea->findAll(array('city_id'=>$data['addcity_id']));
		$addcircle=$this->_shop_addcircle->findAll(array('area_id'=>$data['addarea_id']));
                $shoptags=$this->_shop_tag->findAll(array('shop_id'=>$id));
                $tagteam=$this->_base_shop_tag_team->findAll();
                foreach ($tagteam as $k=>$tm){
                    $tags=$this->_base_shop_tag->findAll(array('team_id'=>$tm['id']));
                    foreach ($tags as $tk=>$tag){
                        foreach ($shoptags as $st){
                            if($st['tag_id']==$tag['id']){
                                $tags[$tk]['checked']=1;
                            }
                        }
                    }
                    $tagteam[$k]['tags']=$tags;
                }
                $basetags=$this->_base_shop_tag->findAll();
                foreach ($basetags as $key=>$t){
                    foreach ($shoptags as $st){
                        if($st['tag_id']==$t['id']){
                            $basetags[$key]['checked']=1;
                        }
                    }
                }
		
		$this->_common->show ( array ('main' => 'shop/shop_edit.tpl','data'=>$data,'menu'=>$menu,'shopimg'=>$shopimg,'msg'=>$msg,'tags'=>$tags,'shoptags'=>$shoptags,'tagteam'=>$tagteam,'provinces'=>$provinces,'city'=>$city,'towns'=>$towns,'addcity'=>$addcity,'addarea'=>$addarea,'addcircle'=>$addcircle) );
	}
	
	//上传店铺图片
	function actionAjaxUploadShopImg(){
		$shopid=$_POST['shopid'];
		$file=$_POST['image-data'];

		$folder='../v2/upload/shop/';
		if (! file_exists ( $folder )) {
			mkdir ( $folder, 0777 );
		}
		$dir = $folder . date ( "Ymd" ) . '/';
		if (! file_exists ( $dir )) {
			mkdir ( $dir, 0777 );
		}
		list($imgtype, $data) = explode(',', $file);
		// 判断类型
		if(strstr($imgtype,'image/jpeg')!==''){
			$ext = '.jpg';
		}elseif(strstr($imgtype,'image/gif')!==''){
			$ext = '.gif';
		}elseif(strstr($imgtype,'image/png')!==''){
			$ext = '.png';
		}
		// 生成的文件名
		$filepath = $dir.time().$ext;
		// 生成文件
		if (file_put_contents($filepath, base64_decode($data), true)) {
			//水印
// 			$ImgWaterMark= & get_singleton ( "Service_ImgWaterMark" );
// 			$waterpath=SERVERROOT.'/resource/images/watermark.png';
// 			$ImgWaterMark->imageWaterMark($filepath,9,$waterpath);
			//压缩图片
			$imgpress = & get_singleton ( "Service_ImgSizePress" );
			$imgpress->image_png_size_press($filepath,$filepath);
			list($width,$height,$type)=getimagesize($filepath);

			$path=str_replace('../v2/',APP_SITE, $filepath);
			$pp = array (
					'shop_id' => $shopid,
					'img' => $path,
					'width' => $width,
					'height' => $height,
					'created' => date ( "Y-m-d H:i:s" ) 
			);
			$id=$this->_shop_img->create ( $pp );
			$img=$path;
		}else{
			$img=$id='';
		}
		$data=array('src'=>$img,'id'=>$id);
		echo json_encode($data);
	}

	
	function actionDelShopImg(){//删除更多店铺图
		$pid=isset ( $_GET ['pid'] ) ? $_GET ['pid'] : '';
		$pid=$this->_common->filter($pid);
		$photo=$this->_shop_img->findByField('id',$pid);
		$this->delAppImg($photo['img']);
		echo $this->_shop_img->removeByPkv($pid);
	
	}
	
	function actionDel(){//删除
		$config = FLEA::getAppInf ( 'dbDSN' );
		
		$id=$this->_common->filter($_GET['id']);
		$shopmenu=$this->_shop_menu->findAll(array('shop_id'=>$id));
		foreach ($shopmenu as $shopm){
			$this->delAppImg($shopm['img']);
			$this->_shop_menu->removeByPkv($shopm['id']);
			$this->_shop_menu_price->removeByConditions(array('shop_id'=>$id));
		}
		$shopimg=$this->_shop_img->findAll(array('shop_id'=>$id));
		foreach ($shopimg as $shopm){
			$this->delAppImg($shopm['img']);
			$this->_shop_img->removeByPkv($shopm['id']);
		}
		$this->_shop_bbs->removeByConditions(array('shop_id'=>$id));
		$this->_shop->removeByPkv($id);
		redirect($_SERVER['HTTP_REFERER']);
	}
	function actionPublic(){ //发布
		$id=$this->_common->filter($_GET['id']);
		$eve=array('id'=>$id,'status'=>1);
		$this->_shop->update($eve);
		redirect($_SERVER['HTTP_REFERER']);
	}
	function actionDePublic(){//不发布
		$id=$this->_common->filter($_GET['id']);
		$eve=array('id'=>$id,'status'=>2);
		$this->_shop->update($eve);
		redirect($_SERVER['HTTP_REFERER']);
	}
	function actionRecommend(){//推荐
		$id=$this->_common->filter($_GET['id']);
		$eve=array('id'=>$id,'recommend'=>1);
		$this->_shop->update($eve);
		redirect($_SERVER['HTTP_REFERER']);
	}
	function actionDeRecommend(){//不推荐
		$id=$this->_common->filter($_GET['id']);
		$eve=array('id'=>$id,'recommend'=>2);
		$this->_shop->update($eve);
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	//图片处理
	function delAppImg($path){
		if(!empty($path)){
			$file=str_replace(APP_SITE, '../v2/', $path);
			if(file_exists($file))
			unlink($file);
		}
	}
	function getUploadObj($f){
		$Upload= & get_singleton ( "Service_UpLoad" );
		$folder='../upload/'.$f.'/';
		if (! file_exists ( $folder )) {
			mkdir ( $folder, 0777 );
		}
		$Upload->setDir($folder.date("Ymd")."/");
		$Upload->setReadDir(APP_SITE.'upload/'.$f.'/'.date("Ymd")."/");
		return $Upload;
	}
	
	//判断地址重复
	function actionCheckShopRepeat(){
		$address=urldecode($_GET['address']);
		$count=$this->_shop->findCount(array("trim(address) = '$address'"));
		echo $count;
	}
	
	
}