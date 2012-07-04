<?php
require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();
require_once(get_langfile_path("bet.php")); 

if ($CURUSER["class"] < UC_MODERATOR)
    stderr($lang_bet['std_error'], $lang_bet['text_permission_denied']);

$id = isset($_POST['id']) && is_valid_id($_POST['id']) ? $_POST['id'] : 0;

$heading = htmlspecialchars($_POST['heading']);
$heading = str_replace("'","",$heading);
$undertext = htmlspecialchars($_POST['undertext']);
$undertext = str_replace("'","",$undertext);
$endtime = (int) $_POST['endtime'] + 0;
$sort = (int) $_POST['sort'];
$res = "UPDATE betgames SET heading =".sqlesc($heading).", undertext=".sqlesc($undertext).", endtime=".sqlesc($endtime).", sort=".sqlesc($sort)." WHERE id =".sqlesc($id)."";
sql_query($res) or sqlerr(__FILE__, __LINE__);
header("location: {$TBDEV['baseurl']}/bet_admin.php");

?>
