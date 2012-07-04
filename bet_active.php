<?php
require "include/bittorrent.php";
 
dbconn(false);
loggedinorreturn();
require_once(get_langfile_path("bet.php"));

if ($CURUSER < UC_MODERATOR)
      stderr($lang_bet['std_error'], $lang_bet['text_permission_denied']);

$id = isset($_GET['id']) && is_valid_id($_GET['id']) ? $_GET['id'] : 0;

$res = sql_query("SELECT active FROM betgames WHERE id =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
$res = mysql_fetch_assoc($res);

if($res['active'] == 0)

$status = 1;
else
$status = 0;

if(isset($res['finished']) == 1)
$status = 0;
sql_query("UPDATE betgames SET active =".sqlesc($status)." WHERE id =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
header("location: {$TBDEV['baseurl']}/bet_admin.php");
?>
