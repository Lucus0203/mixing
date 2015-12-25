<?php
class Controller_Order extends FLEA_Controller_Action {
	/**
	 * 
	 * Enter description here ...
	 * @var Class_Common
	 */
	var $_common;
        var $_order;
	var $_admin;
	var $_adminid;
	
	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );
                $this->_order = get_singleton( "Model_Order" );
		$this->_adminid = isset ( $_SESSION ['loginuserid'] ) ? $_SESSION ['loginuserid'] : "";
		if(empty($_SESSION ['loginuserid'])){
			$url=url("Default","Login");
			redirect($url);
		}
	}
	
	/**
	 * 订单
	 *
	 */
        
	function actionIndex() {
		$config = FLEA::getAppInf ( 'dbDSN' );
		$prefix = $config ['prefix'];
		$keyword = isset ( $_GET ['keyword'] ) ? trim($_GET ['keyword']) : '';
		$order_no = isset ( $_GET ['order_no'] ) ? trim($_GET ['order_no']) : '';
		$verifycode = isset ( $_GET ['verifycode'] ) ? trim($_GET ['verifycode']) : '';
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
                $conditions='';
		if(!empty($keyword)){
			$conditions.=" and (INSTR(body,'".addslashes($keyword)."') or INSTR(shop.title,'".addslashes($keyword)."') )";
			$pageparm['keyword']=$keyword;
		}
                if(!empty($order_no)){
                    $conditions.=" and order_no = $order_no ";
			$pageparm['order_no']=$order_no;
                }
                if(!empty($verifycode)){
                    $conditions.=" and (encouter.verifycode = '$verifycode' or encouter_receive.verifycode = '$verifycode') ";
			$pageparm['verifycode']=$verifycode;
                }
                //status 1正常2失效3退款4已退款
		$sql="select shop.title as shop_name,user.nick_name,user.mobile,oder.id as order_id,oder.shop_id,oder.user_id,oder.body,charge_id,oder.order_no,encouter_receive.verifycode as encouter_receive_code,encouter.verifycode as encouter_code,oder.amount,oder.paid,oder.status from ".$prefix."order oder "
                        . "left join ".$prefix."encouter encouter on encouter.id=oder.encouter_id "
                        . "left join ".$prefix."encouter_receive encouter_receive on encouter_receive.id=oder.encouter_receive_id "
                        . "left join ".$prefix."shop shop on shop.id=oder.shop_id "
                        . "left join ".$prefix."user user on user.id=oder.user_id "
                        . " where 1=1 ".$conditions;
		$total=$this->_order->findBySql("select count(*) as num from ($sql) s");
		$total=@$total[0]['num'];
		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "Notify", "Index" );
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;
		$list=$this->_order->findBySql($sql." order by oder.id desc limit $start,$page_size");
		
		$this->_common->show ( array ('main' => 'order/order_list.tpl','list'=>$list,'page'=>$page,'pageparm'=>$pageparm) );
	}
        
        
        /**
         * 已退款
         */
        function actionRefunded(){
            $orderid=$this->_common->filter($_GET['order_id']);
            if(!empty($orderid)){
		$order=array('id'=>$orderid,'status'=>4);
		$this->_order->update($order);
            }
            redirect($_SERVER['HTTP_REFERER']);
        }
        
        /**
         * 取消退款
         * 
         */
        function actionRefundedCancel(){
            $orderid=$this->_common->filter($_GET['order_id']);
            if(!empty($orderid)){
		$order=array('id'=>$orderid,'status'=>3);
		$this->_order->update($order);
            }
            redirect($_SERVER['HTTP_REFERER']);
        }
        
        /**
         * 详情
         * 
         */
        function actionDetail(){
            $orderid=$this->_common->filter($_GET['order_id']);
        }
        
	
	
	
}

?>