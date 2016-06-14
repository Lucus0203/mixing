<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Shop extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->library ( array('session', 'common' , 'upload' , 'image_lib' ,'imgsizepress'));
		$this->load->helper ( array (
				'form',
				'url',
				'path' 
		) );
		$this->load->model ( array (
				'shop_model',
				'menu_model',
				'shopimg_model',
		) );
	}
	
	public function index() {
		redirect ( 'index.php/index.html' );
	}
	
	public function info() {
		$s = $this->input->get ( 's' );
                $shopid=base64_decode($s);
		$msg = '';
		$shop = $this->shop_model->getRow ( array (
				'id' => $shopid
		) );
                if(empty($shop)){
                    echo '数据错误!';
                    return;
                }
                //特色
                $shoptagsql="select tag.id,base_tag.name from ".$this->db->dbprefix('shop_tag')." tag left join ".$this->db->dbprefix('base_shop_tag')." base_tag  on tag.tag_id = base_tag.id where tag.shop_id={$shopid} ";
		$query=$this->db->query($shoptagsql);
                foreach ($query->result() as $row){
                        $shop['features'][]=$row->name;
                }
                $shopimg = $this->shopimg_model->getAll ( array ('shop_id' => $shopid ) );
                //营业时间
                if(!empty($shop['hours1'])){
                    $hours=$shop['hours1'].'~'.$shop['hours2'];
                    $holiday="";
                    if($shop['holidayflag']!='1'){
                                if(strpos($shop['holidays'] , '1')!==false){
                                        $holiday.='一';
                                }
                                if(strpos($shop['holidays'] , '2')!==false){
                                        $holiday.= empty($holiday)?'二':',二';
                                }
                                if(strpos($shop['holidays'] , '3')!==false){
                                        $holiday.= empty($holiday)?'三':',三';
                                }
                                if(strpos($shop['holidays'] , '4')!==false){
                                        $holiday.= empty($holiday)?'四':',四';
                                }
                                if(strpos($shop['holidays'] , '5')!==false){
                                        $holiday.= empty($holiday)?'五':',五';
                                }
                                if(strpos($shop['holidays'] , '6')!==false){
                                        $holiday.= empty($holiday)?'六':',六';
                                }
                                if(strpos($shop['holidays'] , '0')!==false){
                                        $holiday.= empty($holiday)?'日':',日';
                                }
                        }
                        if($shop['holidayflag']=='3'){
                                $holiday = !empty($holiday)?' 周'.$holiday.':'.$shop['holidayhours1'].'~'.$shop['holidayhours2']:'';
                        }elseif($shop['holidayflag']=='2'){
                                $holiday = !empty($holiday)?' 休:周'.$holiday:'';
                        }
                }
                $shop['hours']=$hours.$holiday;
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
                                if($hours1<=date("H:i") && date("H:i")<=$hours2){
                                        $shop['isopen']=1;
                                }else{
                                        $shop['isopen']=2;
                                }
                        }
                }
                //菜品
                $shop['menus']= $this->menu_model->getAll ( array ('shop_id' => $shopid) );
		$res = array (
                        'shop' => $shop,
                        'shopimg' => $shopimg
		);
		
		$this->load->view ( 'header',array('title'=>$shop['title']));
		$this->load->view ( 'shop/info', $res );
		$this->load->view ( 'footer' );
	}
	
	
	
}
