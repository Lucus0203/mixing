<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Verify extends CI_Controller {
	var $_tags;
	var $_logininfo;
	function __construct() {
		parent::__construct ();
		$this->load->library ( array('session', 'common' , 'upload' , 'image_lib' ,'imgsizepress','pagination'));
		$this->load->helper ( array (
				'form',
				'url',
				'path' 
		) );
		$this->load->model ( array (
				'order_model',
				'encouter_model',
                                'shopverify_model'  
		) );

		$this->_logininfo=$this->session->userdata('loginInfo');
		if (empty ( $this->_logininfo )) {
			redirect ( 'login', 'index' );
		}else{
			$this->load->vars(array('loginInfo'=>$this->_logininfo));
		}
                //分页
                $config['total_rows'] = 10;
                $config['use_page_numbers'] = TRUE;
                $config['page_query_string'] = TRUE;
                $config['next_link'] = '&gt;';
                $config['prev_link'] = '&lt;';
                $config['first_link'] = '«';
                $config['last_link'] = '»';

                $config['full_tag_open'] = '<p class="pageClass">';
                $config['full_tag_close'] = '</p>';
                $this->pagination->initialize($config);
                
	}
	
	public function index() {
		redirect ( 'index.php/index.html' );
	}
	
	/**
	 *验证领取验证码
	 *
	 **/
	public function verify() {
		$loginInfo = $this->session->userdata ( 'loginInfo' );
		$msg = '';
		$act=$this->input->post('act');
                $page = $this->input->get('per_page');
                $page = $page*1 < 1 ? 1 :$page;
                
                if(!empty($act)){
                    $verifycode=str_replace(' ', '',$this->input->post('verifycode'));
                    //判断是否验证过
                    $verify=$this->shopverify_model->getRow(array('shop_id'=>$loginInfo['shop_id'],'verifycode'=>$verifycode));
                    //已于 2015-11-19 20:17:42 在 K3 COFFEE 被账号：15000085200 验证过了，请核实!
                    if(!empty($verify)){
                        $msg = '此验证码已于 '.$verify['created'].' 被账号：'.$verify['mobile'].' 验证过了，请核实!';
                    }else{
                        $sql="select encouter.id as encouter_id,encouter.shop_id,encouter.type,product1,product_img1,price1,product2,product_img2,price2,encouter.status,encouter.verifycode as encouter_verifycode,encouter_receive.choice_menu,encouter_receive.verifycode as receive_verifycode,depositer.mobile as depositer_mobile,receiver.mobile as receiver_mobile from ".$this->db->dbprefix('encouter')." encouter "
                                . "left join ".$this->db->dbprefix('user')." depositer on depositer.id = encouter.user_id "
                                . "left join ".$this->db->dbprefix('encouter_receive')." encouter_receive on encouter_receive.encouter_id=encouter.id and (encouter_receive.status=2 or encouter_receive.status=7) "
                                . "left join ".$this->db->dbprefix('user')." receiver on receiver.id = encouter_receive.from_user "
                            . " where (encouter.status=3 or encouter.status=6) and encouter.shop_id = ? and (encouter.verifycode=? or encouter_receive.verifycode=?) ";
                        $query=$this->db->query($sql,array($loginInfo['shop_id'],$verifycode,$verifycode));
                        if ($query->num_rows() > 0){
                            $row=$query->row_array ();
                            $encouterstatus=$row['status']==3?4:7;//4已领走,7等候已领走
                            //$this->encouter_model->update(array('status'=>$encouterstatus),$row['encouter_id']);
                            //验证记录
                            $shopverify=array('shop_id'=>$row['shop_id'],'verifycode'=>$verifycode,'note'=>$row['product1'],'img'=>$row['product_img1'],'price'=>$row['priwce1']);
                            
                            if($row['receive_verifycode']==$verifycode){//领取者
                                if($row['choice_menu']==2){
                                    $shopverify['note']=$row['product2'];
                                    $shopverify['img']=$row['product_img2'];
                                    $shopverify['price']=$row['price2'];
                                }else{
                                    $shopverify['note']=$row['product1'];
                                    $shopverify['img']=$row['product_img1'];
                                    $shopverify['price']=$row['price1'];
                                }
                                $shopverify['mobile']=$row['receiver_mobile'];
                            }else{//寄存者
                                if($row['choice_menu']==2){//领取者选择2 寄存者则是1
                                    $shopverify['note']=$row['product1'];
                                    $shopverify['img']=$row['product_img1'];
                                    $shopverify['price']=$row['price1'];
                                }else{
                                    $shopverify['note']=$row['product2'];
                                    $shopverify['img']=$row['product_img2'];
                                    $shopverify['price']=$row['price2'];
                                }
                                $shopverify['mobile']=$row['depositer_mobile'];
                            }
                            $this->shopverify_model->create($shopverify);
                            $msg = '验证成功';
                        }else{
                            $msg = '此验证码不存在，请与消费者确认提供的验证码是否正确！';
                        }
                    }
                }
                //$orders=$this->db->get_where ( 'shop_verify');
                $this->db->from('shop_verify');
                $this->db->where('shop_id',$loginInfo['shop_id']);
                $total_page= $this->db->count_all_results();
                $page_size=20;
                $config['base_url'] = 'verify.html';
                $config['per_page'] = $page_size;
                $config['total_rows'] = $total_page;
                $this->pagination->initialize($config);
                
                $this->db->from('shop_verify')->where('shop_id', $loginInfo['shop_id'])->limit($page_size, ($page-1)*$page_size);
                $query = $this->db->get();
		$orders = $query->result_array();
                
                //今日总金额
                $this->db->select_sum('price')->where(array('shop_id'=>$loginInfo['shop_id']," created >= " => date("Y-m-d 00:00:00")," created <= "=>  date("Y-m-d 23:59:59")));
                $query = $this->db->get('shop_verify');
                $total_amount=$query->row_array();
                $today_amount=$total_amount['price']*1;
                //历史总额
                $this->db->select_sum('price')->where(array('shop_id'=>$loginInfo['shop_id']));
                $query = $this->db->get('shop_verify');
                $total_amount=$query->row_array();
                $total_amount=$total_amount['price']*1;

                //$orders=$this->shopverify_model->getAll(array('shop_id'=>$loginInfo['shop_id']));
                $res = array('orders'=>$orders,'msg'=>$msg,'verifycode'=>$verifycode,'links'=>$this->pagination->create_links(),'today_amount'=>$today_amount,'total_amount'=>$total_amount);
		$this->load->view ( 'header');
		$this->load->view ( 'left' );
		$this->load->view ( 'verify/verify', $res );
		$this->load->view ( 'footer' );
	}
	
	
	
}
