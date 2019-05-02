<?php
class Controller_Notify extends FLEA_Controller_Action {
	/**
	 * 
	 * Enter description here ...
	 * @var Class_Common
	 */
	var $_common;
        var $_notify;
	var $_admin;
	var $_adminid;
        var $_sms;
	
	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );
		$this->_sms = get_singleton ( "Class_Sms" );
                $this->_notify = get_singleton( "Model_Notify" );
		$this->_adminid = isset ( $_SESSION ['loginuserid'] ) ? $_SESSION ['loginuserid'] : "";
		if(empty($_SESSION ['loginuserid'])){
			$url=url("Default","Login");
			redirect($url);
		}
	}
	
	/**
	 * 消息
	 *消息类型eventInvitation(搭伴邀请)invitation(普通邀请函)receiver(领取咖啡通知)depositer(被领取咖啡通知)mixing(官方通知,链接方式)
	 */
        
	function actionIndex() {
		$config = FLEA::getAppInf ( 'dbDSN' );
		$prefix = $config ['prefix'];
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
		$sql="select * from ".$prefix."notify notify  where type='mixing' ".$conditions;
		$total=$this->_notify->findBySql("select count(*) as num from ($sql) s");
		$total=@$total[0]['num'];
		
		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "Notify", "Index" );
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;
		$list=$this->_notify->findBySql($sql." order by id desc limit $start,$page_size");
		
		$this->_common->show ( array ('main' => 'notify/notify.tpl','list'=>$list,'page'=>$page,'keyword'=>$keyword) );
	}
        
	function actionAddNotify() {
		$data=$_POST;
                $data['send_time'] =  date("Y-m-d H:i:s");
		$data=$this->_notify->create($data);
		redirect($_SERVER['HTTP_REFERER']);
	}
        function actionEditNotify(){
		$id=$this->_common->filter($_GET['id']);
		if(empty($id)){
			redirect($_SERVER['HTTP_REFERER']);
		}
		$msg='';
		$act=isset ( $_POST ['act'] ) ? $_POST ['act'] : '';
		if($act=='edit'){
			$data=$_POST;
			$this->_notify->update($data);
			$msg="更新成功";
		}
		$notifyg=$this->_notify->findByField('id',$id);
		
		$this->_common->show ( array ('main' => 'notify/notify_edit.tpl','data'=>$notifyg,'msg'=>$msg) );
		
	}
        function actionDelNotify(){//删除
		$id=$this->_common->filter($_GET['id']);
		$this->_notify->removeByPkv($id);
		redirect($_SERVER['HTTP_REFERER']);
	}
        
        function actionSendMobileMsg(){
		$mobile=$this->_common->filter($_GET['mobile']);
                $msg="恭喜您在参加的<胶片里的咖啡馆-搅拌APP>活动中获得了三等奖,奖品为瑞士军刀双肩背包一个,请把您的联系方式及邮寄地址发送给微信公众号'咖啡约我',工作人员会与您确认";
                if(!empty($mobile)){
                    $this->_sms->sendMsg($msg,$mobile);
                }else{
                    $mobile="参数手机号必须";
                }
                $this->_common->show ( array ('main' => 'notify/sendmobilemsg.tpl','msg'=>$msg,'mobile'=>$mobile) );
        }
	
	
	
}

?>