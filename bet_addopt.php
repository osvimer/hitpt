<?php
require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();
require_once(get_langfile_path("bet.php"));

if (empty($_POST['opt'])){
stderr($lang_bet['std_error'], '请输入竞猜选项名');
}

if ($CURUSER < UC_MODERATOR)
      stderr($lang_bet['std_error'], $lang_bet['text_permission_denied']);

$id = isset($_POST['id']) && is_valid_id($_POST['id']) ? $_POST['id'] : 0;

$text = htmlspecialchars($_POST['opt'], ENT_QUOTES);

sql_query("INSERT INTO betoptions(gameid,text) VALUES(".sqlesc($id).",".sqlesc($text).")") or sqlerr(__FILE__, __LINE__);
header("location: {$TBDEV['baseurl']}/bet_opt.php?id=".$id."");
?>
