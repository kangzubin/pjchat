<?php
require_once ('inc_text.php');

function test($type='connection')
{
	switch ($type)
	{
		default:
		case 'connectiton':
			$arr['test']="OK!";
		break;
		case 'time':
			$timeformat=$_REQUEST['timeformat'];
			if (!isset($timeformat)) $timeformat='Y-m-d H:i:s';
			$arr['test']=date($timeformat,time());
		break;
	}
	return $arr;
}

function feedback()
{
	require_once ('inc_database.php');
	db_connect();
	if (!isset($_REQUEST['nickname']))   $nickname='';        else $nickname=$_REQUEST['nickname'];
	if (!isset($_REQUEST['content']))    $content='';         else $content=$_REQUEST['content'];
	$query="INSERT INTO feedback (`id` ,`nickname` ,`content` ,`time` )VALUES (NULL , '$nickname' ,'$content' , NOW( ) );";
	//print $query;
	db_query($query);
	db_close();
	$arr['feedback']="OK!";
	return $arr;
}

function logout()
{
	//unset($_SESSION);
	//unset($_SESSION['rid']);
	//session_unset();
	session_destroy();
	$arr['ok']='OK!';
	return $arr;
}
function login()
{
	if (!isset($_REQUEST['type'])) $act=''; else $type=$_REQUEST['type'];
	switch ($type)
	{
		case 'username':
			$username=$_REQUEST['username'];
			$password=$_REQUEST['password'];
			$url="http://ct.paojiao.cn/login.do?pr=ct";
			break;
		
		case 'rid':
			$rid  = $_REQUEST['rid'];
			$url  = "http://ct.paojiao.cn/user.do?method=userinfo&rid=$rid";
			$html = file_get_contents($url);
			$pos1 = strpos($html, '输入手机号或者ID登录'); 
			$pos2 = strpos($html, '请填写密码'); 
			$pos3 = strpos($html, '入塘(登录)'); 
			if ($pos1 && $pos2 && $pos3)
			{
				require_once('errcode.php');
				$arr['err']='E:'.E_login_wrong_rid;
			}
			else
			{
				$_SESSION['rid']=$_REQUEST['rid'];
				require_once ('user.php');
				require_once ('inc_database.php');
				db_connect();
				$arr=get_user_info(0);
				if (!isset($_REQUEST['agent'])) $agent='unknown'; else $agent=$_REQUEST['agent'];
				$query="INSERT INTO login_log (`id` ,`site`, `uid` ,`name` ,`time` ,`agent`)VALUES (NULL , 'paojiao', '".$arr['id']."', '".str_replace('\'','"',$arr['nickname'])."', NOW( ) ,'$agent');";
				//print $query;
				db_query($query);
				db_close();
			}
	}
	return $arr;
}
function http_post_content($url)
{ 
	$url_arr=parse_url($url); 
	$page="$url_arr[scheme]://$url_arr[host]$url_arr[path]"; 
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $page); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $url_arr['query']); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	$result = curl_exec($ch); 
	curl_close($ch); 
	return $result; 
} 
function check_login($html)
{
	$pos1 = strpos($html, '输入手机号或者ID登录'); 
	$pos2 = strpos($html, '请填写密码'); 
	$pos3 = strpos($html, '入塘(登录)'); 
	if ($pos1 && $pos2 && $pos3)
		return false;
	else 
		return true;
}