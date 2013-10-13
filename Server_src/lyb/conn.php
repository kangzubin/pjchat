<?php
$host=SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT;
$user=SAE_MYSQL_USER;
$pass=SAE_MYSQL_PASS;
$db=SAE_MYSQL_DB;
mysql_connect($host,$user,$pass);
mysql_select_db($db);
mysql_query("set names 'GBK'");
?>
