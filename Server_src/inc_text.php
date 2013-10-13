<?php
function pjencode($str)
{
	$str = str_replace('\n','',$str);
	$str = str_replace('\r','',$str);
	$str = str_replace('%','%25',$str);
	$str = str_replace('&','%28',$str);
	$str = str_replace('=','%3D',$str);
	$str = str_replace('|','%7C',$str);
	return $str;
}
?>