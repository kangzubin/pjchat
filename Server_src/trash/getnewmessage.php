<?php
require('inc_template.php');
$smarty= new cls_template();
$smarty->assign('arr',$arr);
$smarty->display('getnewmessage.tpl');
 
?>