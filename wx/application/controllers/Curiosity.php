<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Curiosity extends CI_Controller {
        var $_state;
	function __construct() {
		parent::__construct ();
		$this->load->database ();
		$this->load->library ( array('session', 'common' , 'wechat' , 'pagination' ));
		$this->load->helper ( array (
				'form',
				'url',
				'path' 
		) );
		$this->load->model ( array (
				'weixinuser_model'
		) );
                
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
	
	public function uploadimg() {
                $weixinUser=$this->session->userdata('weixinUser');
                if(empty($weixinUser['openid'])){
                    redirect ( 'curiosity/getcode' );
                }else{
                    $act=$this->input->post('act');
                    $res=array('user'=>$weixinUser);
                    if(!empty($act)){
                        $config['upload_path'] = './uploads/';
                        $config['allowed_types'] = 'gif|jpg|png|jpeg';
                        $config['file_name'] = $file_name = $weixinUser['id'].date("YmdHis");

                        $this->load->library('upload', $config);
                        if ( ! $this->upload->do_upload('file')){
                            $error = array('error' => $this->upload->display_errors());
                            $res['error']=$error['error'];
                        }else{
                            $img = $this->upload->data();
                            $obj=array('weixin_user_id'=>$weixinUser['id'],'img'=>$file_name.$img['file_ext'],'created'=>  date("Y-m-d H:i:s"));
                            $this->db->insert ( 'weixin_curiosity', $obj );
                            $id=$this->db->insert_id();
                            redirect("curiosity/showresult/".$id);
                            return;
                        }
                    }
                    $this->load->view ( 'curiosity/uploadimg', $res );
                }
	}
        
        //显示第一张
        public function showfirst($objId) {
            $weixinUser=$this->session->userdata('weixinUser');
            $this->session->set_userdata('curiosity_id',$objId);
            if(empty($weixinUser['openid'])){
                redirect ( 'curiosity/getcode' );
            }else{
                redirect ( 'curiosity/showresult/'.$objId );
            }
	}
        
        //显示第二张
        public function showsecond($objId) {
            $weixinUser=$this->session->userdata('weixinUser');
            if(empty($weixinUser['openid'])){
                $this->session->set_userdata('curiosity_id',$objId);
                redirect ( 'curiosity/getcode' );
            }else{
                $query = $this->db->get_where ( 'weixin_curiosity',  array('id' => $objId) );
                $obj=$query->row_array();
                $res=array('user'=>$weixinUser,'obj'=>$obj);
                $this->load->view ( 'curiosity/second', $res );
            }
	}
        
        //显示第三张
        public function showthird($objId) {
            $weixinUser=$this->session->userdata('weixinUser');
            if(empty($weixinUser['openid'])){
                $this->session->set_userdata('curiosity_id',$objId);
                redirect ( 'curiosity/getcode' );
            }else{
                $query = $this->db->get_where ( 'weixin_curiosity',  array('id' => $objId) );
                $obj=$query->row_array();
                $res=array('user'=>$weixinUser,'obj'=>$obj);
                $this->load->view ( 'curiosity/third', $res );
            }
	}
        
        //显示结果
        public function showresult($objId){
            if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') === false ) {
                echo "<script>window.location = 'https://itunes.apple.com/app/jiao-ban/id1036717871?l=zh&ls=1&mt=8';</script>";
                return;
            }
            $weixinUser=$this->session->userdata('weixinUser');
            $this->session->set_userdata('curiosity_id',$objId);
            if(empty($weixinUser['openid'])){
                redirect ( 'curiosity/getcode' );
            }else{
                //查询测试历史是否有玩过
                $curiosityobj=array('weixin_user_id'=>$weixinUser['id'],'weixin_curiosity_id'=>$objId);
                $query = $this->db->get_where ( 'weixin_curiosity_result',  $curiosityobj );
                $obj=$query->row_array();
                if($obj['percent']<=5){
                    //redirect('curiosity/showfirst/'.$objId);
                    $query = $this->db->get_where ( 'weixin_curiosity',  array('id' => $objId) );
                    $obj=$query->row_array();
                    $res=array('user'=>$weixinUser,'obj'=>$obj,'share'=>$obj['count']);
                    $this->db->where ( 'id', $objId );
                    $count=$obj['count']+1;
                    $this->db->update ( 'weixin_curiosity', array('count'=>$count) );
                    $this->load->view ( 'curiosity/first', $res );
                }else{
                    $listSql=" select nickname,headimgurl,percent,shakeSpeed,shakeCount from ".$this->db->dbprefix('weixin_curiosity_result')." curiosity_result left join ".$this->db->dbprefix('weixin_user')." user on curiosity_result.weixin_user_id=user.id where curiosity_result.weixin_curiosity_id={$objId} order by shakeSpeed*1 desc ";

                    $query = $this->db->query($listSql);
                    $list=$query->result_array ();
                    $query = $this->db->get_where ( 'weixin_curiosity',  array('id' => $objId) );
                    $curiosity=$query->row_array();
                    $res=array('user'=>$weixinUser,'data'=>$obj,'list'=>$list);
                    //echo $this->db->last_query();

                    $this->load->view ( 'curiosity/result', $res );
                }
            }
        }
        //get数据更新
        public function seeresult($percent,$shakeSpeed,$shakeCount,$step){
            $weixinUser=$this->session->userdata('weixinUser');
            $curiosity_id=$this->session->userdata('curiosity_id');
            $where=array('weixin_user_id'=>$weixinUser['id'],'weixin_curiosity_id'=>$curiosity_id);
            $this->db->where ( $where );
            if($this->db->count_all_results('weixin_curiosity_result')<=0){
                $this->db->insert ( 'weixin_curiosity_result', $where );
            }
            $query = $this->db->get_where ( 'weixin_curiosity_result',  $where );
            $curiosity_obj=$query->row_array();

            $data=array('percent'=>5);
            if($step==1){
                $data=array(
                    'percent'=>(5+15*$percent/100),
                    'shakeSpeed'=>$shakeSpeed,
                    'shakeCount'=>$shakeCount
                );
            }elseif($step==2){
                $data=array(
                    'percent'=>($curiosity_obj['percent']+35*$percent/100),
                    'shakeCount'=>$curiosity_obj['shakeCount']+$shakeCount
                );
            }elseif($step==3){
                $data=array(
                    'percent'=>($curiosity_obj['percent']+45*$percent/100),
                    'shakeCount'=>$curiosity_obj['shakeCount']+$shakeCount
                );
            }
            $this->db->where ( 'id', $curiosity_obj['id'] );
            $this->db->update ( 'weixin_curiosity_result', $data );//更新浏览数据
            echo '<script>window.location="'.  site_url('curiosity/showresult/'.$curiosity_id).'"</script>';exit();
        }
        
        //获取ajax更新数据
        public function upcuriositydata(){
                $percent=$this->input->post('percent');
                $shakeSpeed=$this->input->post('shakeSpeed');
                $shakeCount=$this->input->post('shakeCount');
                $step=$this->input->post('step');
                $weixinUser=$this->session->userdata('weixinUser');
                $curiosity_id=$this->session->userdata('curiosity_id');
                $where=array('weixin_user_id'=>$weixinUser['id'],'weixin_curiosity_id'=>$curiosity_id);
                $this->db->where ( $where );
                if($this->db->count_all_results('weixin_curiosity_result')<=0){
                    $this->db->insert ( 'weixin_curiosity_result', $where );
                }
                $query = $this->db->get_where ( 'weixin_curiosity_result',  $where );
                $curiosity_obj=$query->row_array();
                
                $data=array('percent'=>5);
                if($step==1){
                    $data=array(
                        'percent'=>(5+15*$percent/100),
                        'shakeSpeed'=>$shakeSpeed,
                        'shakeCount'=>$shakeCount
                    );
                }elseif($step==2){
                    $data=array(
                        'percent'=>($curiosity_obj['percent']+35*$percent/100),
                        'shakeCount'=>$curiosity_obj['shakeCount']+$shakeCount
                    );
                }elseif($step==3){
                    $data=array(
                        'percent'=>($curiosity_obj['percent']+45*$percent/100),
                        'shakeCount'=>$curiosity_obj['shakeCount']+$shakeCount
                    );
                }
                $this->db->where ( 'id', $curiosity_obj['id'] );
                $this->db->update ( 'weixin_curiosity_result', $data );//更新浏览数据
                //echo $this->db->last_query();
                echo 1;
        }
        
        //登录
        public function login(){
                $orgin_state=$this->session->userdata('state');
                $state=$this->input->get('state');
                $code=$this->input->get('code');
                if($orgin_state == $state){
                   $tokenData = $this->wechat->getTokenData($code);
                   if($tokenData->errcode == 40029){
                       echo '<p style="font-size:2.5em;text-align: center;padding: 10px;color:#fff;background-color:green;">授权成功<br/>请返回朋友圈重新打开此链接</p>';exit();
                   }
                   //print_r($tokenData);
//                   $info=array('access_token'=>$tokenData->access_token,
//                       'refresh_token'=>$tokenData->refresh_token,
//                       'openid'=>$tokenData->openid,
//                       'unionid'=>$tokenData->unionid);
                   $userData = $this->wechat->getUserInfo($tokenData->access_token,$tokenData->openid);
                   $info=array('access_token'=>$tokenData->access_token,
                       'refresh_token'=>$tokenData->refresh_token,
                       'openid'=>$tokenData->openid,
                       'unionid'=>$tokenData->unionid,
                       'nickname'=>$userData->nickname,
                       'sex'=>$userData->sex,
                       'province'=>$userData->province,
                       'city'=>$userData->city,
                       'country'=>$userData->country,
                       'headimgurl'=>$userData->headimgurl);
                   $dbuser=$this->weixinuser_model->getRow(array('openid'=>$tokenData->openid));
                   if(empty($dbuser['openid'])){
                        $info['id']=$this->weixinuser_model->create($info);
                   }else{
                        $this->weixinuser_model->update($info,$dbuser['id']);
                        $info['id']=$dbuser['id'];
                   }
                   $this->session->set_userdata('weixinUser',$info);
                   $objId=$this->session->userdata('curiosity_id');
                   if(!empty($objId)){
                       redirect('curiosity/showresult/'.$objId);
                   }else{
                        redirect('curiosity/uploadimg');
                   }
                }else{
                   redirect('curiosity/getcode');
                }
        }
        
        //获取code url
        public function getcode(){
                $state = rand(10000, 99999);
                $this->session->set_userdata('state',$state);
                $url=$this->wechat->getCodeRedirect(site_url('curiosity/login'),$state);
                redirect($url);
        }
	
	
}

