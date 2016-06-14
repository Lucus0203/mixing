<?php
require_once APP_DIR . DS . 'apiLib' . DS . 'ext' . DS . 'Upload.php';
require_once APP_DIR . DS . 'apiLib' . DS . 'constant_beans.php';
$act=filter($_REQUEST['act']);
switch ($act){
	case 'addDiary':
		addDiary();//新增慢生活
		break;
	case 'getDiarys':
		getDiarys();//查看慢生活
		break;
	case 'delDiary':
		delDiary();//删除慢生活
		break;
	case 'delDiaryImg':
		delDiaryImg();//删除慢生活
		break;
        case 'leaveMsg':
                leaveMsg();//留言
                break;
        case 'delMsg':
                delMsg();//删除留言
                break;
        case 'newReplyCount':
                newReplyCount();//有新的回复
                break;
        case 'like':
                like();//点赞
                break;
        case 'deLike'://取消点赞
                deLike();
                break;
        case 'newMsg'://漫生活新消息列表
                newMsg();
                break;
        case 'delNewMsg'://删除新消息列表记录
                delNewMsg();
                break;
        case 'detail'://漫生活详细
                detail();
                break;
        case 'shopDiarys'://店铺里的漫生活
                shopDiarys();
                break;
	default:
		break;
}
//新增慢生活
function addDiary(){
        global $db;
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$note=filterIlegalWord(!empty($_REQUEST['note'])?$_REQUEST['note']:'');
	$voice=filter(!empty($_REQUEST['voice'])?$_REQUEST['voice']:'');
	$voice_time=filter(!empty($_REQUEST['voice_time'])?$_REQUEST['voice_time']:'');
	$address=filter(!empty($_REQUEST['address'])?$_REQUEST['address']:'');
	$lng=filter(!empty($_REQUEST['lng'])?$_REQUEST['lng']:'');
	$lat=filter(!empty($_REQUEST['lat'])?$_REQUEST['lat']:'');
	$shopid=filter(!empty($_REQUEST['shopid'])?$_REQUEST['shopid']:'');
        $data=array('user_id'=>$loginid,'note'=>$note,'voice'=>$voice,'voice_time'=>$voice_time,'address'=>$address,'lng'=>$lng,'lat'=>$lat,'created'=>date("Y-m-d H:i:s"));
        if(empty($loginid)){
            echo json_result(null,'2','请先登录');
            return ;
        }
        if(trim($note)==''){
            echo json_result(null,'3','请说点什么吧~');
            return ;
        }
        if(!empty($shopid)){
            $data['shop_id']=$shopid;
//            $shopinfo=$db->getRow('shop',array('id'=>$shopid),array('title','img'));
//            $data['shop_title']=$shopinfo['title'];
//            $data['shop_img']=$shopinfo['img'];
        }
        //声音
        $upload = new UpLoad();
        $folder = "upload/voice/";
        if (!file_exists($folder)) {
                mkdir($folder, 0777);
        }
        $upload->setDir($folder . date("Ymd") . "/");
        $upload->setPrefixName('diary' . $loginid);
        $file = $upload->upLoad('voice');
        if ($file['status'] != 0 && $file['status'] != 1){
            echo json_result(null,'3',$file['errMsg']);
            return ;
        }elseif ($file['status'] == 1) {
            $data['voice']=APP_SITE . $file['file_path'];
        }
        $diary_id=$db->create('diary',$data);
        //相册
        $upload = new UpLoad();
        $folder = "upload/diaryPhoto/";
        if (!file_exists($folder)) {
                mkdir($folder, 0777);
        }
        $upload->setDir($folder . date("Ymd") . "/");
        $upload->setPrefixName('diary' . $loginid);
        $file = $upload->uploadFiles('photos');
        if ($file['status'] == 1) {
                foreach ($file['filepaths'] as $path) {
                        $photo['diary_id'] = $diary_id;
                        $photo['user_id'] = $loginid;
                        list($width,$height,$type)=getimagesize($path);
                        $photo['img'] = APP_SITE . $path;
                        $photo['width'] = $width;
                        $photo['height'] = $height;
                        $photo['created'] = date("Y-m-d H:i:s");
                        $db->create('diary_img', $photo);
                }
        }
        //如果参加了胶片里的咖啡馆活动发送通知
        if(strpos($note,'胶片里的咖啡馆') && $file['status'] == 1){
            $db->create('notify',array('user_id'=>$loginid,'img'=>'http://www.xn--8su10a.com/img/office_mark_head.png','send_time'=>date("Y-m-d H:i:s"),'msg'=>'恭喜你成功参与"胶片里的咖啡馆活动"','url'=>'http://app.xn--8su10a.com/vote/participate.html?votenum='.$diary_id,'type'=>'mixing','isread'=>2));
        }
        //发布漫生活获取豆子
        $num=BEANS_NUM_DIARY_ADD;
        $sql="select id from ".DB_PREFIX."beans_log beans_log where user_id=$loginid and shop_id=$shopid and type=2 and created>='".date("Y-m-d")." 00:00:00' and created<='".date("Y-m-d")." 23:59:59' ";
        if($db->getCountBySql($sql)<3){//漫生活发布小于3次获取豆子
            $beanlog=array('user_id'=>$loginid,'diary_id'=>$diary_id,'content'=>'发布漫生活获取豆子'.$num.'颗','num'=>$num,'type'=>2);//途径类型1登录2发布漫生活3签到
            $db->create('beans_log',$beanlog);
            //增加用户豆子
            $updateBeansSql="update ".DB_PREFIX."user user set beans=beans+".$num." where id = ".$loginid;
            $db->excuteSql($updateBeansSql);
        }
        echo json_result(array('success'=>'TRUE'));
}

