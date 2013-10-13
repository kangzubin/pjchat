<?php
function get_friend_list($type='all')
{
	$rid  = $_SESSION['rid'];
	if (!$rid) $arr['err']='E:'.E_login_unauthorized;
	$url  = "http://ct.paojiao.cn/friends.do?method=showfriends&rid=$rid";
	$html1 = file_get_contents($url);
	//print $html1;
	$arr['id']=array();
	$arr['online']=array();
	$arr['gender']=array();
	$arr['nickname']=array();
	$arr['group']=array();
	preg_match ('/<a href="buddies.do(.)method=showbuddies(.*)>(.*)\(([\d]+)\)<\/a>/', $html1, $matches);
	$buddiescount=$matches[4];

	if ($buddiescount)
	{
		$page = 1;
		do{
			$page++;
			$url  = "http://ct.paojiao.cn/buddies.do?method=showbuddies&targtepage=$page&rid=$rid";
			$html = file_get_contents($url);
			//print $html;
			$arr = _parse_buddies($html, $arr);
		}
		while( strpos($html, "<anchor title=\"转到该页\">转到该页<go method=\"post\" href="));
	}
	$html=$html1;
	preg_match ('/&lt;我的好友\(([\d]+)\)&gt;<a href="\/pond\/friends\/friends.jsp/', $html, $matches);
	$friendcount=$matches[1];
	preg_match_all ('/<a href="friends.do(.)method=showfriends&amp;groupid=([\d]+)(.*)>(.*)\(([\d]+)\)<\/a>/', $html, $matches);
	$arr['groupid']=$matches[2];
	$arr['count']=$matches[5];
	$arr['groupname']=$matches[4];

	$arr = _parse_friend($html1, $arr);
	$page = 1;
	while( strpos($html, "&nbsp;>>") && strpos($html, "<anchor title=\"转到该页\">转到该页<go method=\"post\" href="))
	{
		$page++;
		//print $page;
		$url  = "http://ct.paojiao.cn/friends.do?method=showfriends&type=time&targetpage=$page&rid=$rid";
		//print $url;
		$html = file_get_contents($url);
		//print $html;
		$arr = _parse_friend($html, $arr);
	}



	foreach ( $arr['groupid'] as $k=>$i)
	{
		if ($arr['count'][$k])
		{
			$page = 0;
			do{
				$page++;
				$url  = "http://ct.paojiao.cn/friends.do?method=showfriends&groupid=$i&targetpage=$page&rid=$rid";
				//print $url;
				$html = file_get_contents($url);
				//print $html;
				$arr = _parse_friend($html, $arr, $i);
			}
			while( strpos($html, "<anchor title=\"转到该页\">转到该页<go method=\"post\" href="));

		}
	}
	$arr['groupid']=array_merge(array('B',0),$arr['groupid'],array(-1));
	$arr['count']=array_merge(array($buddiescount,$friendcount),$arr['count'],array(-1));
	$arr['groupname']=array_merge(array('我的死党','我的好友'),$arr['groupname'],array('黑名单'));

	$arr['groupcount']=count($arr['groupid']);
	$arr['groupids']=join($arr['groupid'],'|');
	$arr['groupnames']=join($arr['groupname'],'|');

	$arr['fndcount']=count($arr['id']);
	$arr['ids']=join($arr['id'],'|');
	$arr['onlines']=join($arr['online'],'|');
	$arr['genders']=join($arr['gender'],'|');
	$arr['nicknames']=join($arr['nickname'],'|');
	$arr['groups']=join($arr['group'],'|');
	//echo "fndcount=${arr['fndcount']}&id=${arr['ids']}&online=${arr['onlines']}&nickname=${arr['nicknames']}&group=${arr['groups']}&groupcount=${arr['groupcount']}&groupid=${arr['groupids']}&groupname=${arr['groupnames']}";
	return $arr;
}
function _parse_buddies($html, $arr)
{
	preg_match_all ('/<a href="\/pond\/help\/online.jsp(.*)<img src="\/image\/(\d+).gif(.*)>(.*)<\/a>([\s]+)<a href="user.do(.)method=userinfo&amp;userid=([\d]+)(.*)>(.+)<\/a>/', $html, $matches);
	$arr['id']=array_merge($arr['id'],$matches[7]);
	$tmp=array();
	$tmp1=array();
	foreach ( $matches[2] as $k=>$v)
	{
		$tmp[$k]= strlen($v)==2 ? 'OFF' : 'ON' ;
		$tmp1[$k]= $v[strlen($v)-1]==1 ? 'MM' : 'GG' ;
	}
	$arr['online']=array_merge($arr['online'],$tmp);
	$arr['gender']=array_merge($arr['gender'],$tmp1);
	$arr['nickname']=array_merge($arr['nickname'],$matches[9]);
	$tmp=array();
	foreach ( $matches[0] as $k=>$v) $tmp[$k]= 'B';
	$arr['group']=array_merge($arr['group'],$tmp);
	
	return $arr;
}
function _parse_friend($html, $arr, $i=0)
{
	preg_match_all ('/<a href="\/pond\/help\/online.jsp(.*)<img src="\/image\/(\d+).gif(.*)>(.*)<\/a><a href="friends.do(.)method=userinfo&amp;userid=([\d]+)(.*)>([\s]+)(.+)([\s]+)([\s]+)<\/a>/', $html, $matches);
	//print_r($matches);
	
	$arr['id']=array_merge($arr['id'],$matches[6]);
	$tmp=array();
	$tmp1=array();
	foreach ( $matches[2] as $k=>$v)
	{
		$tmp[$k]= strlen($v)==2 ? 'OFF' : 'ON' ;
		$tmp1[$k]= $v[strlen($v)-1]==1 ? 'MM' : 'GG' ;
	}
	$arr['online']=array_merge($arr['online'],$tmp);
	$arr['gender']=array_merge($arr['gender'],$tmp1);
	$arr['nickname']=array_merge($arr['nickname'],$matches[9]);
	$tmp=array();
	foreach ( $matches[0] as $k=>$v) $tmp[$k]= $i;
	$arr['group']=array_merge($arr['group'],$tmp);

	return $arr;
}
?>