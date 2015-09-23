<?php
class Controller_ShopMenu extends FLEA_Controller_Action {
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
		$shopid = isset ( $_GET ['id'] ) ? $_GET ['id'] : '';
		$menu=$this->_shop_menu->findAll(array('shop_id'=>$shopid));
		foreach ($menu as $k=>$m){
			$menu[$k]['prices']=$this->_shop_menu_price->findAll(array('menu_id'=>$m['id']));
		}
		
		$this->_common->show ( array ('main' => 'shop/menu.tpl','menu'=>$menu,'shopid'=>$shopid) );
	}

	//ajax上传菜品
	function actionAjaxUploadShopMenu(){
		$shopid=$_POST['shopid'];
		$title=$_POST['title'];
		$file=$_POST['image-data'];
		
		$folder='../v2/upload/shopMenu/';
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
			//压缩图片
			$imgpress = & get_singleton ( "Service_ImgSizePress" );
			$imgpress->image_png_size_press($filepath,$filepath);
		
			$path=str_replace('../v2/',APP_SITE, $filepath);
			$pp = array (
					'shop_id' => $shopid,
					'title' => $title,
					'img' => $path,
					'status'=> 1,
					'created' => date ( "Y-m-d H:i:s" )
			);
			$id=$this->_shop_menu->create ( $pp );
			$img=$path;
		}else{
			$img=$id='';
		}
		$data=array('src'=>$img,'id'=>$id,'title'=>$title);
		echo json_encode($data);
	}
	
	// 删除菜品
	function actionDelMenu() {
		$pid=isset ( $_GET ['pid'] ) ? $_GET ['pid'] : '';
		$pid=$this->_common->filter($pid);
		//$pub_photo=$this->_shop_menu->findByField('id',$pid);
		//$this->delAppImg($pub_photo['img']);
		echo $this->_shop_menu->removeByPkv($pid);
	}
	


	/**
	 *菜品价格更新
	 */
	function actionMenuPriceUpdate(){
		$menuid=isset ( $_POST ['menuid'] ) ? $_POST ['menuid'] : '';
		$prices=isset ( $_POST ['prices'] ) ? $_POST ['prices'] : '';//1待售,2寄售中
		$prices=explode(',' , $prices);
		$typies=isset ( $_POST ['typies'] ) ? $_POST ['typies'] : '';
		$typies=explode(',' , $typies);
		$this->_shop_menu_price->removeByConditions(array('menu_id'=>$menuid));
                $menu=$this->_shop_menu->findByField('id',$menuid,null,"shop_id");
		foreach ($prices as $k=>$p){
			$mp=array('shop_id'=>$menu['shop_id'],'menu_id'=>$menuid,'price'=>$p,'type'=>$typies[$k]);
			$this->_shop_menu_price->create($mp);
		}
		echo 1;
	}
	
	/**
	 * 菜品上下架
	 */
	function actionMenuPublic(){
		$menuid=isset ( $_POST ['menuid'] ) ? $_POST ['menuid'] : '';
		$public=isset ( $_POST ['public'] ) ? $_POST ['public'] : '';//1待售,2寄售中
		//$menu = $this->_shop_menu->findByField(array('id'=>$menuid));
		$menu['id']=$menuid;
		$menu['status']=$public;
		echo $this->_shop_menu->update($menu);
		//echo 1;
	}
	
	
}