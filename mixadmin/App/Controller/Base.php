<?php
class Controller_Base extends FLEA_Controller_Action {
	/**
	 * 
	 * 基础数据
	 * @var Class_Common
	 */
	var $_common;
	var $_user;
	var $_shop_tag;
        var $_shop_tag_team;
        var $_user_tag_team;
	var $_user_tag;
	var $_topic;
	var $_question;
	var $_admin;
	var $_adminid;
	
	function __construct() {
		$this->_common = get_singleton ( "Class_Common" );

		$this->_user = get_singleton ( "Model_User" );
		$this->_shop_tag = get_singleton ( "Model_BaseShopTag" );
		$this->_shop_tag_team = get_singleton ( "Model_BaseShopTagTeam" );
		$this->_user_tag = get_singleton ( "Model_BaseUserTag" );
		$this->_user_tag_team = get_singleton ( "Model_BaseUserTagTeam" );
		$this->_topic = get_singleton ( "Model_BaseTopic" );
		$this->_question = get_singleton ( "Model_BaseQuestion" );
		$this->_user_event_bbs = get_singleton ( "Model_UserEventBbs" );
		$this->_adminid = isset ( $_SESSION ['loginuserid'] ) ? $_SESSION ['loginuserid'] : "";
		if(empty($_SESSION ['loginuserid'])){
			$url=url("Default","Login");
			redirect($url);
		}
	}
	
	function actionIndex() {
		$this->_common->show ( array ('main' => 'base/index.tpl') );
	}
	
        /**
         * 用户标签开始
         */
	function actionUserTag() {
            $config = FLEA::getAppInf ( 'dbDSN' );
		$prefix = $config ['prefix'];
                $sql="select tag.id,tag.name,team.name as team from ".$prefix."base_user_tag tag left join ".$prefix."base_user_tag_team team on tag.team_id=team.id order by tag.id desc";
		$list=$this->_user_tag->findBySql($sql);
                $team=$this->_user_tag_team->findAll();
		$this->_common->show ( array ('main' => 'base/usertag_list.tpl','list'=>$list,'team'=>$team) );
	}
	function actionAddUserTag() {
		$data=$_POST;
		$data=$this->_user_tag->create($data);
		redirect($_SERVER['HTTP_REFERER']);
	}
	function actionAddUserTagTeam() {
		$data=$_POST;
		$data=$this->_user_tag_team->create($data);
		redirect($_SERVER['HTTP_REFERER']);
	}
        function actionEditUserTag(){
		$id=$this->_common->filter($_GET['id']);
		if(empty($id)){
			redirect($_SERVER['HTTP_REFERER']);
		}
		$msg='';
		$act=isset ( $_POST ['act'] ) ? $_POST ['act'] : '';
		if($act=='edit'){
			$data=$_POST;
			$this->_user_tag->update($data);
			$msg="更新成功";
		}
		$usertag=$this->_user_tag->findByField('id',$id);
                $team=$this->_user_tag_team->findAll();
		
		$this->_common->show ( array ('main' => 'base/usertag_edit.tpl','data'=>$usertag,'team'=>$team,'msg'=>$msg) );
		
	}
        function actionDelUserTag(){//删除
		$id=$this->_common->filter($_GET['id']);
		$this->_user_tag->removeByPkv($id);
		redirect($_SERVER['HTTP_REFERER']);
	}
        
        /**
         * 店铺特色标签
         */
	function actionShopTag() {
		$config = FLEA::getAppInf ( 'dbDSN' );
		$prefix = $config ['prefix'];
                $sql="select tag.id,tag.name,team.name as team from ".$prefix."base_shop_tag tag left join ".$prefix."base_shop_tag_team team on tag.team_id=team.id order by tag.id desc";
		$list=$this->_shop_tag->findBySql($sql);
                $team=$this->_shop_tag_team->findAll();
		$this->_common->show ( array ('main' => 'base/shoptag_list.tpl','list'=>$list,'team'=>$team) );
	}
	function actionAddShopTag() {
		$data=$_POST;
                if(!empty($data['team_id'])){
                    $data=$this->_shop_tag->create($data);
                }
		redirect($_SERVER['HTTP_REFERER']);
	}
	function actionAddShopTagTeam() {
		$data=$_POST;
		$data=$this->_shop_tag_team->create($data);
		redirect($_SERVER['HTTP_REFERER']);
	}
        function actionEditShopTag(){
		$id=$this->_common->filter($_GET['id']);
		if(empty($id)){
			redirect($_SERVER['HTTP_REFERER']);
		}
		$msg='';
		$act=isset ( $_POST ['act'] ) ? $_POST ['act'] : '';
		if($act=='edit'){
			$data=$_POST;
			$this->_shop_tag->update($data);
			$msg="更新成功";
		}
		$usertag=$this->_shop_tag->findByField('id',$id);
                $team=$this->_shop_tag_team->findAll();
		
		$this->_common->show ( array ('main' => 'base/shoptag_edit.tpl','data'=>$usertag,'team'=>$team,'msg'=>$msg) );
		
	}
        function actionDelShopTag(){//删除
		$id=$this->_common->filter($_GET['id']);
		$this->_shop_tag->removeByPkv($id);
		redirect($_SERVER['HTTP_REFERER']);
	}
        