//查看慢生活
function getDiarys(){
        global $db;
	$userid=filter(!empty($_REQUEST['userid'])?$_REQUEST['userid']:'');
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$page_no = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
        if(empty($loginid)){
            echo json_result(null,'2','请先登录');
            return ;
        }
        if(!empty($userid)){
            $userallow=$db->getRow('user',array('id'=>$userid),array('allow_news'));//访问权限
            if($userid == $loginid){
                $type = 4;//自己
            }else{
                $relation = getRelationStatus($loginid, $userid);
                if($relation['relation_status']==4){
                    $type = 2;//好友
                }else{
                    $type = 3;//陌生人
                }
            }
        }else{
            $type=1;//查看关注的漫生活
        }
        if($type==3&&$userallow['allow_news']==2){//陌生人且不许查看
            echo json_result(null,'4','成为TA的好友可以看到内容哦');
            return ;
        }
        if($type==3&&$page_no>1){//陌生人且只能查看8条信息
            echo json_result(null,'5','成为TA的好友可以看到更多内容哦');
            return ;
        }
        
        $sql="select diary.id as diary_id,diary.user_id,user.head_photo,user.nick_name,diary.views,diary.beans,diary.note,diary.voice,diary.voice_time,diary.address,diary.lng,diary.lat,diary.shop_id,shop.title as shop_title,shop.img as shop_img,diary.created,if(diary_msg.msg <> '',1,2 ) as liked from ".DB_PREFIX."diary diary "
                . "left join ".DB_PREFIX."user user on diary.user_id=user.id "
                . "left join ".DB_PREFIX."shop shop on shop.id=diary.shop_id "
                . "left join (select diary_id,msg from ".DB_PREFIX."diary_msg diary_msg where type=2 and user_id =$loginid group by diary_id ) diary_msg on diary_msg.diary_id=diary.id "//是否点赞
                . "where diary.isdel=2 ";//isdel 1删除2正常 //liked 1 已赞 2 未赞
        //如果是自己的慢生活同时找出关注的咖啡馆漫生活
        if($type==1){
            $shopsql=" select s2.id from ".DB_PREFIX."shop_users su left join ".DB_PREFIX."shop s1 on su.shop_id=s1.id "
                    . "left join ".DB_PREFIX."shop s2 on s1.id=s2.id "//s1.addarea_id=s2.addarea_id
                    . "where su.user_id=$loginid ";
            $blacklist=" select relation_id from ".DB_PREFIX."user_relation relation where relation.user_id = $loginid and relation.status=2 ";//黑名单
            $firendlist=" select relation_id from ".DB_PREFIX."user_relation relation where relation.user_id = $loginid and relation.status=1 ";//关注的人
            //$sql.=" and diary.user_id = $loginid or (diary.shop_id in ($shopsql) and diary.user_id not in ($blacklist) and diary.shop_view_status != 2 ) "; //关注自己收藏的咖啡馆
            //查看范围 关注的人 或者 shop_id不为空且没屏蔽
            //$sql.=" and (diary.user_id = $loginid or (diary.note <>'加入了咖啡约我' and (diary.user_id in ($firendlist) or  (diary.shop_id <>'' and diary.shop_id is not null and diary.shop_view_status != 2) ) ) )";
            //查看所有慢生活除去屏蔽
            $sql.=" and (diary.user_id = $loginid or (diary.shop_view_status != 2 ) )";
        }else{
            $sql.=" and diary.user_id = $userid ";
        }
	$sql .= " order by created desc limit $start,$page_size";
        
	$diarys=$db->getAllBySql($sql);
        //更新浏览记录
        $updateViewsql="update ".DB_PREFIX."diary diary inner join ($sql) s on s.diary_id=diary.id set diary.views=diary.views+1 ";
        $db->excuteSql($updateViewsql);
        //获取相册留言等内容
        foreach ($diarys as $k=>$d){
            //相册
            $imgsql="select id as img_id,user_id,img,width,height from ".DB_PREFIX."diary_img as img where diary_id=".$d['diary_id']." order by id asc ";
            $imgs=$db->getAllBySql($imgsql);
            if(count($imgs)>0){
                $diarys[$k]['imgs']=$imgs;
            }
            //留言
            $msgsql="select msg.id as msg_id,msg.user_id as from_user_id,from_user.nick_name as from_nick_name,from_user.head_photo as from_head_photo,msg.to_user_id,to_user.nick_name as to_nick_name,to_user.head_photo as to_head_photo,msg.msg from ".DB_PREFIX."diary_msg msg left join " .DB_PREFIX. "user from_user on from_user.id=msg.user_id left join ".DB_PREFIX."user to_user on to_user.id=msg.to_user_id "
                    . "where msg.type = 1 and msg.diary_id = ".$d['diary_id'];
            $msgs=$db->getAllBySql($msgsql);
            if(count($msgs)>0){
                $diarys[$k]['msgs']=$msgs;
            }
        }
        //$db->update('diary_msg',array('isread'=>2),array('to_user_id'=>$loginid));
        echo json_result(array('diarys'=>$diarys));
    
}

