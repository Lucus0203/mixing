<?php
class Controller_BusinessCircle extends FLEA_Controller_Action {
	/**
	 * 
	 * Enter description here ...
	 * @var Class_Common
	 */
	var $_common;
	var $_user;
	var $_shop;
	var $_admin;
	var $_adminid;
	var $_address_city;
	var $_address_province;
	var $_address_town;
	var $_shop_addcity;
	var $_shop_addarea;
	var $_shop_addcircle;
	var $_business_circle;
	
	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );

		$this->_user = get_singleton ( "Model_User" );
		$this->_shop = get_singleton ( "Model_Shop" );
		$this->_address_city = get_singleton ( "Model_AddressCity" );
		$this->_address_province = get_singleton ( "Model_AddressProvince" );
		$this->_address_town = get_singleton ( "Model_AddressTown" );
		$this->_shop_addcity = get_singleton ( "Model_ShopAddcity" );
		$this->_shop_addarea = get_singleton ( "Model_ShopAddarea" );
		$this->_shop_addcircle = get_singleton ( "Model_ShopAddcircle" );
		$this->_business_circle = get_singleton ( "Model_BusinessCircle" );
		$this->_adminid = isset ( $_SESSION ['loginuserid'] ) ? $_SESSION ['loginuserid'] : "";
		if(empty($_SESSION ['loginuserid'])){
			$url=url("Default","Login");
			redirect($url);
		}
	}
	
	/**
	 * 商圈
	 *
	 */
	function actionIndex() {
		$config = FLEA::getAppInf ( 'dbDSN' );
		$prefix = $config ['prefix'];
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
		$province_id = isset ( $_GET ['province_id'] ) ? $this->_common->filter($_GET ['province_id']) : '';
		$city_id = isset ( $_GET ['city_id'] ) ? $this->_common->filter($_GET ['city_id']) : '';
		$area_id = isset ( $_GET ['area_id'] ) ? $this->_common->filter($_GET ['area_id']) : '';
		$type = isset ( $_GET ['type'] ) ? $this->_common->filter($_GET ['type']) : '';
		$keyword = isset ( $_GET ['keyword'] ) ? $this->_common->filter($_GET ['keyword']) : '';

		$pageparm = array ();
		$conditions = ' 1=1 ';
		if(!empty($province_id)){
			$conditions.=" and province.id =$province_id ";
			$pageparm['province_id']=$province_id;
		}
		if(!empty($city_id)){
			$conditions.=" and city.id =$city_id ";
			$pageparm['city_id']=$city_id;
		}
		if(!empty($area_id)){
			$conditions.=" and area.id =$area_id ";
			$pageparm['area_id']=$area_id;
		}
		if(!empty($type)){
			$conditions.=" and type =$type ";
			$pageparm['type']=$type;
		}
		if(!empty($keyword)){
			$conditions.=" and circle.name like '%$keyword%' ";
			$pageparm['keyword']=$keyword;
		}
		$sql="select province.name as province,city.name as city,area.name as area,circle.* from ".$prefix."shop_addcircle circle 
			left join ".$prefix."shop_addarea area on circle.area_id=area.id
			left join ".$prefix."shop_addcity city on circle.city_id=city.id
			left join ".$prefix."address_province province on city.province_id=province.id where ".$conditions;
		$total=$this->_business_circle->findBySql("select count(*) as num from ($sql) s");
		$total=@$total[0]['num'];
		
		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "BusinessCircle", "Index" );
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;

		$list=$this->_business_circle->findBySql($sql." order by circle.id desc limit $start,$page_size");
		$provinces=$this->_address_province->findAll();
		$city=$this->_shop_addcity->findAll(array('province_id'=>$province_id));
                $area=$this->_shop_addarea->findAll(array('city_id'=>$city_id));
		
		$this->_common->show ( array ('main' => 'businessCircle/list.tpl','list'=>$list,'page'=>$page,'province_id'=>$province_id,'city_id'=>$city_id,'area_id'=>$area_id,'type'=>$type,'keyword'=>$keyword,'provinces'=>$provinces,'city'=>$city,'area'=>$area,'towns'=>$towns) );
	}
	

	function actionAddCity(){ //添加城市
		$data=$_POST;
                foreach ($data['name'] as $n){
                    if(!empty($n)&&!empty($data['province_id'])){
                        $obj=array('province_id'=>$data['province_id'],'name'=>$n);
                        $obj['pinyin']=$this->_common->getFirstCharter($n);
                        $geo=$this->_common->getLngFromBaidu($n);
                        $cityCode=$this->_common->getCityCodeFromBaidu($geo['lng'], $geo['lat']);
                        if(!empty($cityCode)){
                            $obj['code']=$cityCode;
                            $this->_shop_addcity->create($obj);
                        }
                    }
                }
		redirect($_SERVER['HTTP_REFERER']);
	}
        
	function actionAddArea(){ //添加区域
		$data=$_POST;
                foreach ($data['name'] as $n){
                    if(!empty($n)&&!empty($data['province_id'])&&!empty($data['city_id'])){
                        $obj=array('province_id'=>$data['province_id'],'city_id'=>$data['city_id'],'name'=>$n);
                        $this->_shop_addarea->create($obj);
                    }
                }
		redirect($_SERVER['HTTP_REFERER']);
	}
        
	function actionAddCircle(){ //添加商圈
		$data=$_POST;
                foreach ($data['name'] as $n){
                    if(!empty($n)&&!empty($data['province_id'])&&!empty($data['city_id'])&&!empty($data['area_id'])){
                        $obj=array('province_id'=>$data['province_id'],'city_id'=>$data['city_id'],'area_id'=>$data['area_id'],'name'=>$n);
                        $this->_shop_addcircle->create($obj);
                    }
                }
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	function actionEdit(){//编辑商圈
		$id=$this->_common->filter($_GET['id']);
		if(empty($id)){
			redirect($_SERVER['HTTP_REFERER']);
		}
		$msg='';

		$act=isset ( $_POST ['act'] ) ? $_POST ['act'] : '';
		if($act=='edit'){
			$data=$_POST;
			$this->_shop_addcircle->update($data);
			$msg="更新成功";
		}
		
		$circle=$this->_shop_addcircle->findByField('id',$id);
		
		$provinces=$this->_address_province->findAll();
                $city_data=$this->_shop_addcity->findByField('id',$circle['city_id']);
		$city=$this->_shop_addcity->findAll(array('province_id'=>$city_data['province_id']));
		$area=$this->_shop_addarea->findAll(array('city_id'=>$circle['city_id']));
		
		$this->_common->show ( array ('main' => 'businessCircle/edit.tpl','data'=>$circle,'provinces'=>$provinces,'city_data'=>$city_data,'city'=>$city,'area'=>$area,'msg'=>$msg) );
	}
        
        function actionEditCity(){//编辑城市
		$id=$this->_common->filter($_GET['id']);
		$act=isset ( $_POST ['act'] ) ? $_POST ['act'] : '';
		if($act=='edit'){
			$data=$_POST;
			$this->_shop_addcity->update($data);
			$msg="更新成功";
		}
                $data=$this->_shop_addcity->findByField('id',$id);
                $this->_common->show ( array ('main' => 'businessCircle/city_edit.tpl','data'=>$data,'msg'=>$msg) );
        }
        
        function actionEditArea(){//编辑区域
		$id=$this->_common->filter($_GET['id']);
		$act=isset ( $_POST ['act'] ) ? $_POST ['act'] : '';
		if($act=='edit'){
			$data=$_POST;
			$this->_shop_addarea->update($data);
			$msg="更新成功";
		}
                $data=$this->_shop_addarea->findByField('id',$id);
                $this->_common->show ( array ('main' => 'businessCircle/area_edit.tpl','data'=>$data,'msg'=>$msg) );
        }
	
	function actionDel(){//删除商圈
		$id=$this->_common->filter($_GET['id']);
                //若有数据则不可删
                if($this->_shop->findCount(array('addcity_id'=>$id))>0){
                    redirect($_SERVER['HTTP_REFERER']);
                }else{
                    $this->_shop_addcircle->removeByPkv($id);
                    redirect($_SERVER['HTTP_REFERER']);
                }
	}
        
        function actionDelCity(){//删除城市
		$id=$this->_common->filter($_GET['id']);
                //若有数据则不可删
                if($this->_shop->findCount(array('addcity_id'=>$id))>0){
                    echo 2;
                    return;
                }
                if($this->_shop_addarea->findCount(array('city_id'=>$id))>0){
                    echo 2;
                    return;
                }
		if($this->_shop_addcity->removeByPkv($id)){
                    echo 1;
                }else{
                    echo 2;
                }
	}
        
        function actionDelArea(){//删除区域
		$id=$this->_common->filter($_GET['id']);
                //若有数据则不可删
                if($this->_shop->findCount(array('addarea_id'=>$id))>0){
                    echo 2;
                    return;
                }
                //若有数据则不可删
                if($this->_shop_addcircle->findCount(array('area_id'=>$id))>0){
                    echo 2;
                    return;
                }
		if($this->_shop_addarea->removeByPkv($id)){
                    echo 1;
                }else{
                    echo 2;
                }
	}
        
        //热门商圈
        function actionHotCircle(){
            $id=$this->_common->filter($_GET['id']);
            $type=$this->_common->filter($_GET['type']);
            if(!empty($id)&&!empty($type)){
                $data=array('id'=>$id,'type'=>$type);
                $this->_shop_addcircle->update($data);
            }
            redirect($_SERVER['HTTP_REFERER']);
        }
        
        //导出商圈
        function actionDownCsv(){
            $config = FLEA::getAppInf ( 'dbDSN' );
            $PREFIX = $config ['prefix'];
            $sql = "SELECT circle.name as circle,area.name as area,city.name as city,city.code as city_code 
                                                        FROM {$PREFIX}shop_addcircle AS circle
							LEFT JOIN {$PREFIX}shop_addarea AS area ON circle.`area_id`=area.`id` 
							LEFT JOIN {$PREFIX}shop_addcity AS city ON circle.`city_id`=city.`id` 
							";
		$sql .= ' ORDER BY circle.`id` ';
		$result = $this->_shop_addcircle->findBySql ( $sql );
		$data_csv .= "序号,城市,城市编号,区域,商圈" . base64_decode ( "DQo=" );
		$data_csv = mb_convert_encoding ( $data_csv, 'GBK', 'UTF-8' );
		$no = 0;
		for($i = 0; $i < count ( $result ); $i ++) {
                        $str = ++ $no . "," . $result [$i] ['city'] . "," . $result [$i] ['city_code'] . "," . $result [$i] ['area'] . "," . $result [$i] ['circle'] . "" . base64_decode ( "DQo=" );
                        $str = mb_convert_encoding ( $str, 'GBK', 'UTF-8' );
                        $data_csv .= $str;
		}
		$filename = "商圈数据" . date ( 'YmdHis' ) . '.csv';
		header ( "Cache-Control: public" );
		header ( 'Content-type: application/vnd.ms-excel' );
		header ( "Content-Disposition: attachment; filename=" . $filename );
		echo $data_csv;
		return;
        }
	
}

?>