<?php
include("conn.php");
function htmtocode($content)
{
	$content = str_replace("\n", "<br>", str_replace(" ", "&nbsp;", $content));
	return $content;
}
$SQL="SELECT * FROM `message` order by id desc limit 0,30";
$query=mysql_query($SQL);
echo "<strong>泡椒聊天器v1.2用户反馈列表：</strong>";
while($row=mysql_fetch_array($query)){
?>
<table width=350 border="0" cellpadding="5" cellspacing="1" bgcolor="#add3ef">
<tr bgcolor="#eff3ff"><td>[时间]：<?=$row[lastdate]?>[#<?=$row[id]?>]<br>[IMEI]：<?=$row[imei]?></td></tr>
<tr bgColor="#ffffff"><td>[内容]：<br><?echo htmtocode($row[content]);?></td></tr>
</table>
<? } ?>

