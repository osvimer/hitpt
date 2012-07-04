<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
require_once(get_langfile_path("bet.php"));

if ($CURUSER["class"] < UC_MODERATOR)
	stderr($lang_bet['std_error'], $lang_bet['text_permission_denied']);

if (empty($_POST['heading']) || empty($_POST['undertext']) || empty($_POST['endtime'])){
stderr($lang_bet['std_error'], '请输入竞猜项目的名称和结束时间！');
}

$heading = htmlspecialchars($_POST['heading']);
$heading = str_replace("'","",$heading);
$undertext = htmlspecialchars($_POST['undertext']);
$undertext = str_replace("'","",$undertext);
$endtime = (int) $_POST['endtime'] + 0;
$sort = (int) $_POST['sort'];
sql_query("INSERT INTO betgames(heading, undertext, endtime, sort, creator) VALUES(".sqlesc($heading).", ".sqlesc($undertext).", ".sqlesc($endtime).", ".sqlesc($sort).", '$CURUSER[username]')") or sqlerr(__FILE__, __LINE__);
header("location: {$TBDEV['baseurl']}/bet_admin.php");
?>