//删除慢生活
function delDiary(){
        global $db;
	$diaryid=filter(!empty($_REQUEST['diaryid'])?$_REQUEST['diaryid']:'');
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
        if(empty($loginid)){
            echo json_result(null,'2','请先登录');
            return ;
        }
        if($db->getCount('diary',array('id'=>$diaryid,'user_id'=>$loginid))<=0){
            echo json_result(null,'3','操作失败,你不能删除此内容');
            return;
        }else{
            $imgs=$db->getAll('diary_img',array('diary_id'=>$diaryid),array('img'));
            foreach ($imgs as $m){
                $path=str_replace(APP_SITE, "", $m['img']);
                if(file_exists($path)){
                    unlink($path);//删除图片
                }
            }
            $db->delete('diary_msg',array('diary_id'=>$diaryid));
            $db->delete('diary_img',array('diary_id'=>$diaryid));
            $db->delete('diary',array('id'=>$diaryid));
            echo json_result(array('success'=>'TRUE'));
        }
        
}


//删除相册
function delDiaryImg(){
        global $db;
	$imgid=filter(!empty($_REQUEST['imgid'])?$_REQUEST['imgid']:'');
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
        if(empty($loginid)){
            echo json_result(null,'2','请先登录');
            return ;
        }
        $img=$db->getRow('diary_img',array('id'=>$imgid));
        if($img['user_id']!=$loginid){
            echo json_result(null,'2','不可删除别人的照片');
            return ;
        }
        $path=str_replace(APP_SITE, "", $img['img']);
        if(file_exists($path)){
            unlink($path);//删除图片
        }
        $db->delete('diary_img',array('id'=>$imgid,'user_id'=>$loginid));
        //返回慢生活信息
        if(empty($img['diary_id'])){
            echo json_result(null,'3','图片已删除');
            return ;
        }
        $diary_id=$img['diary_id'];
        $sql="select id as diary_id,note,voice,voice_time,address,lng,lat,created from ".DB_PREFIX."diary diary where id=$diary_id ";//isdel 1删除2正常
	$diary=$db->getRowBySql($sql);
        
        //获取相册留言等内容
        //相册
        $imgsql="select id as img_id,img,width,height from ".DB_PREFIX."diary_img as img where diary_id=".$diary_id;
        $imgs=$db->getAllBySql($imgsql);
        if(count($imgs)>0){
            $diary['imgs']=$imgs;
        }
        //留言
        $msgsql="select msg.id as msg_id,msg.user_id as from_user_id,from_user.nick_name as from_nick_name,from_user.head_photo as from_head_photo,msg.to_user_id,to_user.nick_name as to_nick_name,to_user.head_photo as to_head_photo,msg.msg from ".DB_PREFIX."diary_msg msg left join " .DB_PREFIX. "user from_user on from_user.id=msg.user_id left join ".DB_PREFIX."user to_user on to_user.id=msg.to_user_id "
                . "where msg.diary_id = ".$diary_id;
        $msgs=$db->getAllBySql($msgsql);
        if(count($msgs)>0){
            $diary['msgs']=$msgs;
        }
        echo json_result(array('diary'=>$diary));
}

