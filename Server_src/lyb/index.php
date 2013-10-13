<?php
include("conn.php");
$page=0;
if($_POST['submit'])
{
	if ($_POST[imei] && $_POST[content])
	{
		$sql="insert into message (id,imei,content,lastdate)"."values ('','$_POST[imei]','$_POST[content]',NOW())";
		mysql_query($sql);
		$page=1;
	}
	else $page=2;
}
if ($page==0)
{
	echo "<form action='index.php' method='post'>";
	echo "手机IMEI：<input type='text' name='imei' /><br/>";
	echo "反馈内容：<textarea name='content'></textarea><br/>";
	echo "<input type='submit' name='submit' value='提交'/></form>";
}
elseif ($page==1) echo"OK";
elseif ($page==2) echo"ERROR";
?>
