<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Event extends CI_Controller {
	function __construct() {
		parent::__construct ();
		$this->load->library ( array('session', 'common' , 'upload' , 'image_lib' ,'imgsizepress'));
		$this->load->helper ( array (
				'form',
				'url',
				'path' 
		) );
		$this->load->model ( array (
				'event_model',
				'eventimg_model'
		) );
	}
	
	public function index() {
		redirect ( 'index.php/index.html' );
	}
	
	public function info() {
		$p = $this->input->get ( 'p' );
                $eventid=base64_decode($p);
		$event = $this->event_model->getRow ( array (
				'id' => $eventid
		) );
                if(empty($event)){
                    echo '数据错误!';
                    return;
                }
                $event['imgs']= $this->eventimg_model->getAll ( array ('public_event_id' => $eventid ) );
		$res = array ('event' => $event);
		
		$this->load->view ( 'header',array('title'=>$event['title']));
		$this->load->view ( 'event/info', $res );
		$this->load->view ( 'footer' );
	}
	
	
	
}
