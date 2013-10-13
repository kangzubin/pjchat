<?php
function get_message($type='inbox',$limit=10)
{
	$rid  = $_SESSION['rid'];
	if (!$rid) $arr['err']='E:'.E_login_unauthorized;
	for ($pg=1;$pg<$limit/10+1;$pg++)
	{
		switch ($type)
		{
			case 'new';
				$url  = "http://msg.paojiao.cn/msg.do?method=newmsg&t=$pg&rid=$rid";
				break;
			case 'inbox';
				$url  = "http://msg.paojiao.cn/msg.do?method=inbox&t=$pg&rid=$rid";
				break;
			case 'outbox';
				$url  = "http://msg.paojiao.cn/msg.do?method=outbox&t=$pg&rid=$rid";
				break;
		}
		$html = file_get_contents($url);
		//echo $html;

		$pos1 = strpos($html, '没有新信息,要查看信息,请查看<a href="msg.do?method=inbox'); 
		if ($pos1)
		{
			$arr['err']='E:'.E_msg_no_new_message;
			break;
		}
		else
		{
			if ($type=='outbox')
				preg_match_all ('/<a href="msg.do(.)method=outmsgdetail&amp;msgid=([\d]+)(.*)>(.*)<\/a><br\/>([\s]+)(.*)<br\/>([\s]+)\((.*)\)<br\/>/', $html, $matches);
			else
				preg_match_all ('/<a href="msg.do(.)method=inmsgdetail&amp;msgid=([\d]+)(.*)>(.*)<\/a><br\/>([\s]+)(.*)<br\/>([\s]+)\((.*)\)<br\/>/', $html, $matches);

			if (isset($arr['id']))          $arr['id']=   array_merge($arr['id'],$matches[2]);                      else $arr['id']=$matches[2];
			if (isset($arr['shorttext']))   $arr['shorttext']=   array_merge($arr['shorttext'],$matches[4]);        else $arr['shorttext']=pjencode($matches[4]);
			if (isset($arr['from']))        $arr['from']=   array_merge($arr['from'],$matches[6]);                  else $arr['from']=$matches[6];
			//$arr['id']=$matches[2];
			//$arr['shorttext']=$matches[4];
			//$arr['from']=$matches[6];
			//print_r($matches[6]);
			foreach ($arr['from'] as $k=>$v)
			{
				if ($arr['from'][$k]=='系统信息')
					$arr['type'][$k]='sys';
				else
				{
					$arr['type'][$k]='user';
					//$arr['from'][$k]=substr($arr['from'][$k],strpos($arr['from'][$k],':')+1);
					$arr['from'][$k]=trim($arr['from'][$k],"椒友");
				}
			}
			if (isset($arr['time']))   $arr['time']=   array_merge($arr['time'],$matches[8]);           else $arr['time']=$matches[8];
			//$arr['time']=$matches[8];
			foreach ($arr['id'] as $k=>$v)
			{
				//if (strlen($arr['shorttext'][$k])>40 || $arr['type'][$k]=='sys')
				//{
					$tmp=get_message_content($v);
					$arr['fulltext'][$k]=pjencode($tmp['fulltext']);
					$arr['authorid'][$k]=$tmp['authorid'];
					$arr['author'][$k]=pjencode($tmp['author']);
					//$arr['fromid'][$k]=get_message_content($v)['fromid'];
				//}
				//else
				//{
				//	$arr['fulltext'][$k]='';
				//}
			}
			//$fromid=join($arr['fromid'],'|');
		//echo "msgcount=${arr['msgcount']}&msgid=${id}&shorttext=${shorttext}&from=${from}&time=${time}&fulltext=${fulltext}&type=${type}";
		}
	}
	@$arr['msgcount']=count($arr['id']);
	@$arr['ids']=join($arr['id'],'|');
	@$arr['shorttexts']=join($arr['shorttext'],'|');
	@$arr['froms']=join($arr['from'],'|');
	@$arr['authorids']=join($arr['authorid'],'|');
	@$arr['times']=join($arr['time'],'|');
	@$arr['fulltexts']=join($arr['fulltext'],'|');
	@$arr['types']=join($arr['type'],'|');

	return $arr;
}

function get_message_content($msgid)
{
	$rid  = $_SESSION['rid'];
	if (!$rid) $arr['err']='E:'.E_login_unauthorized;
	$url  = "http://msg.paojiao.cn/msg.do?method=inmsgdetail&msgid=$msgid&rid=$rid";

	$html = file_get_contents($url);
	//echo $html;
	$pos1 = strpos($html, '呃...该消息可能属于违规消息已被池塘管理员删除.<br/>'); 
	if ($pos1)
	{
		$arr['err']='E:'.E_msg_message_not_exist;
	}
	else
	{
		if (preg_match ('/<p align="left">([\s]+)内容:(.*)([\s]*)<br\/>([\s]+)系统信息<br\/>/', $html, $matches))
		{
			$arr['fulltext']=$matches[2];
			$arr['content']=$matches[2];
			$arr['authorid']='0';
			$arr['author']="sys";
			//$arr['fromid']=0;
		}
		else if (preg_match ('/<p align="left">([\s]+)内容:(.*)<br\/>([\s]+)发送人:<a href=(.+)userid=([\d]+)(.*)>(.*)<\/a/', $html, $matches))
		{
			$arr['fulltext']=$matches[2];
			$arr['authorid']=$matches[5];
			$arr['author']=$matches[7];
			if(preg_match ('/<img(.*)expression\/(.*)" alt="(.*)" \/>(.*)/', $arr['fulltext'], $matches))
			{
				$arr['emo']=$matches[3];
				$arr['content']=$matches[4];
			}
			else 
				$arr['content']=$arr['fulltext'];
			
			//$arr['fromid']=$matches[5];
		}
	//print_r($matches);
	}
	return $arr;
}

function send_message($to,$content='test',$emotion=0)
{
	$rid  = $_SESSION['rid'];
	if (!$rid) $arr['err']='E:'.E_login_unauthorized;
	$url="http://msg.paojiao.cn/msg.do?method=createmsgend&receiveuserid=$to&content=$content&expression=$emotion&rid=$rid";
	require_once('system.php');
	$html = http_post_content($url);
	//echo $html;
	$pos1 = strpos($html, "信息已发送.<br/>");
	$pos2 = strpos($html, "系统将自动返回...<br/>");
	$pos3 = strpos($html, "您发送短消息速度太快了,请稍休息会儿,谢谢.<br/>");
	$pos4 = strpos($html, "该用户可能由于发布广告等违规操作被池塘管理员删除.<br/>");
	$pos5 = strpos($html, "呃...请填写消息内容.<br/>");

	if ($pos1 && $pos2)
	{
		$arr['ok']='OK!';
	}
	else if($pos3)
	{
		$arr['err']='E:'.E_msg_too_fast;
	}
	else if($pos4)
	{
		$arr['err']='E:'.E_msg_no_reciver;
	}
	else if($pos5)
	{
		$arr['err']='E:'.E_msg_no_content;
	}
	return $arr;
}

?>