//留言
function leaveMsg(){
        global $db;
	$diaryid=filter(!empty($_REQUEST['diaryid'])?$_REQUEST['diaryid']:'');
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$to_userid=filter(!empty($_REQUEST['to_userid'])?$_REQUEST['to_userid']:'');
	$msg=filterIlegalWord(!empty($_REQUEST['msg'])?$_REQUEST['msg']:'');
        if(empty($loginid)){
            echo json_result(null,'2','请先登录');
            return ;
        }
        if(empty($msg)){
            echo json_result(null,'3','请输入留言内容');
            return ;
        }
        $diary=$db->getRow('diary',array('id'=>$diaryid),array('user_id'));
        $data=array('diary_id'=>$diaryid,'user_id'=>$loginid,'msg'=>$msg);
        if(!empty($to_userid)){
            $data['to_user_id']=$to_userid;
        }
        //给自己留言是已读状态
        if($diary['user_id']==$loginid){
            $data['isread_user']=2;
        }
        $db->create('diary_msg',$data);
        $msgsql="select msg.id as msg_id,msg.user_id as from_user_id,from_user.nick_name as from_nick_name,from_user.head_photo as from_head_photo,msg.to_user_id,to_user.nick_name as to_nick_name,to_user.head_photo as to_head_photo,msg.msg from ".DB_PREFIX."diary_msg msg left join " .DB_PREFIX. "user from_user on from_user.id=msg.user_id left join ".DB_PREFIX."user to_user on to_user.id=msg.to_user_id "
                    . "where msg.type=1 and msg.diary_id = ".$diaryid;
        $msges=$db->getAllBySql($msgsql);
        echo json_result(array('msges'=>$msges));
        
}

//删除留言
function delMsg(){
        global $db;
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$msgid=filter(!empty($_REQUEST['msgid'])?$_REQUEST['msgid']:'');
        if(empty($loginid)){
            echo json_result(null,'2','请先登录');
            return ;
        }
        $msg=$db->getRow('diary_msg',array('id'=>$msgid));
        $diary_id=$msg['diary_id'];
        $db->delete('diary_msg',array('id'=>$msgid,'user_id'=>$loginid));
        $msgsql="select msg.id as msg_id,msg.user_id as from_user_id,from_user.nick_name as from_nick_name,from_user.head_photo as from_head_photo,msg.to_user_id,to_user.nick_name as to_nick_name,to_user.head_photo as to_head_photo,msg.msg from ".DB_PREFIX."diary_msg msg "
                . "left join " .DB_PREFIX. "user from_user on from_user.id=msg.user_id "
                . "left join ".DB_PREFIX."user to_user on to_user.id=msg.to_user_id "
                    . "where msg.type=1 and msg.diary_id = ".$diary_id;
        $msges=$db->getAllBySql($msgsql);
        echo json_result(array('msges'=>$msges));
        
        
}


