<?php
require_once APP_DIR . DS . 'apiLib' . DS . 'ext' . DS . 'Upload.php';
require_once APP_DIR . DS . 'apiLib' . DS . 'ext' . DS . 'Huanxin.php';
$act=filter($_REQUEST['act']);
switch ($act){
        case 'createGroup':
                createGroup();
                break;
	case 'getGroups':
		getGroups();//获取所有群组
		break;
	case 'getGroupInfo':
		getGroupInfo();//获取群组详情
		break;
	case 'updateGroupInfo':
		updateGroupInfo();//更新群组详情
		break;
        case 'dissolveGroup':
		dissolveGroup();//解散群组
		break;
	default:
		break;
}

//创建群组
function createGroup(){
        $loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
        $groupname=filter(!empty($_REQUEST['groupname'])?$_REQUEST['groupname']:'');
        $note=filter(!empty($_REQUEST['note'])?$_REQUEST['note']:'');
        $maxusers=filter(!empty($_REQUEST['maxusers'])?$_REQUEST['maxusers']:200);
        $user=$db->getRow('user',array('id'=>$loginid));
        $HuanxinObj=Huanxin::getInstance();
        $huserObj=$HuanxinObj->createGroup($groupname,$note,$maxusers,$user['user_name']);
        $hxgroupid=$huserObj->data->groupid;
        if(!empty($hxgroupid)){
                $group=array('hx_group_id'=>$hxgroupid,'user_id'=>$user['id'],'name'=>$groupname,'note'=>$note);
                $db->create('chatgroup',$group);
                echo json_result(array('group'=>$group));
                return;
        }
        echo json_result(null,'2','群组创建失败');
        return;
}


//获取所有群组
function getGroups(){
        global $db;
	$hxgroupid=filter(!empty($_REQUEST['hxgroupid'])?$_REQUEST['hxgroupid']:'');
        $sql="select hx_group_id,img,name from ".DB_PREFIX."chatgroup where hx_group_id in ($hxgroupid) ";
        $groups=$db->getAllBySql($sql);
        echo json_result(array('groups'=>$groups));
}


//获取群组详情
function getGroupInfo(){
        global $db;
	$hxgroupid=filter(!empty($_REQUEST['hxgroupid'])?$_REQUEST['hxgroupid']:'');
	$usernames=filter(!empty($_REQUEST['usernames'])?$_REQUEST['usernames']:'');
        $group=$db->getRow('group',array('hx_group_id'=>$hxgroupid),array('hx_group_id','img','name','note'));
	$usersql="select id as userid,user_name,nick_name,head_photo from ".DB_PREFIX."user where 1=1 ";
        if(!empty($usernames)){
            $usernames =  explode(",", $usernames);
            foreach ($usernames as $u){
                $usersql.=" and user_name = '{$u}'";
            }
            $group['users']=$db->getAllBySql($usersql);
        }
        echo json_result(array('group'=>$group));
}
//更新群组详情
function updateGroupInfo(){
        global $db;
        $loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
        $hxgroupid=filter(!empty($_REQUEST['hxgroupid'])?$_REQUEST['hxgroupid']:'');
        $groupname=filter(!empty($_REQUEST['groupname'])?$_REQUEST['groupname']:'');
        $note=filter(!empty($_REQUEST['note'])?$_REQUEST['note']:'');
        $group=$db->getRow('chatgroup',array('hx_group_id'=>$hxgroupid,'user_id'=>$loginid));
        if(empty($group)){
            echo json_result(null,'2','讨论组更新资料错误');
            return;
        }
        $data=array();
        if(!empty($groupname)){
            $data['name']=$groupname;
        }
        if(!empty($note)){
            $data['note']=$note;
        }
	//上传群头像
	$upload=new UpLoad();
	$folder="upload/chatGroup/";
	if (! file_exists ( $folder )) {
		mkdir ( $folder, 0777 );
	}
	$upload->setDir($folder.date("Ymd")."/");
	$upload->setPrefixName('user'.$loginid);
	$file=$upload->upLoad('photo');//$_File['photo'.$i]
	if($file['status']!=0&&$file['status']!=1){
		echo json_result(null,'37',$file['errMsg']);
		return;
	}
	if($file['status']==1){
                if(!empty($group['img'])){
                    $path=str_replace(APP_SITE, "", $group['img']);
                    unlink($path);
                }
                $data['img']=APP_SITE.$file['file_path'];
	}
	$db->update('chatGroup',$data,array('id'=>$group['id']));
        $group=$db->getRow('chatgroup',array('hx_group_id'=>$hxgroupid),array('hx_group_id','img','name','note'));
        
        echo json_result(array('group'=>$group));
}

//解散群组
function dissolveGroup(){
        global $db;
        $loginid=filter(!empty($_REQUEST['loginid'])?$_REQUEST['loginid']:'');
        $hxgroupid=filter(!empty($_REQUEST['hxgroupid'])?$_REQUEST['hxgroupid']:'');
        if($db->getCount('chatgroup',array('hx_group_id'=>$hxgroupid,'user_id'=>$loginid))<=0){
            echo json_result(null,'2','讨论组解散失败');
            return;
        }
        $HuanxinObj=Huanxin::getInstance();
        $huserObj=$HuanxinObj->delGroup($hxgroupid);
        $success=$huserObj->data->success;
        if($success===true){
            $db->del('chatgroup',array('hx_group_id'=>$hxgroupid));
            echo json_result(array('success'=>'TRUE'));
            return;
        }
        echo json_result(array('success'=>'FAIL'));
        
}