<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
require_once(get_langfile_path("bet.php"));

if ($CURUSER < UC_MODERATOR)
    stderr($lang_bet['std_error'], $lang_bet['text_permission_denied']);

$id = isset($_GET['id']) && is_valid_id($_GET['id']) ? $_GET['id'] : 0;

$bid = isset($_GET['b']) && is_valid_id($_GET['b']) ? $_GET['b'] : 0;



$res= sql_query("SELECT * FROM bets where optionid =".sqlesc($id)."")or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($res) > 0)
{
stderr($lang_bet['std_error'], $lang_bet['text_try_to_del']);
}
else
{
sql_query("DELETE FROM betoptions WHERE id =".sqlesc($id)."")or sqlerr(__FILE__, __LINE__);

header("Location: {$TBDEV['baseurl']}/bet_opt.php?id=".$bid."");
}

?>