//点赞送豆子
function like(){
        global $db;
	$diaryid=filter(!empty($_REQUEST['diaryid'])?$_REQUEST['diaryid']:'');
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
        if(empty($loginid)){
            echo json_result(null,'2','请先登录');
            return ;
        }
        $userBeans=$db->getRow('user',array('id'=>$loginid),array('beans'));
        if($userBeans['beans']<=0){
            echo json_result(null,'3','您的豆子不够了');
            return ;
        }
        $diary=$db->getRow('diary',array('id'=>$diaryid));
        if($db->getCount('diary_msg',array('user_id'=>$loginid,'diary_id'=>$diaryid,'type'=>2))==0){//没点赞过
            $data=array('diary_id'=>$diaryid,'user_id'=>$loginid,'to_user_id'=>$diary['user_id'],'msg'=>'+1','type'=>2);
            $db->create('diary_msg',$data);
            //增加漫生活豆子
            $updateBeansSql="update ".DB_PREFIX."diary diary set beans=beans+1 where id=$diaryid ";
            $db->excuteSql($updateBeansSql);
            
            //增加对方用户豆子
            $updateBeansSql="update ".DB_PREFIX."user user set beans=beans+1 where id = ".$diary['user_id'];
            $db->excuteSql($updateBeansSql);
            //减少自己的豆子
            $updateBeansSql="update ".DB_PREFIX."user user set beans=beans-1 where id = ".$loginid;
            $db->excuteSql($updateBeansSql);
        }
        echo json_result(array('success'=>'TRUE'));
}

//取消点赞
function deLike(){
        global $db;
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$diaryid=filter(!empty($_REQUEST['diaryid'])?$_REQUEST['diaryid']:'');
        if(empty($loginid)){
            echo json_result(null,'2','请先登录');
            return ;
        }
        if($db->getCount('diary_msg',array('user_id'=>$loginid,'diary_id'=>$diaryid,'type'=>2))>0){//点赞过
            $diary=$db->getRow('diary',array('id'=>$diaryid));
            $db->delete('diary_msg',array('user_id'=>$loginid,'diary_id'=>$diaryid,'type'=>2));
            //减少漫生活豆子
            $updateBeansSql="update ".DB_PREFIX."diary diary set beans=beans-1 where id=$diaryid ";
            $db->excuteSql($updateBeansSql);
            //减少对方豆子
            $updateBeansSql="update ".DB_PREFIX."user user set beans=beans-1 where id={$diary['user_id']} ";
            $db->excuteSql($updateBeansSql);
            //增加自己豆子
            $updateBeansSql="update ".DB_PREFIX."user user set beans=beans+1 where id = ".$loginid;
            $db->excuteSql($updateBeansSql);
        }
        echo json_result(array('success'=>'TRUE'));
}

//慢生活新消息
function newReplyCount(){
        global $db;
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
        $countSql="select diary_msg.id from ".DB_PREFIX."diary_msg diary_msg "
                . "left join ".DB_PREFIX."diary diary on diary_msg.diary_id=diary.id "
                . "where ((diary_msg.to_user_id = $loginid and diary_msg.isread_touser=1) or (diary.user_id = $loginid and diary_msg.isread_user=1)) and diary_msg.user_id <> $loginid  ";
        $count=$db->getCountBySql($countSql);
        $lastUserSql="select head_photo from ".DB_PREFIX."diary_msg diary_msg left join ".DB_PREFIX."user user on diary_msg.user_id=user.id "
                . "left join ".DB_PREFIX."diary diary on diary_msg.diary_id=diary.id "
                . "where ((diary_msg.to_user_id = $loginid and diary_msg.isread_touser=1) or (diary.user_id = $loginid and diary_msg.isread_user=1)) and diary_msg.user_id <> $loginid order by diary_msg.id desc limit 0,1 ";
        
        $uesr=$db->getRowBySql($lastUserSql);
        echo json_result(array('count'=>$count,'head_photo'=>$uesr['head_photo']));
}

