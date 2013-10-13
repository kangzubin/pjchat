<?php
$rid  = $_REQUEST['rid'];
$url  = "http://msg.paojiao.cn/msg.do?method=inbox&rid=$rid";
//$url="http://msg.paojiao.cn/msg.do?method=inmsgdetail&msgid=22086163&rid=$rid";
$html = file_get_contents($url);
//echo $html;


$pos1 = strpos($html, '没有新信息,要查看信息,请查看<a href="msg.do?method=inbox'); 

if ($pos1)
{
	$arr['err']='E:621';
}
else
{
	preg_match_all ('/<a href="msg.do(.)method=inmsgdetail&amp;msgid=([\d]+)(.*)>(.*)<\/a><br\/>([\s]+)(.*)<br\/>([\s]+)\((.*)\)<br\/>/', $html, $matches);
	$arr['id']=$matches[2];
	$arr['shorttext']=$matches[4];
	$arr['from']=$matches[6];
	//print_r($matches[6]);
	foreach ($arr['from'] as $k=>$v)
	{
		if ($arr['from'][$k]=='系统信息')
			$arr['type'][$k]='sys';
		else
		{
			$arr['type'][$k]='user';
			$arr['from'][$k]=substr($arr['from'][$k],strpos($arr['from'][$k],':')+1);
		}
	}
	$arr['time']=$matches[8];

	foreach ($arr['id'] as $k=>$v)
	{
		if (strlen($arr['shorttext'][$k])>40 || $arr['type'][$k]=='sys')
		{
			$url  = "http://msg.paojiao.cn/msg.do?method=inmsgdetail&msgid=$v&rid=$rid";

			$html = file_get_contents($url);
			if ($arr['type'][$k]=='sys')
			{
				preg_match ('/<p align="left">([\s]+)内容:(.*)<br\/>([\s]+)系统信息<br\/>/', $html, $matches);
				$arr['fulltext'][$k]=$matches[2];
				//$arr['fromid'][$k]=0;
			}
			else 
			{
				preg_match ('/<p align="left">([\s]+)内容:(.*)<br\/>([\s]+)发送人:<a href=(.+)userid=([\d]+)/', $html, $matches);
				$arr['fulltext'][$k]=$matches[2];
				//$arr['fromid'][$k]=$matches[5];
			}
			//print_r($matches);
		}
		else
		{
			$arr['fulltext'][$k]='';
		}
	}
	$arr['msgcount']=count($arr['id']);
	$id=join($arr['id'],'|');
	$shorttext=join($arr['shorttext'],'|');
	$from=join($arr['from'],'|');
	$time=join($arr['time'],'|');
	$fulltext=join($arr['fulltext'],'|');
	$type=join($arr['type'],'|');
	//$fromid=join($arr['fromid'],'|');
	echo "msgcount=${arr['msgcount']}&msgid=${id}&shorttext=${shorttext}&from=${from}&time=${time}&fulltext=${fulltext}&type=${type}";
 
 
 
}
 
?>