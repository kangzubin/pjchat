<?php
function get_topic_list($ctid,$limit=15)
{
	$rid  = $_SESSION['rid'];
	if (!$rid) $arr['err']='E:'.E_login_unauthorized;
	for ($pg=1;$pg<$limit/15+1;$pg++)
	{
		$url  = "http://ct.paojiao.cn/topic.do?method=home&id=$ctid&targetpage=$pg&rid=$rid";
		$html = file_get_contents($url);
		//echo $html;
		$pos1 = strpos($html, '呃...该池塘分类可能被池塘管理者删除.<br/>'); 
		if ($pos1)
		{
			$arr['err']='E:'.E_topic_ct_not_exist;
		}
		else
		{
			preg_match_all ('/(.*)(\s)<a href="topic.do(.)method=topicdetail&amp;topicid=([\d]+)(.{0,70})>(.*)<\/a>(.*)<br\/>\((.*)，<a href="topic.do(.)method=allreply(.*)userid=([\d]+)(.*)>回([\d]+)<\/a>\/阅([\d]+)\)<br\/>/', $html, $matches);
			//print_r($matches);
			$tmp=array();
			foreach ( $matches[1] as $k=>$v)
			{
				$str='';
				preg_match_all ('/\[(.{1,6})\]/', $v, $matches1);
				foreach ($matches1[1] as $i)
				{
					$str.=$i;
				}
				$tmp[$k]=$str;
				$tmp1[$k]=$matches1[1];
				$tmp2[$k]=$matches[7][$k]=='<small>*new!</small>'?'1':'';
			}
			if (isset($arr['headarr']))   $arr['headarr']=   array_merge($arr['headarr'],$tmp1);           else $arr['headarr']=$tmp1; 
			if (isset($arr['header']))    $arr['header']=    array_merge($arr['header'],$tmp);             else $arr['header']=$tmp;
			if (isset($arr['topicid']))   $arr['topicid']=   array_merge($arr['topicid'],$matches[4]);     else $arr['topicid']=$matches[4];
			if (isset($arr['title']))     $arr['title']=     array_merge($arr['title'],$matches[6]);       else $arr['title']=$matches[6];
			if (isset($arr['new']))       $arr['new']=       array_merge($arr['new'],$tmp2);               else $arr['new']=$tmp2;
			if (isset($arr['author']))    $arr['author']=    array_merge($arr['author'],$matches[8]);      else $arr['author']=$matches[8];
			if (isset($arr['authorid']))  $arr['authorid']=  array_merge($arr['authorid'],$matches[11]);   else $arr['authorid']=$matches[11];
			if (isset($arr['visit']))     $arr['visit']=     array_merge($arr['visit'],$matches[13]);      else $arr['visit']=$matches[13];
			if (isset($arr['reply']))     $arr['reply']=     array_merge($arr['reply'],$matches[14]);      else $arr['reply']=$matches[14];
		}
	}
		
	@$arr['topiccount']=count($arr['topicid']);
	@$arr['headers']=join($arr['header'],'|');
	@$arr['topicids']=join($arr['topicid'],'|');
	@$arr['titles']=join($arr['title'],'|');
	@$arr['news']=join($arr['new'],'|');
	@$arr['authors']=join($arr['author'],'|');
	@$arr['authorids']=join($arr['authorid'],'|');
	@$arr['visits']=join($arr['visit'],'|');
	@$arr['replys']=join($arr['reply'],'|');
//print_r($arr);
	//echo "fndcount=${arr['fndcount']}&id=${arr['ids']}&online=${arr['onlines']}&nickname=${arr['nicknames']}&group=${arr['groups']}&groupcount=${arr['groupcount']}&groupid=${arr['groupids']}&groupname=${arr['groupnames']}";
	return $arr;
}

