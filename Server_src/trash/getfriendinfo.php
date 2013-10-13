<?php
require_once ('user.php');
//$rid  = $_REQUEST['rid'];
$userid  = $_REQUEST['userid'];

$arr=get_user_info($userid);
require('inc_template.php');		
$smarty= new cls_template();
$smarty->assign('arr',$arr);
$smarty->display('getfriendinfo.tpl');

