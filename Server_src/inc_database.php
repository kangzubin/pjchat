<?php
function db_connect()
{
	$host=SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT;
	$user=SAE_MYSQL_USER;
	$pass=SAE_MYSQL_PASS;
	$db=SAE_MYSQL_DB;
	mysql_connect($host,$user,$pass);
	mysql_select_db($db);
	mysql_query("set names 'utf8'");
}
function db_query($query)
{
	$res=mysql_query($query);
	return $res;
}
function db_close()
{
	mysql_close();
}
?>