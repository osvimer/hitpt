<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
require_once(get_langfile_path("bet.php"));

if ($CURUSER < UC_MODERATOR)
      stderr($lang_bet['std_error'], $lang_bet['text_permission_denied']);

$id = isset($_POST['id']) && is_valid_id($_POST['id']) ? $_POST['id'] : 0;

sql_query("INSERT INTO betoptions(gameid,text) VALUES(".sqlesc($id).",'1')") or sqlerr(__FILE__, __LINE__);
sql_query("INSERT INTO betoptions(gameid,text) VALUES(".sqlesc($id).",'X')") or sqlerr(__FILE__, __LINE__);
sql_query("INSERT INTO betoptions(gameid,text) VALUES(".sqlesc($id).",'2')") or sqlerr(__FILE__, __LINE__);
header("location: {$TBDEV['baseurl']}/bet_opt.php?id=".$id."");
?>