        /**
         * 话题数据
         */
	function actionTopic() {
		$list=$this->_topic->findAll();
		$this->_common->show ( array ('main' => 'base/topic_list.tpl','list'=>$list) );
	}
	function actionAddTopic() {
		$data=$_POST;
		$data=$this->_topic->create($data);
		redirect($_SERVER['HTTP_REFERER']);
	}
        function actionEditTopic(){
		$id=$this->_common->filter($_GET['id']);
		if(empty($id)){
			redirect($_SERVER['HTTP_REFERER']);
		}
		$msg='';
		$act=isset ( $_POST ['act'] ) ? $_POST ['act'] : '';
		if($act=='edit'){
			$data=$_POST;
			$this->_topic->update($data);
			$msg="更新成功";
		}
		$usertag=$this->_topic->findByField('id',$id);
		
		$this->_common->show ( array ('main' => 'base/topic_edit.tpl','data'=>$usertag,'msg'=>$msg) );
		
	}
        function actionDelTopic(){//删除
		$id=$this->_common->filter($_GET['id']);
		$this->_topic->removeByPkv($id);
		redirect($_SERVER['HTTP_REFERER']);
	}
        function actionRecommendTopic(){//推荐
		$id=$this->_common->filter($_GET['id']);
                $data=array('id'=>$id,'recommend'=>2);
		$this->_topic->update($data);
		redirect($_SERVER['HTTP_REFERER']);
	}
        function actionUnRecommendTopic(){//不推荐
		$id=$this->_common->filter($_GET['id']);
                $data=array('id'=>$id,'recommend'=>1);
		$this->_topic->update($data);
		redirect($_SERVER['HTTP_REFERER']);
	}
        
        /**
         * 问题数据
         */
	function actionQuestion() {
		$list=$this->_question->findAll();
		$this->_common->show ( array ('main' => 'base/question_list.tpl','list'=>$list) );
	}
	function actionAddQuestion() {
		$data=$_POST;
		$data=$this->_question->create($data);
		redirect($_SERVER['HTTP_REFERER']);
	}
        function actionEditQuestion(){
		$id=$this->_common->filter($_GET['id']);
		if(empty($id)){
			redirect($_SERVER['HTTP_REFERER']);
		}
		$msg='';
		$act=isset ( $_POST ['act'] ) ? $_POST ['act'] : '';
		if($act=='edit'){
			$data=$_POST;
			$this->_question->update($data);
			$msg="更新成功";
		}
		$usertag=$this->_question->findByField('id',$id);
		
		$this->_common->show ( array ('main' => 'base/question_edit.tpl','data'=>$usertag,'msg'=>$msg) );
		
	}
        function actionDelQuestion(){//删除
		$id=$this->_common->filter($_GET['id']);
		$this->_question->removeByPkv($id);
		redirect($_SERVER['HTTP_REFERER']);
	}
        function actionRecommendQuestion(){//推荐
		$id=$this->_common->filter($_GET['id']);
                $data=array('id'=>$id,'recommend'=>2);
		$this->_question->update($data);
		redirect($_SERVER['HTTP_REFERER']);
	}
        function actionUnRecommendQuestion(){//不推荐
		$id=$this->_common->filter($_GET['id']);
                $data=array('id'=>$id,'recommend'=>1);
		$this->_question->update($data);
		redirect($_SERVER['HTTP_REFERER']);
	}
        
	
}

?>