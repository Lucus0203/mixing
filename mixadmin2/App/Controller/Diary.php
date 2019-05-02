<?php
class Controller_Diary extends FLEA_Controller_Action {
	/**
	 * 
	 * Enter description here ...
	 * @var Class_Common
	 */
	var $_common;
        var $_diary;
	var $_shop;
	var $_user;
	var $_admin;
	var $_adminid;
	
	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );
		$this->_shop = get_singleton ( "Model_Shop" );
		$this->_user = get_singleton ( "Model_User" );
                $this->_diary = get_singleton( "Model_Diary" );
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
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
                $conditions='';
		if(!empty($keyword)){
			$conditions.=" and (INSTR(shop.title,'".addslashes($keyword)."') or INSTR(diary.note,'".addslashes($keyword)."') or INSTR(user.nick_name,'".addslashes($keyword)."') )";
			$pageparm['keyword']=$keyword;
		}
                //shop_view_status 1 店铺查看公开 2店铺查看不公开
		$sql="select shop.title as shop_name,user.nick_name,diary.id as diary_id,diary.user_id,diary.shop_id,diary.note,diary.views,diary.beans,diary.shop_view_status from ".$prefix."diary diary "
                        . "left join ".$prefix."shop shop on shop.id=diary.shop_id "
                        . "left join ".$prefix."user user on user.id=diary.user_id "
                        . " where 1=1 ".$conditions;
		$total=$this->_diary->findBySql("select count(*) as num from ($sql) s");
		$total=@$total[0]['num'];
		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "Diary", "Index" );
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;
		$list=$this->_diary->findBySql($sql." order by diary.id desc limit $start,$page_size");
		
		$this->_common->show ( array ('main' => 'diary/diary_list.tpl','list'=>$list,'page'=>$page,'pageparm'=>$pageparm) );
	}
        
        
        /**
         * 屏蔽
         */
        function actionDeView(){
            $diaryid=$this->_common->filter($_GET['diary_id']);
            if(!empty($diaryid)){
		$diary=array('id'=>$diaryid,'shop_view_status'=>2);
		$this->_diary->update($diary);
            }
            redirect($_SERVER['HTTP_REFERER']);
        }
        
        /**
         * 公开
         * 
         */
        function actionDeViewCancel(){
            $diaryid=$this->_common->filter($_GET['diary_id']);
            if(!empty($diaryid)){
		$diary=array('id'=>$diaryid,'shop_view_status'=>1);
		$this->_diary->update($diary);
            }
            redirect($_SERVER['HTTP_REFERER']);
        }
        
        /**
         * 咖啡馆的慢生活
         */
        function actionShop(){
		$config = FLEA::getAppInf ( 'dbDSN' );
		$prefix = $config ['prefix'];
                $shopid=$this->_common->filter($_GET['shop_id']);
		$keyword = isset ( $_GET ['keyword'] ) ? trim($_GET ['keyword']) : '';
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
                $conditions='';
		if(!empty($keyword)){
			$conditions.=" and (INSTR(shop.title,'".addslashes($keyword)."') or INSTR(diary.note,'".addslashes($keyword)."') or INSTR(user.nick_name,'".addslashes($keyword)."') )";
			$pageparm['keyword']=$keyword;
		}
                //shop_view_status 1 店铺查看公开 2店铺查看不公开
		$sql="select shop.title as shop_name,user.nick_name,diary.id as diary_id,diary.shop_id,diary.user_id,diary.note,diary.views,diary.beans,diary.shop_view_status from ".$prefix."diary diary "
                        . "left join ".$prefix."shop shop on shop.id=diary.shop_id "
                        . "left join ".$prefix."user user on user.id=diary.user_id "
                        . " where diary.shop_id=$shopid ".$conditions;
		$total=$this->_diary->findBySql("select count(*) as num from ($sql) s");
		$total=@$total[0]['num'];
		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "Diary", "Index" );
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;
		$list=$this->_diary->findBySql($sql." order by diary.id desc limit $start,$page_size");
                
		$shop=$this->_shop->findByField('id',$shopid);
		
		$this->_common->show ( array ('main' => 'diary/diary_list.tpl','list'=>$list,'page'=>$page,'pageparm'=>$pageparm,'shop'=>$shop) );
        }
        
        /**
         * 用户的慢生活
         */
        function actionUser(){
		$config = FLEA::getAppInf ( 'dbDSN' );
		$prefix = $config ['prefix'];
                $userid=$this->_common->filter($_GET['user_id']);
		$keyword = isset ( $_GET ['keyword'] ) ? trim($_GET ['keyword']) : '';
		$page_no = isset ( $_GET ['page_no'] ) ? $_GET ['page_no'] : 1;
		$page_size = 20;
                $conditions='';
		if(!empty($keyword)){
			$conditions.=" and (INSTR(shop.title,'".addslashes($keyword)."') or INSTR(diary.note,'".addslashes($keyword)."') or INSTR(user.nick_name,'".addslashes($keyword)."') )";
			$pageparm['keyword']=$keyword;
		}
                //shop_view_status 1 店铺查看公开 2店铺查看不公开
		$sql="select shop.title as shop_name,user.nick_name,diary.id as diary_id,diary.shop_id,diary.user_id,diary.note,diary.views,diary.beans,diary.shop_view_status from ".$prefix."diary diary "
                        . "left join ".$prefix."shop shop on shop.id=diary.shop_id "
                        . "left join ".$prefix."user user on user.id=diary.user_id "
                        . " where diary.user_id=$userid ".$conditions;
		$total=$this->_diary->findBySql("select count(*) as num from ($sql) s");
		$total=@$total[0]['num'];
		$pages = & get_singleton ( "Service_Page" );
		$pages->_page_no = $page_no;
		$pages->_page_num = $page_size;
		$pages->_total = $total;
		$pages->_url = url ( "Diary", "Index" );
		$pages->_parm = $pageparm;
		$page = $pages->page ();
		$start = ($page_no - 1) * $page_size;
		$list=$this->_diary->findBySql($sql." order by diary.id desc limit $start,$page_size");
                
		$user=$this->_user->findByField('id',$userid);
		
		$this->_common->show ( array ('main' => 'diary/diary_list.tpl','list'=>$list,'page'=>$page,'pageparm'=>$pageparm,'user'=>$user) );
        }
        
        
        
        /**
         * 详情
         * 
         */
        function actionDetail(){
            $config = FLEA::getAppInf ( 'dbDSN' );
            $prefix = $config ['prefix'];
            $diaryid=$this->_common->filter($_GET['diary_id']);
            $sql="select diary.id as diary_id,diary.user_id,user.head_photo,user.nick_name,diary.views,diary.beans,diary.note,diary.voice,diary.voice_time,diary.shop_id,shop.title as shop_title,shop.img as shop_img,diary.created,diary.shop_view_status from ".$prefix."diary diary "
                    . "left join ".$prefix."user user on diary.user_id=user.id "
                    . "left join ".$prefix."shop shop on diary.shop_id=shop.id "
                    . "where diary.id = $diaryid ";//isdel 1删除2正常 //liked 1 已赞 2 未赞
            $diary=$this->_diary->findBySql($sql);
            $diary=$diary[0];

            //相册
            $imgsql="select id as img_id,img,width,height from ".$prefix."diary_img as img where diary_id=".$diaryid;
            $imgs=$this->_diary->findBySql($imgsql);
            if(count($imgs)>0){
                $diary['imgs']=$imgs;
            }
            //留言
            $msgsql="select msg.id as msg_id,msg.user_id as from_user_id,from_user.nick_name as from_nick_name,from_user.head_photo as from_head_photo,msg.to_user_id,to_user.nick_name as to_nick_name,to_user.head_photo as to_head_photo,msg.msg from ".$prefix."diary_msg msg left join " .$prefix. "user from_user on from_user.id=msg.user_id left join ".$prefix."user to_user on to_user.id=msg.to_user_id "
                    . "where msg.type = 1 and msg.diary_id = ".$diaryid;
            $msgs=$this->_diary->findBySql($msgsql);
            if(count($msgs)>0){
                $diary['msgs']=$msgs;
            }
            $this->_common->show ( array ('main' => 'diary/diary_detail.tpl','diary'=>$diary) );
            
        }
        
	
	
	
}

?>