function get_topic_detail($topicid)
{
	$rid  = $_SESSION['rid'];
	if (!$rid) $arr['err']='E:'.E_login_unauthorized;
	$url  = "http://ct.paojiao.cn/topic.do?method=topicdetail&topicid=$topicid&rid=$rid";
	$html = file_get_contents($url);
	//echo $html;
	$pos1 = strpos($html, '很抱歉,帖子可能由于发广告,或其他异常原因被池塘管理者删除.请更换其他帖浏览.<br/>'); 
	if ($pos1)
	{
		$arr['err']='E:'.E_topic_not_exist;
	}
	else
	{
		$sec=explode('-----------<br/>',$html);
		//print_r($sec);
		
		preg_match('/<p align="left">(\s+)标题:(.*)<br\/>(\s+)来自:<a href="user.do(.*)userid=(\d+)(.{0,30})>(.{0,60})<\/a>(.*)(\s+)(.*)(\s+)(.*) 阅(\d+)次<br\/>(\s+)/', $sec[0], $matches);
		$arr['title']=pjencode($matches[2]);
		$arr['authorid']=$matches[5];
		$arr['author']=$matches[7];
		$arr['time']=$matches[12];
		$arr['visit']=$matches[13];
		$tmp=explode('【本帖附件】<br/>',$sec[1]);
		$arr['content']=trim($tmp[0]);
		$arr['content']=pjencode(trim($arr['content'],'<br/>'));
		if(isset($tmp[1]))
		{
			preg_match_all('/<a href="topic.do(.)method=download&amp;resid=(\d+)(.*)respath=(.*)&amp;filename=(.*)&(.*)(\s+)\(([0-9BKM\.]+),([\w]+),([\d]+)次,([\d]+)%说好\)/', $tmp[1], $matches);
			foreach ($matches[2] as $k=>$v)
			{
				//$arr['imgname'][$k]=$v;
				$arr['imgresid'][$k]=$matches[2][$k];
				$arr['imgsize'][$k]=$matches[8][$k];
				$arr['imgtype'][$k]=$matches[9][$k];
				$arr['imgdowntimes'][$k]=$matches[10][$k];
				$arr['imggood'][$k]=$matches[11][$k];
				$arr['imgdownload'][$k]=$matches[4][$k].$matches[5][$k];
			}
			//print_r($matches);
			@$arr['imgcount']=count($arr['imgresid']);
			@$arr['imgresids']=join($arr['imgresid'],'|');
			@$arr['imgsizes']=join($arr['imgsize'],'|');
			@$arr['imgtypes']=join($arr['imgtype'],'|');
			@$arr['imgdowntimess']=join($arr['imgdowntimes'],'|');
			@$arr['imggoods']=join($arr['imggood'],'|');
			@$arr['imgdownloads']=join($arr['imgdownload'],'|');
			
			preg_match_all('/<anchor title="(.*)">(\1)<go method="post" href="topic.do(.)method=download&amp;resid=([\d]+)&amp;topicid=([\d]+)(.*)>([\s]+)<postfield name="respath" value="(.*)"\/>([\s]+)<postfield name="filename" value="(.*)"\/>(\s+)<\/go><\/anchor>(\s+)\(([0-9BKM\.]+),([\w]+),([\d]+)次,([\d]+)%说好\)<br\/>/',$tmp[1], $matches1);
			foreach ($matches1[2] as $k=>$v)
			{
				$arr['attname'][$k]=$v;
				$arr['attresid'][$k]=$matches1[4][$k];
				$arr['attsize'][$k]=$matches1[13][$k];
				$arr['atttype'][$k]=$matches1[14][$k];
				$arr['attdowntimes'][$k]=$matches1[15][$k];
				$arr['attgood'][$k]=$matches1[16][$k];
				$arr['attdownload'][$k]=$matches1[8][$k].$matches1[10][$k];
			}
			//print_r($matches1);
			@$arr['attcount']=count($arr['attname']);
			@$arr['attnames']=join($arr['attname'],'|');
			@$arr['attresids']=join($arr['attresid'],'|');
			@$arr['attsizes']=join($arr['attsize'],'|');
			@$arr['atttypes']=join($arr['atttype'],'|');
			@$arr['attdowntimess']=join($arr['attdowntimes'],'|');
			@$arr['attgoods']=join($arr['attgood'],'|');
			@$arr['attdownloads']=join($arr['attdownload'],'|');
			
			if(preg_match('/签名:(.*)<br\/>/',$sec[2],$tmp3))
				$arr['attname']=$tmp3[1];
			//print_r($tmp3);
			//print $sec[4].$sec[4];
			@preg_match('/精彩回复\(([\d]+)帖\)/',$sec[4].$sec[5],$tmp4);
			//print_r($tmp4);
				$arr['reply']=$tmp4[1];
		}
	}
	//print_r($arr);
	return $arr;
}

