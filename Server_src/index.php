<?php
session_start();
error_reporting(0);
require_once('errcode.php');
require_once('inc_text.php');
if (!isset($_REQUEST['act'])) $act='about'; else $act=$_REQUEST['act'];
if (isset($_REQUEST['rid'])) $_SESSION['rid']=$_REQUEST['rid'];

switch (strtolower($act))
{
	case 'help': //获取帮助
		/* 参数
		topic 主题
		*/
		$arr['help']=file_get_contents('help.txt');
		break;
	
	case 'about': //关于
		$arr['about']=file_get_contents('about.txt');
		break;

	case 'login': //用户登录
		/* 参数
		type 类型 'username' 'rid' 'imei'
		groupid 所属群组
		limit 开始数，总数
		*/
		require_once('system.php');
		$arr=login();
		break;
	case 'logout': //用户注销
		/* 参数
		type 类型 'username' 'rid' 'imei'
		groupid 所属群组
		limit 开始数，总数
		*/
		require_once('system.php');
		$arr=logout();
		break;
	
	case 'checkversion': //检查版本
		/* 参数
		type 类型 'username' 'rid' 'imei'
		groupid 所属群组
		limit 开始数，总数
		*/
		if (!isset($_REQUEST['clienttype'])) $clienttype='server'; else $clienttype=$_REQUEST['clienttype'];
		if (!isset($_REQUEST['clientver'])) $clientver='0'; else $clientver=$_REQUEST['clientver'];
		require_once('checkversion.php');
		$arr=check_version($clienttype, $clientver);
		break;
	
	case 'getfriendlist': //获取好友列表
		/* 参数
		type 类型 
		groupid 所属群组
		limit 开始数，总数
		*/
		require_once('friend.php');
		$arr=get_friend_list();
		break;
	
	case 'getfriendinfo': //获取好友信息
		/* 参数
		friendid 好友id
		*/
		require_once('user.php');
		if (!isset($_REQUEST['userid'])) $userid='0'; else $userid=$_REQUEST['userid'];
		$arr=get_user_info($userid);
		break;
	
	case 'keeponline': //保持在线，也就是访问轩宝的主页
		require_once('user.php');
		$userid='1139776';
		$arr=get_user_info($userid);
		break;
	
	case 'getfriendgroup': //获取好友分组
		/* 参数
		limit 开始数，总数
		*/
		break;
	
	case 'addfriendgroup': //添加好友分类
		/* 参数
		groupname 分类名称
		*/
		break;
	
	case 'addfriend': //添加好友
		/* 参数
		userid 用户id
		*/
		break;
	
	case 'deletefriend': //删除好友
		/* 参数
		userid 用户id
		*/
		break;
	
	case 'getmessagelist': //获取收件箱列表
		/* 参数
		limit 开始数，总数
		*/
		require_once('message.php');
		if (!isset($_REQUEST['type'])) $type='inbox'; else $type=$_REQUEST['type'];
		if (!isset($_REQUEST['limit'])) $limit='10'; else $limit=$_REQUEST['limit'];
		$arr=get_message($type,$limit);
		break;
	
	case 'getmessagecontent': //获取信息内容
		require_once('message.php');
		if (!isset($_REQUEST['msgid'])) $msgid=0; else $msgid=$_REQUEST['msgid'];
		$arr=get_message_content($msgid);
		break;
	
	case 'sendmessage': //发送信息
		/* 参数
		limit 开始数，总数
		*/
		if (!isset($_REQUEST['to'])) $to='0'; else $to=$_REQUEST['to'];
		if (!isset($_REQUEST['content'])) $content='0'; else $content=$_REQUEST['content'];
		if (!isset($_REQUEST['emotion'])) $emotion='0'; else $emotion=$_REQUEST['emotion'];
		require_once('message.php');
		$arr=send_message($to,$content,$emotion);
		break;
	
	case 'getnewmessage': //接收新信息
		/* 参数
		limit 开始数，总数
		*/
		require_once('message.php');
		$arr=get_message('new');
		break;
	
	case 'gettopiclist': //获取主题信息
		require_once('topic.php');
		if (!isset($_REQUEST['ctid'])) $ctid='76'; else $ctid=$_REQUEST['ctid'];
		if (!isset($_REQUEST['limit'])) $limit='15'; else $limit=$_REQUEST['limit'];
		$arr=get_topic_list($ctid,$limit);
		break;
	
	case 'gettopicdetail': //获取主题内容
		require_once('topic.php');
		if (!isset($_REQUEST['topicid'])) $topicid='0'; else $topicid=$_REQUEST['topicid'];
		$arr=get_topic_detail($topicid);
		break;
	
	case 'gettopicreplylist': //获取主题回复列表
		require_once('topic.php');
		if (!isset($_REQUEST['topicid'])) $topicid='0'; else $topicid=$_REQUEST['topicid'];
		break;
	
	case 'gettopicreplydetail': //获取主题回复内容
		break;
	
	case 'publishtopic': //发表帖子
		if (!isset($_REQUEST['ctid']))    $ctid='0';        else $ctid=$_REQUEST['ctid'];
		if (!isset($_REQUEST['topic']))   $topic='';    else $topic=$_REQUEST['topic'];
		if (!isset($_REQUEST['content'])) $content='';  else $content=$_REQUEST['content'];
		if (!isset($_REQUEST['type']))    $type='';         else $type=$_REQUEST['type'];
		require_once('topic.php');
		$arr=publish_topic($ctid,$topic,$content,$type	);
		break;
	
	case 'publishreply': //回复帖子
		if (!isset($_REQUEST['topicid']))    $topicid='0';        else $topicid=$_REQUEST['topicid'];
		if (!isset($_REQUEST['content']))    $content='';         else $content=$_REQUEST['content'];
		if (!isset($_REQUEST['emotion']))    $emotion='';         else $emotion=$_REQUEST['emotion'];
		if (!isset($_REQUEST['type']))       $type='';         else $type=$_REQUEST['type'];
		require_once('topic.php');
		$arr=publish_reply($topicid,$content,$emotion);
		break;
	
	case 'getmyinfo': //获取我的信息
		break;
	
	case 'setting': //获取设置信息
		break;
		
	case 'feedback': //获取设置信息
		require_once('system.php');
		$arr=feedback();
		break;
	
	default:
	case 'test': //用户登录
		/* 参数
		type 类型 'username' 'rid' 'imei'
		groupid 所属群组
		limit 开始数，总数
		*/
		if (!isset($_REQUEST['type']))    $type='connection';        else $type=$_REQUEST['type'];
		require_once('system.php');
		$arr=test($type);
		break;

}

require('inc_template.php');		
$smarty= new cls_template();
$smarty->assign('arr',$arr);
$smarty->display(strtolower($act).'.tpl');


?>
