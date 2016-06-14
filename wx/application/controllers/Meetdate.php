<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Meetdate extends CI_Controller {
        var $_state;
	function __construct() {
		parent::__construct ();
		$this->load->database ();
		$this->load->helper ( array (
				'form',
				'url',
				'path' 
		) );
	}
	
	public function create() {
            $act=$this->input->post('act');
            if(!empty($act)){
                $year=$this->input->post('year');
                $month=$this->input->post('month');
                $day=$this->input->post('day');
                $hours=$this->input->post('hours');
                $minutes=$this->input->post('minutes');
                $seconds=$this->input->post('seconds');
                $from_user=$this->input->post('from_user');
                $to_user=$this->input->post('to_user');
                $msg=$this->input->post('msg');
                $obj=array('year'=>$year,'month'=>$month,'day'=>$day,'hours'=>$hours,'minutes'=>$minutes,'seconds'=>$seconds,'from_user'=>$from_user,'to_user'=>$to_user,'msg'=>$msg,'created'=>date("Y-m-d H:i:s"));
                $this->db->insert ( 'weixin_meettime', $obj );
                $id=$this->db->insert_id();
                redirect("meetdate/analysis/".$id);
                return;
            }
            $this->load->view ( 'meetdate/create' );
	}
        
        //显示第一张
        public function analysis($objId) {
            if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false ) {
                echo "<script>window.location = 'https://itunes.apple.com/app/jiao-ban/id1036717871?l=zh&ls=1&mt=8';</script>";
                return;
            }
            $where=array('id'=>$objId);
            $query = $this->db->get_where ( 'weixin_meettime', $where );
            $data = $query->row_array ();
            if(empty($data['id'])){
                redirect ( 'meetdate/create' );
            }else{
                $res=array('data'=>$data);
                $this->load->view ( 'meetdate/analysis',$res );
            }
	}
        
	
}

