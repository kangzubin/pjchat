<?php
include ("inc_database.php");
echo '<head><meta http-equiv="content-type" content="text/xml;charset=utf8"></head><body>';
echo "泡椒聊天登录记录查询：<br/><br/>";

db_connect();

$result = mysql_query("SELECT * FROM `login_log` ORDER BY time DESC LIMIT 0,50");
echo "<table border='1'>
<tr>
<th>次数</th>
<th>网站</th>
<th>用户ID</th>
<th>昵称</th>
<th>登录时间</th>
<th>客户端</th>
</tr>";

while($row = mysql_fetch_array($result))
  {
  echo "<tr>";
  echo "<td>" . $row['id'] . "</td>";
  echo "<td>" . $row['site'] . "</td>";
  echo "<td>" . $row['uid'] . "</td>";
  echo "<td>" . $row['name'] . "</td>";
  echo "<td>" . $row['time'] . "</td>";
  echo "<td>" . $row['agent'] . "</td>";
  echo "</tr>";
  }
echo "</table>";


$result = mysql_query("SELECT * FROM `feedback` ORDER BY time DESC LIMIT 0,20");
echo "<table border='1'>
<tr>
<th>次数</th>
<th>昵称</th>
<th>内容</th>
<th>反馈时间</th>
</tr>";

while($row = mysql_fetch_array($result))
  {
  echo "<tr>";
  echo "<td>" . $row['id'] . "</td>";
  echo "<td>" . $row['nickname'] . "</td>";
  echo "<td>" . $row['content'] . "</td>";
  echo "<td>" . $row['time'] . "</td>";
  echo "</tr>";
  }
echo "</table></body>";


?>