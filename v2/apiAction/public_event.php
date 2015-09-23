<?php
$act=filter($_REQUEST['act']);
switch ($act){
	case 'getEvents':
		getEvents();//首页获取官方活动
		break;
	case 'eventInfo':
		eventInfo();
		break;
	case 'collectEvents'://用户收藏的活动
		collectEvents();
		break;
	case 'collectEvent'://收藏
		collectEvent();
		break;
	case 'removeCollectEvent'://取消收藏
		removeCollectEvent();
		break;
	case 'isCollect'://查看是否收藏
		isCollect();
		break;
	case 'joinEvent'://求伴
		joinEvent();
		break;
	case 'togetherEvent'://对求伴者发起搭伴邀请
		togetherEvent();
		break;
	case 'eventFeedback':
		eventFeedback();//活动反馈纠错
		break;
        case 'collectUsers':
                collectUsers();
                break;
        case 'share':
                share();
                break;
	default:
		break;
}

//首页获取官方活动
function getEvents(){
	global $db;
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$page_no = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
	
	//$pucount="select count(id) as num,public_event_id from ".DB_PREFIX."public_users pu group by pu.public_event_id ";
	if(!empty($loginid)){//已登录者
		$sql="select pe.id,pe.title,pe.img,pe.address,pe.datetime,if(pu.user_id is not null,1,2) as iscollect from ".DB_PREFIX."public_event pe 
				left join ".DB_PREFIX."public_users pu on pe.id=pu.public_event_id and pu.user_id=$loginid 
				where pe.isdelete = 0 and pe.ispublic=1 and (pe.end_date > '".date('Y-m-d H:i:s')."' or pe.end_date = '' or pe.end_date is null ) 
			 	order by pu.user_id,pe.num asc";
	}else{//未登录
		$sql="select pe.id,pe.title,pe.img,pe.address,pe.datetime from ".DB_PREFIX."public_event pe 
				where pe.isdelete = 0 and pe.ispublic=1 and (pe.end_date > '".date('Y-m-d H:i:s')."' or pe.end_date = '' or pe.end_date is null ) 
				order by pe.num asc";
	}
	$sql.=(!empty($lng)&&!empty($lat))?",sqrt(power(lng-{$lng},2)+power(lat-{$lat},2))":'';
	$sql .= ",pe.created,pe.id desc limit $start,$page_size";
	$list=$db->getAllBySql($sql);
	foreach ($list as $k=>$v){
		//$list[$k]['datetime']=strtotime($v['datetime']);
		$created = date("m.d",strtotime($v['created']));
		if(empty($v['end_date'])){
			$list[$k]['created'] = date("Y.m.d",strtotime($v['created']));
		}else{
			$end_date = date("m.d",strtotime($v['end_date']));
			$list[$k]['created'] = $created.'~'.$end_date;
		}
		$list[$k]['distance']=(!empty($v['lat'])&&!empty($v['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$v['lat'],$v['lng']):lang_UNlOCATE;
	}
	echo json_result($list);
}
//用户收藏的活动
function collectEvents(){
        global $db;
	$loginid=filter($_REQUEST['loginid']);
	if(empty($loginid)){
		echo json_result(null,'21','用户未登录');
		return;
	}
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$page_no = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;

	$sql="select pe.id as event_id,pe.img,pe.title,pe.datetime,pe.lng,pe.lat from ".DB_PREFIX."public_event pe left join ".DB_PREFIX."public_users pu on pe.id=pu.public_event_id where pu.user_id=".$loginid;
	$sql.=(!empty($lng)&&!empty($lat))?" order by sqrt(power(lng-{$lng},2)+power(lat-{$lat},2))":'';

	$sql .= " limit $start,$page_size";
	$pes=$db->getAllBySql($sql);
	foreach ($pes as $k=>$v){
		$pes[$k]['distance']=(!empty($v['lat'])&&!empty($v['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$v['lat'],$v['lng']):lang_UNlOCATE;
	}
	//echo json_result(array('shops'=>$shops));
	echo json_result(array('events'=>$pes));
}

//收藏活动
function collectEvent(){
	global $db;
	$eventid=filter($_REQUEST['eventid']);
	$loginid=filter($_REQUEST['loginid']);
	if(!empty($eventid)&&!empty($loginid)){
		if($db->getCount('public_users',array('user_id'=>$loginid,'public_event_id'=>$eventid))==0){
			$up=array('user_id'=>$loginid,'public_event_id'=>$eventid,'created'=>date("Y-m-d H:i:s"));
			$db->create('public_users', $up);
		}
		//活动用户及头像地址
// 		$sql="select u.id as user_id,u.nick_name,u.user_name,up.path from ".DB_PREFIX."public_users pu left join ".DB_PREFIX."user u on pu.user_id = u.id left join ".DB_PREFIX."user_photo up on u.head_photo_id = up.id where pu.public_event_id=".$eventid;
// 		$pub_event['user_count']=$db->getCountBySql($sql);//参与人数
// 		$pub_event['users_photo']=$db->getAllBySql($sql);
		//echo json_result($pub_event);
		echo json_result('success');
		//echo json_result(array('eventid'=>$eventid));
	}else{
		echo json_result(null,'20','用户未登录或者该活动已删除');
	}

}

//取消收藏
function removeCollectEvent(){
	global $db;
	$eventid=filter($_REQUEST['eventid']);
	$loginid=filter($_REQUEST['loginid']);
	if(!empty($eventid)&&!empty($loginid)){
		$up=array('user_id'=>$loginid,'public_event_id'=>$eventid);
		$db->delete('public_users', $up);
		echo json_result('success');
	}else{
		echo json_result(null,'20','用户未登录或者该活动已删除');
	}
}

//是否收藏
function isCollect(){
	global $db;
	$eventid=filter($_REQUEST['eventid']);
	$loginid=filter($_REQUEST['loginid']);
	if(!empty($eventid)&&!empty($loginid)){
		if($db->getCount('public_users',array('user_id'=>$loginid,'public_event_id'=>$eventid))>0){
			echo json_result('1');//已收藏
		}else{
			echo json_result('2');//未收藏
		}
	}else{
		echo json_result(null,'20','用户未登录或者该活动已删除');
	}
}

//官方活动详情
function eventInfo(){
	global $db;
	$id=filter($_REQUEST['eventid']);
	$lng=filter($_REQUEST['lng']);
	$lat=filter($_REQUEST['lat']);
	$loginid=filter($_REQUEST['loginid']);
	$pub_event=$db->getRow("public_event",array('id'=>$id),array('title','content','price','datetime','address','lng','lat'));//获取活动信息
	$pub_event['distance']=(!empty($pub_event['lat'])&&!empty($pub_event['lng'])&&!empty($lng)&&!empty($lat))?getDistance($lat,$lng,$pub_event['lat'],$pub_event['lng']):lang_UNlOCATE;
	//活动海报
	$pub_event['photos']=$db->getAll("public_photo",array('public_event_id'=>$id));//活动海报
	//获取搭伴用户
	$groupusersql="select u.id as user_id,u.nick_name,u.user_name,u.head_photo from ".DB_PREFIX."public_event_together pet left join ".DB_PREFIX."user u on pet.user_id = u.id where pet.other_id is null order by pet.id asc ";
	$pub_event['groupusers']=$db->getAllBySql($groupusersql);
	//活动用户及头像地址
	$sql="select u.id as user_id,u.nick_name,u.user_name,u.head_photo as path from ".DB_PREFIX."public_users pu left join ".DB_PREFIX."user u on pu.user_id = u.id where pu.public_event_id=".$id;
	$pub_event['user_count']=$db->getCountBySql($sql);//关注人数
	$pub_event['users_photo']=$db->getAllBySql($sql);
	//是否收藏
	$pub_event['iscollect']=2;
	if(!empty($id)&&!empty($loginid)){
		if($db->getCount('public_users',array('user_id'=>$loginid,'public_event_id'=>$id))>0){
			$pub_event['iscollect']=1;//已收藏
		}
	}
	echo json_result($pub_event);
}

//报名求伴
function joinEvent(){
	global $db;
	$id=filter($_REQUEST['eventid']);
	$loginid=filter($_REQUEST['loginid']);
	$db->create('public_event_together', array('public_event_id'=>$id,'user_id'=>$loginid));
	echo json_result('success');
}

//对求伴者发起搭伴邀请
function togetherEvent(){
	global $db;
	$eventid=filter($_REQUEST['eventid']);
	$userid=filter($_REQUEST['userid']);
	$loginid=filter($_REQUEST['loginid']);
	$datetime=filter($_REQUEST['datetime']);
	$address=filter($_REQUEST['address']);
	$note=filter($_REQUEST['note']);
	$pay_type=filter($_REQUEST['pay_type']);//1我买单2请我吧3AA制
        $event=$db->getRow('public_event',array('id'=>$eventid));
        $lng=$event['lng'];
        $lat=$event['lat'];
	if(empty($loginid)){
		echo json_result(null,'2','请重新登录');
		return;
	}
	if(empty($userid)){
		echo json_result(null,'3','请选择你要搭伴的人');
		return;
	}
	if(empty($datetime)){
		echo json_result(null,'4','请选择时间');
		return;
	}
	if(empty($note)){
		echo json_result(null,'5','请填写寄语');
		return;
	}
	if(empty($pay_type)){
		echo json_result(null,'6','请选择资费类型');
		return;
	}
        if($address!=$event['address']){
            $reslg=getLngFromBaidu($address);
            $lng=$reslg['lng'];
            $lat=$reslg['lat'];
        }
	$db->create('public_event_together_others', array('public_event_id'=>$eventid,'title'=>$event['title'],'user_id'=>$userid,'other_id'=>$loginid,'datetime'=>$datetime,'address'=>$address,'lng'=>$lng,'lat'=>$lat,'note'=>$note,'pay_type'=>$pay_type,'status'=>1,'isreaded_other'=>1));
	echo json_result('success');
}

//活动反馈纠错
function eventFeedback(){
	global $db;
	$eventid=filter($_REQUEST['eventid']);
	$loginid=filter($_REQUEST['loginid']);
	$content=filterIlegalWord($_REQUEST['content']);
	$feedback=array('public_event_id'=>$eventid,'content'=>$content,'type'=>'event','created'=>date("Y-m-d H:i:s"));
	if(!empty($loginid)){
		$feedback['user_id']=$loginid;
	}
	$db->create('feedback', $feedback);
	echo json_result('success');
}

//关注/收藏的人
function collectUsers(){
        global $db;
	$eventid=filter($_REQUEST['eventid']);
	$loginid=filter($_REQUEST['loginid']);
        $page_no = isset($_REQUEST ['page']) ? $_REQUEST ['page'] : 1;
        $page_size = PAGE_SIZE;
        $start = ($page_no - 1) * $page_size;
        if(!empty($loginid)){
                $sql="select user.id as user_id,user.nick_name,user.head_photo,user.sex,user.birthday,user.constellation,if(ur.id is null,1,2) as isfollowed from ".DB_PREFIX."public_users pu "
                        . "left join ".DB_PREFIX."user user on pu.user_id=user.id "
                        . "left join ".DB_PREFIX."user_relation ur on ur.user_id=$loginid and ur.relation_id=user.id "
                        . "where pu.public_event_id = ".$eventid." and pu.user_id != $loginid ";
        }else{
                 $sql="select user.id as user_id,user.nick_name,user.head_photo,user.sex,user.birthday,1 as status from ".DB_PREFIX."public_users pu "
                        . "left join ".DB_PREFIX."user user on pu.user_id=user.id "
                        . "where pu.public_event_id = ".$eventid;
        }
        $sql .= " limit $start,$page_size";
        $data=$db->getAllBySql($sql);
        foreach ($data as $k => $v) {
                $data[$k]['age']='';
                if(!empty($v['birthday'])){
                        $data[$k]['age'] = floor((time()-strtotime($v['birthday'])) / 60 / 60 / 24 / 365);
                }
        }
        echo json_result(array('users'=>$data));
        
}

//分享活动
function share(){
	global $db;
	$eventid=filter($_REQUEST['eventid']);
        if(empty($eventid)){
            echo json_result(null,'2','请选择你要分享的活动');
            return ;
        }
        $event=$db->getRow('public_event',array('id'=>$eventid));
        $url=WEB_SITE.'eventDetail.html?p='.  base64_encode($eventid);
        $title=$event['title'];
        $img=$event['img'];
        $share=array('url'=>$url,'title'=>$title,'img'=>$img);
        echo json_result(array('share'=>$share));
}
