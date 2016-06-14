<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Weixinmenu extends CI_Controller {
        var $_state;
	function __construct() {
		parent::__construct ();
		$this->load->library ( array('session', 'common' , 'wechat' ));
		$this->load->helper ( array (
				'form',
				'url',
				'path' 
		) );
		$this->load->model ( array (
				'diary_model',
				'votelist_model',
				'weixinuser_model'
		) );
	}
	
	public function createmenu() {
                $menu='{
                    "button": [
                        {
                            "type": "media_id", 
                            "name": "活动结果公布", 
                            "media_id": "o7V0uvzNMCxRgGGU94gKmm4lOZcTTy0TgLxr_j48QY0", 
                            "key": "button1"
                        }, 
                        {
                            "type": "view_limited", 
                            "name": "联系我们",
                            "media_id":"XqBjrd2TEKFRotp9vaLCMObJLEcqZ70shu53iDtpzfg",
                            "key":"K_CONTACT_US"
                        },
                        {
                            "type":"view",
                            "name":"下载搅拌(iOS)",
                            "url":"https://itunes.apple.com/cn/app/jiao-ban-zai-ka-fei-guan-yu/id1036717871?mt=8"
                    ]
                }';
                $res=$this->wechat->createMenu($menu);
                print_r($res);
	}
        
        public function getmedialist(){
            $type=$this->input->get('type');
            $offset=$this->input->get('offset');
            $count=$this->input->get('count');
            $res=$this->wechat->getMedialist($type,$offset,$count);
            print_r($res);
        }
        
	
	
}