function publish_topic($ctid,$topic,$content,$type='')
{
	$rid  = $_SESSION['rid'];
	if (!$rid) $arr['err']='E:'.E_login_unauthorized;
	$time = date('YmdHis');
	$url="http://ct.paojiao.cn/topic.do?method=savetopicend&id=$ctid&topic=$topic&content=$content&topictype=$type&time=$time&rid=$rid";
	//$html = file_get_contents($url);
	
	require_once('system.php');
	$html = http_post_content($url);
	echo $html;
	$pos1 = strpos($html, "发表成功.<br/>");
	$pos2 = strpos($html, "您会获得相应水草<br/>");
	$pos3 = strpos($html, "呃...该池塘分类可能被池塘管理者删除.<br/>");
	$pos4 = strpos($html, "呃...标题不能小于3个字.<br/>");
	$pos5 = strpos($html, "呃...内容不能小于5个字.<br/>");
	$pos6 = strpos($html, "呃...每位椒友1分钟内只能发表1个主题帖子,请稍作休息后再继续发帖喔.<br/>");
	//$pos7 = strpos($html, "呃...每位椒友1分钟内只能发表1个主题帖子,请稍作休息后再继续发帖喔.<br/>");
	if ($pos1 && $pos2)
	{
		$arr['ok']='OK!';
	}
	else if($pos3)
	{
		$arr['err']='E:'.E_topic_ct_not_exist;
	}
	else if($pos4)
	{
		$arr['err']='E:'.E_topic_short_title;
	}
	else if($pos5)
	{
		$arr['err']='E:'.E_topic_short_content;
	}
	else if($pos6)
	{
		$arr['err']='E:'.E_topic_send_fast;
	}
	return $arr;

}

function publish_reply($topicid,$content,$emotion=0)
{
	$rid  = $_SESSION['rid'];
	if (!$rid) $arr['err']='E:'.E_login_unauthorized;
//	$time = date('YmdHis');
	$url="http://ct.paojiao.cn/topic.do?method=savetopicend&id=$ctid&topic=$topic&content=$content&topictype=$type&time=$time&rid=$rid";
	//$html = file_get_contents($url);
	
//	require_once('system.php');
//	$html = http_post_content($url);
	echo $html;
/*	$pos1 = strpos($html, "发表成功.<br/>");
	$pos2 = strpos($html, "您会获得相应水草<br/>");
	$pos3 = strpos($html, "呃...该池塘分类可能被池塘管理者删除.<br/>");
	$pos4 = strpos($html, "呃...标题不能小于3个字.<br/>");
	$pos5 = strpos($html, "呃...内容不能小于5个字.<br/>");
	$pos6 = strpos($html, "呃...每位椒友1分钟内只能发表1个主题帖子,请稍作休息后再继续发帖喔.<br/>");
	$pos7 = strpos($html, "呃...每位椒友1分钟内只能发表1个主题帖子,请稍作休息后再继续发帖喔.<br/>");
	if ($pos1 && $pos2)
	{
		$arr['ok']='OK!';
	}
	else if($pos3)
	{
		$arr['err']='E:702';
	}
	else if($pos4)
	{
		$arr['err']='E:703';
	}
	else if($pos5)
	{
		$arr['err']='E:704';
	}
	else if($pos6)
	{
		$arr['err']='E:705';
	}
	else if($pos7)
	{
		$arr['err']='E:706';
	}*/
	return $arr;

}

?>