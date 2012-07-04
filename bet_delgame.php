<?php
require "include/bittorrent.php";
 
dbconn();
loggedinorreturn();
require_once(get_langfile_path("bet.php"));

if ($CURUSER < UC_MODERATOR)
      stderr($lang_bet['std_error'], $lang_bet['text_permission_denied']);

$id = isset($_GET['id']) && is_valid_id($_GET['id']) ? $_GET['id'] : 0;

$res = sql_query("SELECT * FROM bets where gameid =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($res) > 0)
{
stderr($lang_bet['std_error'], $lang_bet['text_try_to_del']);
}
else
{
sql_query("DELETE FROM betgames WHERE id =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
sql_query("DELETE FROM betoptions WHERE gameid =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
sql_query("DELETE FROM bets WHERE gameid =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);

header("Location: {$TBDEV['baseurl']}/bet_admin.php");

}
?>