//新消息列表
function newMsg(){
        global $db;
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$page_no = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
        
        $sql="select diary_msg.id as msgid,user.id as user_id,user.head_photo,user.nick_name,diary_msg.type,diary_msg.msg,diary_msg.created,diary.id as diary_id,diary.note,diary_img.img,diary.voice_time from ".DB_PREFIX."diary_msg diary_msg "
                . "left join ".DB_PREFIX."diary diary on diary_msg.diary_id=diary.id "
                . "left join ".DB_PREFIX."user user on diary_msg.user_id = user.id "
                . "left join (select diary_id,img from ".DB_PREFIX."diary_img diary_img group by diary_id) diary_img on diary_img.diary_id=diary.id "
                . "where (diary_msg.to_user_id = $loginid and diary_msg.isread_touser=1 and isdel_list_touser=1) or (diary.user_id = $loginid and diary_msg.isread_user=1 and isdel_list_user=1) ";
	$sql .= "order by diary_msg.isread_touser asc,diary_msg.isread_user asc,diary_msg.created desc limit $start,$page_size";
        $data = $db->getAllBySql($sql);
        //更新未读状态
        $db->update('diary_msg',array('isread_touser'=>2),array('to_user_id'=>$loginid));
        
        $updateSql="update ".DB_PREFIX."diary_msg set isread_user=2 where id in (select m.id from (select msg.id from ".DB_PREFIX."diary_msg msg left join ".DB_PREFIX."diary diary on msg.diary_id=diary.id where diary.user_id = $loginid and msg.isread_user=1) m )";
        $db->excuteSql($updateSql);
        echo json_result(array('news'=>$data));
        
}

//删除消息列表
function delNewMsg(){
        global $db;
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$msgid=filter(!empty($_REQUEST['msgid'])?$_REQUEST['msgid']:'');
        $msg=$db->getRow('diary_msg',array('id'=>$msgid));
        if($loginid==$msg['to_user_id']){
            $db->update('diary_msg',array('isdel_list_touser'=>2),array('id'=>$msgid,'to_user_id'=>$loginid));
        }
        $diary=$db->getRow('diary',array('id'=>$msg['diary_id']),array('user_id'));
        if($diary['user_id']==$loginid){
            $db->update('diary_msg',array('isdel_list_user'=>2),array('id'=>$msgid));
        }
        echo json_result(array('success'=>'TRUE'));
}

//漫生活详情
function detail(){
	global $db;
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$diaryid=filter(!empty($_REQUEST['diaryid'])?$_REQUEST['diaryid']:'');
        if(empty($loginid)){
            echo json_result(null,'2','请先登录');
            return ;
        }
        $sql="select diary.id as diary_id,diary.user_id,user.head_photo,user.nick_name,diary.views,diary.beans,diary.note,diary.voice,diary.voice_time,diary.address,diary.lng,diary.lat,diary.shop_id,shop.title as shop_title,shop.img as shop_img,diary.created,if(diary_msg.msg <> '',1,2 ) as liked from ".DB_PREFIX."diary diary "
                . "left join ".DB_PREFIX."user user on diary.user_id=user.id "
                . "left join ".DB_PREFIX."shop shop on diary.shop_id=shop.id "
                . "left join (select diary_id,msg from ".DB_PREFIX."diary_msg diary_msg where type=2 and user_id =$loginid group by diary_id ) diary_msg on diary_msg.diary_id=diary.id "
                . "where diary.id = $diaryid ";//isdel 1删除2正常 //liked 1 已赞 2 未赞
	$diary=$db->getRowBySql($sql);
        
        //相册
        $imgsql="select id as img_id,img,width,height from ".DB_PREFIX."diary_img as img where diary_id=".$diaryid." order by id asc ";
        $imgs=$db->getAllBySql($imgsql);
        if(count($imgs)>0){
            $diary['imgs']=$imgs;
        }
        //留言
        $msgsql="select msg.id as msg_id,msg.user_id as from_user_id,from_user.nick_name as from_nick_name,from_user.head_photo as from_head_photo,msg.to_user_id,to_user.nick_name as to_nick_name,to_user.head_photo as to_head_photo,msg.msg from ".DB_PREFIX."diary_msg msg left join " .DB_PREFIX. "user from_user on from_user.id=msg.user_id left join ".DB_PREFIX."user to_user on to_user.id=msg.to_user_id "
                . "where msg.type = 1 and msg.diary_id = ".$diaryid;
        $msgs=$db->getAllBySql($msgsql);
        if(count($msgs)>0){
            $diary['msgs']=$msgs;
        }
        echo json_result(array('diary'=>$diary));
}

