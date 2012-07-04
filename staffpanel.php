<?php
ob_start();
require_once("include/bittorrent.php");
dbconn();
loggedinorreturn();
stdhead("Administration");
print("<h1 align=center>管理组面板</h1>");
if (get_user_class() < UC_MODERATOR)
{
	stdmsg("错误", "你的权限不允许访问!!!");
	stdfoot();
	exit;
}
begin_main_frame();

///////////////////// SysOp Only \\\\\\\\\\\\\\\\\\\\\\\\\\\\
if (get_user_class() >= UC_SYSOP) {
	echo("<h1 align=center>..:: 系统设定  ::..</h1>");
	print("<br />");
	print("<br />");
	print("<table width=80% border=1 cellspacing=0 cellpadding=5 align=center>");
	echo("<td class=colhead align=left>选项</td><td class=colhead align=left>说明</td>");
	$query = "SELECT * FROM sysoppanel";
	$sql = sql_query($query);
	while ($row = mysql_fetch_array($sql)) {
		$id = $row['id'];
		$name = $row['name'];
		$url = $row['url'];
		$info = $row['info'];

		echo("<tr><td class=rowfollow align=left><strong><a href=$url>$name</a></strong></td> <td class=rowfollow align=left>$info</td></tr>");
	}
	print("</table>");
	print("<br />");
	print("<br />");
}
///////////////////// Admin Only \\\\\\\\\\\\\\\\\\\\\\\\\\\\
if (get_user_class() >= UC_ADMINISTRATOR) {
	echo("<h1 align=center>..:: 管理设定 :..</h1>");
	print("<br />");
	print("<br />");
	print("<table width=80% border=1 cellspacing=0 cellpadding=5 align=center>");
	echo("<td class=colhead align=left>选项</td><td class=colhead align=left>说明</td>");
	$query = "SELECT * FROM adminpanel";
	$sql = sql_query($query);
	while ($row = mysql_fetch_array($sql)) {
		$id = $row['id'];
		$name = $row['name'];
		$url = $row['url'];
		$info = $row['info'];

		echo("<tr><td class=rowfollow align=left><strong><a href=$url>$name</a></strong></td> <td class=rowfollow align=left>$info</td></tr>");
	}
	print("</table>");
	print("<br />");
	print("<br />");
}
///////////////////// Moderator Only \\\\\\\\\\\\\\\\\\\\\\\\\\\\
if (get_user_class() >= UC_MODERATOR) {
	echo("<h1 align=center>..:: 日常维护  ::..</h1>");
	print("<br />");
	print("<br />");
	print("<table width=80% border=1 cellspacing=0 cellpadding=5 align=center>");
	echo("<td class=colhead align=left>选项</td><td class=colhead align=left>说明</td>");
	$query = "SELECT * FROM modpanel";
	$sql = sql_query($query);
	while ($row = mysql_fetch_array($sql)) {
		$id = $row['id'];
		$name = $row['name'];
		$url = $row['url'];
		$info = $row['info'];

		echo("<tr><td class=rowfollow align=left><strong><a href=$url>$name</a></strong></td> <td class=rowfollow align=left>$info</td></tr>");
	}

	print("</table>");
	print("<br />");
	print("<br />");
}
end_main_frame();
stdfoot();
?>
