<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Vote extends CI_Controller {
        var $_state;
	function __construct() {
		parent::__construct ();
		$this->load->library ( array('session', 'common' , 'wechat' , 'pagination' ));
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
                $weixinUser=$this->session->userdata('weixinUser');
                $votenum = $this->input->get('votenum',true);
                $page = $this->input->get('per_page',true);
                if(empty($weixinUser['openid'])){
                    redirect ( 'vote/getcode' );
                }else{
                    $votenum=$this->common->filterSql(intval(preg_replace('/($s*$)|(^s*^)/m', '',$votenum)));
                    $page=$this->common->filterSql(intval($page));
                    $page = $page*1 < 1 ? 1 :$page;
                    $page_size=5;

                    $sql="select diary.id as diary_id,diary.user_id,img,note,num,diary.created from ".$this->db->dbprefix('diary')." diary "
                            . "left join (select diary_id,img from ".$this->db->dbprefix('diary_img')." diary_img group by diary_id ) diary_img on diary_img.diary_id=diary.id "
                            . "left join (select sum(vote_list.num) as num,vote_list.diary_id from ".$this->db->dbprefix('vote_list')." vote_list group by diary_id ) s on diary.id=s.diary_id "
                            . " where (note like '%胶片里的咖啡馆%' or note like '%胶片里的咖啡厅%') and diary.created<='2016-02-14 23:59:59' ";
                            //. " where diary_img.img <> '' ";
                    if(!empty($votenum)){
                        $sql.=" and diary.id = '".$votenum."' ";
                    }
                    $sql.= " group by diary.id order by diary.created desc ";//num desc,
                    $sql=" select * from ($sql) o group by user_id order by o.created desc";
                    $query = $this->db->query("select count(*) as num from ($sql) s ");
                    $num=$query->row_array ();
                    $total_page = $num['num'];
                    $sql="select d.*,user.nick_name from ($sql) d left join ".$this->db->dbprefix('user')." user on user.id = d.user_id order by d.num desc ";
                    $sql.=" limit ".($page-1)*$page_size.",".$page_size;
                    $query = $this->db->query($sql);
                    $diarys=$query->result_array ();
                    $config['base_url'] = 'index.html';
                    $config['per_page'] = $page_size;
                    $config['total_rows'] = $total_page;
                    $this->pagination->initialize($config);
                    
                    $votedSql="select count(id) as num from ".$this->db->dbprefix('vote_list')." votelist where vote_user_id='".$weixinUser['id']."' and  created > '".date("Y-m-d 00:00:00")."' and created <='".date("Y-m-d 23:59:59")."' ";
                    $query = $this->db->query($votedSql);
                    $isvotednum=$query->row_array ();
                    //print_r($isvotednum);
                    if($isvotednum['num']<=0){
                        $voteflag=1;//可以投票
                    }else{
                        $voteflag=2;//不可以投票
                    }
                    $res = array (
                        'diarys' => $diarys,
                        'voteflag' => $voteflag,
                        'links'=>$this->pagination->create_links(),
                        'votenum'=>$votenum,
                        'page'=>$page
                    );
                    $this->load->view ( 'vote/index', $res );
                }
	}
        
        function insertk3vote(){
            array('');
        }
        
        //投票测试预览画面
        public function voteinfo() {
                $weixinUser=$this->session->userdata('weixinUser');
                $votenum = $this->input->get('votenum',true);
                $page = $this->input->get('per_page',true);
                if(empty($weixinUser['openid'])){
                    redirect ( 'vote/getcode' );
                }else{
                    $votenum=$this->common->filterSql(intval(preg_replace('/($s*$)|(^s*^)/m', '',$votenum)));
                    $page=$this->common->filterSql(intval($page));
                    $page = $page*1 < 1 ? 1 :$page;
                    $page_size=5;

                    $sql="select diary.id as diary_id,diary.user_id,img,note,num,diary.created from ".$this->db->dbprefix('diary')." diary "
                            . "left join (select diary_id,img from ".$this->db->dbprefix('diary_img')." diary_img group by diary_id ) diary_img on diary_img.diary_id=diary.id "
                            . "left join (select sum(vote_list.num) as num,vote_list.diary_id from ".$this->db->dbprefix('vote_list')." vote_list group by diary_id ) s on diary.id=s.diary_id "
                            . " where (note like '%胶片里的咖啡馆%' or note like '%胶片里的咖啡厅%') ";
                            //. " where diary_img.img <> '' ";
                    if(!empty($votenum)){
                        $sql.=" and diary.id = '".$votenum."' ";
                    }
                    $sql.= " group by diary.id order by diary.created desc ";//num desc,
                    $sql=" select * from ($sql) o group by user_id order by o.created desc";
                    $query = $this->db->query("select count(*) as num from ($sql) s ");
                    $num=$query->row_array ();
                    $total_page = $num['num'];
                    $sql.=" limit ".($page-1)*$page_size.",".$page_size;
                    $sql="select d.*,user.nick_name from ($sql) d left join ".$this->db->dbprefix('user')." user on user.id = d.user_id order by d.num desc,d.created desc ";
                    $query = $this->db->query($sql);
                    $diarys=$query->result_array ();
                    $config['base_url'] = 'voteinfo.html';
                    $config['per_page'] = $page_size;
                    $config['total_rows'] = $total_page;
                    $this->pagination->initialize($config);
                    
                    $votedSql="select count(id) as num from ".$this->db->dbprefix('vote_list')." votelist where vote_user_id='".$weixinUser['id']."' and  created > '".date("Y-m-d 00:00:00")."' and created <='".date("Y-m-d 23:59:59")."' ";
                    $query = $this->db->query($votedSql);
                    $isvotednum=$query->row_array ();
                    //print_r($isvotednum);
                    if($isvotednum['num']<=0){
                        $voteflag=1;//可以投票
                    }else{
                        $voteflag=2;//不可以投票
                    }
                    $res = array (
                        'diarys' => $diarys,
                        'voteflag' => $voteflag,
                        'links'=>$this->pagination->create_links(),
                        'votenum'=>$votenum,
                        'page'=>$page
                    );
                    $this->load->view ( 'vote/voteinfo', $res );
                }
	}
        
        //通知投票成功画面
        public function participate(){
                $votenum = $this->input->get('votenum',true);
                $votenum=$this->common->filterSql(intval(preg_replace('/($s*$)|(^s*^)/m', '',$votenum)));
                
                $sql="select diary.id as diary_id,diary.user_id,img,note,num,diary.created from ".$this->db->dbprefix('diary')." diary "
                        . "left join (select diary_id,img from ".$this->db->dbprefix('diary_img')." diary_img group by diary_id ) diary_img on diary_img.diary_id=diary.id "
                        . "left join (select count(vote_list.id) as num,vote_list.diary_id from ".$this->db->dbprefix('vote_list')." vote_list group by diary_id ) s on diary.id=s.diary_id "
                        . " where (note like '%胶片里的咖啡馆%' or note like '%胶片里的咖啡厅%') ";
                        //. " where diary_img.img <> '' ";
                if(!empty($votenum)){
                    $sql.=" and diary.id = '".$votenum."' ";
                }
                $sql.= " group by diary.id order by diary.created desc ";//num desc,
                $sql=" select * from ($sql) o group by user_id order by o.created desc";
                $sql="select d.*,user.nick_name from ($sql) d left join ".$this->db->dbprefix('user')." user on user.id = d.user_id order by d.created desc ";
                $query = $this->db->query($sql);
                $d=$query->row_array ();
                $res = array (
                    'd' => $d,
                    'links'=>$this->pagination->create_links(),
                    'votenum'=>$votenum
                );
                $this->load->view ( 'vote/participate', $res );
                    
        }
        
        //投票
        public function vote(){
                $weixinUser=$this->session->userdata('weixinUser');
                $votenum=$this->input->get('votenum',true);
                if(empty($weixinUser['unionid'])){
                    redirect ( 'vote/getcode' );
                }else{
                    $votenum=$this->common->filterSql(intval($votenum));
                    //查看今日是否投过票
                    $votedSql="select count(id) as num from ".$this->db->dbprefix('vote_list')." votelist where vote_user_id='".$weixinUser['id']."' and  created > '".date("Y-m-d 00:00:00")."' and created <='".date("Y-m-d 23:59:59")."' ";
                    $query = $this->db->query($votedSql);
                    $isvotednum=$query->row_array ();
                    if($isvotednum['num']<=0){
                        $obj=array('vote_user_id'=>$weixinUser['id'],'diary_id'=>$votenum);
                        $this->votelist_model->create($obj);
                    }
                    //$_SERVER['HTTP_REFERER']
                    //redirect("vote/index");
                    redirect($_SERVER['HTTP_REFERER']);
                }
        }
        
        //投票登录
        public function login(){
                $orgin_state=$this->session->userdata('state');
                $state=$this->input->get('state');
                $code=$this->input->get('code');
                if($orgin_state == $state){
                   $tokenData = $this->wechat->getTokenData($code);
                   if($tokenData->errcode == 40029){
                       echo '<p style="font-size:2.5em;text-align: center;padding: 10px;color:#fff;background-color:green;">自动登录成功<br/>请返回朋友圈重新打开此链接</p>';exit();
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
                        $this->session->set_userdata('weixinUser',$info);
                   }else{
                        $this->session->set_userdata('weixinUser',$dbuser);
                   }
                   redirect('vote/index');
                }else{
                   redirect('vote/getcode');
                }
        }
        
        //获取code url
        public function getcode(){
                $state = rand(10000, 99999);
                $this->session->set_userdata('state',$state);
                $url=$this->wechat->getCodeRedirect(site_url('vote/login'),$state);
                redirect($url);
        }
	
	
}

