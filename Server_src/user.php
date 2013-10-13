<?php
require_once ('inc_text.php');

function get_user_info($userid)
{
	$rid  = $_SESSION['rid'];
	if (!$rid) $arr['err']='E:'.E_login_unauthorized;
	$url  = "http://ct.paojiao.cn/user.do?method=userinfo&userid=$userid&rid=$rid";
	//echo $url;
	$html = file_get_contents($url);
	//echo $html;
	$pos1 = strpos($html, '呃...没有您要找的这位椒友,请确认ID后重新查找.<br/>'); 
	if ($pos1)
	{
		$arr['err']='E:'.E_user_not_exist;
	}
	else
	{
		preg_match ('/ID:([\d]+)\((.*)\)(.*)<img src="\/image\/([\d]+).gif"/', $html, $matches);
		$arr['id']=$matches[1];
		$arr['checked']=$matches[2]=='已验证' ? 'Y' : 'N';
		$arr['online']=strlen($matches[4])==2 ? 'OFF' : 'ON';
		preg_match ("/昵称:(.+)</", $html, $matches);
		$arr['nickname'] = pjencode($matches[1]);
		preg_match ('/<img src="(.*)" alt="头像" \/>/', $html, $matches);
		$arr['face'] = pjencode($matches[1]);
		if ($arr['face']=='http://down.s60net.com:8118/photo/face/paojiao.gif' ) $arr['face']='';
		preg_match ("/银子:(.*)>(\d+)<\/a> 金子:(.*)(\d+)/", $html, $matches);
		$arr['silver']=$matches[2];
		$arr['gold']=$matches[4];
		preg_match ("/水草\(积分\):(.*)>(\d+)<\/a>/", $html, $matches);
		$arr['grass']=$matches[2];
		preg_match ('/池塘等级:<anchor title="池塘等级">(.*)\(([\d]+)级\)<go method="post" href="user.do/', $html, $matches);
		$arr['ctrank']=$matches[2];
		preg_match ('/在线等级:<a href=(.*)alt="在线(\d+)级/', $html, $matches);
		@$arr['olrank']=$matches[2];
		if (preg_match ('/<br\/>家族:<a(.+)method=home&amp;id=(\d+)(.*)>(.*)<\/a>\((.+)\)/', $html, $matches))
		{
			$arr['familyid']=$matches[2];
			$arr['familyname']=$matches[4];
			$arr['familypost']=$matches[5];
		}
		//无家族的
		if (preg_match ('/<br\/>部门荣誉:(.*)/', $html, $matches))
		{
			$tmp=$matches[1];
			preg_match_all ('/<img src="image\/pond\/banzhu\/group([\d]+)\.gif" alt="(.{0,20})"/', $tmp, $matches);
			//print_r( $matches);
			$arr['branchid']=$matches[1];
			$arr['branchname']=$matches[2];
			$arr['branchcount']=count($arr['branchid']);
			$arr['branchids']=join($arr['branchid'],'|');
			$arr['branchnames']=join($arr['branchname'],'|');
		}
		if (preg_match ('/>恋人<\/a>:<a href="user.do(.*)userid=(\d+)(.*)>(.*)<\/a>/', $html, $matches))
		{
			$arr['loverid']=$matches[2];
			$arr['lovername']=$matches[4];
		}
		if (preg_match ('/<br\/>死党:(.*)/', $html, $matches))
		{
			$tmp=$matches[1];
			preg_match_all ('/href="user.do(.{0,30})userid=([\d]+)(.{0,30})>(.{0,60})<\/a/', $tmp, $matches);
			$arr['buddiesid']=$matches[2];
			$arr['buddiesname']=$matches[4];
			$arr['buddiescount']=count($arr['buddiesid']);
			$arr['buddiesids']=join($arr['buddiesid'],'|');
			$arr['buddiesnames']=join($arr['buddiesname'],'|');
		}
	
		if (preg_match ('/>TA的同好<(.*)/', $html, $matches))
		{
			$tmp=$matches[1];
			preg_match_all ('/href="love.do(.{0,20})id=([\d]+)(.{0,30})>(.{0,60})<\/a/', $tmp, $matches);
			$arr['hobbyid']=$matches[2];
			$arr['hobbyname']=$matches[4];
			$arr['hobbycount']=count($arr['hobbyid']);
			$arr['hobbyids']=join($arr['hobbyid'],'|');
			$arr['hobbynames']=join($arr['hobbyname'],'|');
		}


		if (preg_match ('/机型:<a(.*)/', $html, $matches))
		{
			$tmp=$matches[1];
			preg_match_all ('/href(.{0,60});id=([\d]+)(.{0,30})>(.{1,5})</', $tmp, $matches);
//			print_r($matches);

			$arr['phoneid']=join($matches[2],'|');
			$arr['phonename']=join($matches[4],'|');
		}
		preg_match ('/入塘时间:([\d\/]+)\((\d+)天\)<br\/>/', $html, $matches);
		$arr['jointime']=$matches[1];
		$arr['joinday']=$matches[2];
			
		preg_match ('/所在地:<anchor>(.*)<go method="post"(.*)<\/anchor>-<anchor>(.*)<go method="post"/', $html, $matches);
		$arr['provience']=$matches[1];
		$arr['city']=$matches[3];
		preg_match ('/签名(<\/a>)?:(.*)<br\/>/', $html, $matches);
		$arr['sign']=pjencode($matches[2]);
		preg_match ('/上次登入:(.*)<br\/>/', $html, $matches);
		$arr['lastlogin']=$matches[1];

		preg_match_all ('/<a href="user.do(.)method=userinfo&amp;userid=(\d+)(.*)>(.*)<\/a>\((.*)\)<br\/>/', $html, $matches);
		
		$arr['guestid']=$matches[2];
		$arr['guestname']=pjencode($matches[4]);
		$arr['guesttime']=$matches[5];
		$arr['guestcount']=count($arr['guestid']);
		$arr['guestids']=join($arr['guestid'],'|');
		$arr['guestnames']=join($arr['guestname'],'|');
		$arr['guesttimes']=join($arr['guesttime'],'|');
		
		
		
		$url  = "http://ct.paojiao.cn/user.do?method=onlinerank&userid=".$arr['id']."&rid=$rid";
		//echo $url;
		$html = file_get_contents($url);
		//echo $html;
		
		preg_match ("/在线时长:(.+)</", $html, $matches);
		//print_r($matches);
		$arr['onlinetime'] = $matches[1];

		preg_match ("/升级剩余时间:(.+)</", $html, $matches);
		//print_r($matches);
		$arr['upgradetime'] = $matches[1];

		preg_match ("/排名:(.+)</", $html, $matches);
		//print_r($matches);
		$arr['onlinerank'] = $matches[1];

	}
	return $arr;
}

function add_user($userid)
{}

function delete_user($userid)
{}