//店铺漫生活列表
function shopDiarys(){
        global $db;
	$shopid=filter(!empty($_REQUEST['shopid'])?$_REQUEST['shopid']:'');
	$loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
	$page_no = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : 1;
	$page_size = PAGE_SIZE;
	$start = ($page_no - 1) * $page_size;
        
        if(empty($loginid)){
            echo json_result(null,'2','请先登录');
            return ;
        }
        $sql="select diary.id as diary_id,diary.user_id,user.head_photo,user.nick_name,diary.views,diary.beans,diary.note,diary.voice,diary.voice_time,diary.address,diary.lng,diary.lat,diary.shop_id,shop.title as shop_title,shop.img as shop_img,diary.created,if(diary_msg.msg <> '',1,2 ) as liked from ".DB_PREFIX."diary diary "
                . "left join ".DB_PREFIX."user user on diary.user_id=user.id "
                . "left join ".DB_PREFIX."shop shop on shop.id=diary.shop_id "
                . "left join (select diary_id,msg from ".DB_PREFIX."diary_msg diary_msg where type=2 and user_id =$loginid group by diary_id ) diary_msg on diary_msg.diary_id=diary.id "
                . "where diary.isdel=2 and diary.shop_id = $shopid and (diary.shop_view_status != 2 or diary.user_id=$loginid)";//isdel 1删除2正常
	$sql .= " order by created desc limit $start,$page_size";
	$diarys=$db->getAllBySql($sql);
        //更新浏览记录
        $updateViewsql="update ".DB_PREFIX."diary diary inner join ($sql) s on s.diary_id=diary.id set diary.views=diary.views+1 ";
        $db->excuteSql($updateViewsql);
        //获取相册留言等内容
        foreach ($diarys as $k=>$d){
            //相册
            $imgsql="select id as img_id,img,width,height from ".DB_PREFIX."diary_img as img where diary_id=".$d['diary_id'];
            $imgs=$db->getAllBySql($imgsql);
            if(count($imgs)>0){
                $diarys[$k]['imgs']=$imgs;
            }
            //留言
            $msgsql="select msg.id as msg_id,msg.user_id as from_user_id,from_user.nick_name as from_nick_name,from_user.head_photo as from_head_photo,msg.to_user_id,to_user.nick_name as to_nick_name,to_user.head_photo as to_head_photo,msg.msg from ".DB_PREFIX."diary_msg msg left join " .DB_PREFIX. "user from_user on from_user.id=msg.user_id left join ".DB_PREFIX."user to_user on to_user.id=msg.to_user_id "
                    . "where msg.type = 1 and msg.diary_id = ".$d['diary_id'];
            $msgs=$db->getAllBySql($msgsql);
            if(count($msgs)>0){
                $diarys[$k]['msgs']=$msgs;
            }
        }
        $db->update('diary_msg',array('isread'=>2),array('to_user_id'=>$loginid));
        $shop=$db->getRow('shop',array('id'=>$shopid),array('img','title'));
        echo json_result(array('shop'=>$shop,'diarys'=>$diarys));
    
}


function getRelationStatus($myself_id,$user_id){
	global $db;
	$info=array();
	//我关注的
	$myfav_count=$db->getCount('user_relation',array('user_id'=>$myself_id,'relation_id'=>$user_id));
	//关注我的
	$myfun_count=$db->getCount('user_relation',array('user_id'=>$user_id,'relation_id'=>$myself_id));
	if($myfav_count>0&&$myfun_count>0){
		$info['relation']='好友';
		$info['relation_status']=4;
	}elseif ($myfav_count>0){
		$info['relation']='关注中';//我关注的人
		$info['relation_status']=2;
	}elseif ($myfun_count>0){
		$info['relation']='被关注';//关注我的人
		$info['relation_status']=3;
	}
	if ($myfun_count>0){
		$re=$db->getRow('user_relation',array('user_id'=>$user_id,'relation_id'=>$myself_id));
		if($re['status']==2){
			$info['relation']='陌生人';//对方黑名单中
			$info['relation_status']=6;
		}
	}
	if ($myfav_count>0){
		$re=$db->getRow('user_relation',array('user_id'=>$myself_id,'relation_id'=>$user_id));
		if($re['status']==2){
			$info['relation']='黑名单';//黑名单
			$info['relation_status']=5;
		}
	}
	if($myfav_count<=0&&$myfun_count<=0){
		$info['relation']='陌生人';//陌生人
		$info['relation_status']=1;
	}
	return $info;
}