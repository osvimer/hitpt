<?php
require_once("include/bittorrent.php");
function bark($msg) {
stdhead();
stdmsg($lang_nowarn['text_updatefail'], $msg);
stdfoot();
exit;
}
dbconn();
loggedinorreturn();
require_once(get_langfile_path());

if(isset($_POST["nowarned"])&&($_POST["nowarned"]=="nowarned")){
//if (get_user_class() >= UC_SYSOP) {
if (get_user_class() < UC_MODERATOR)
stderr("对不起", "你的权限不允许访问。");
{
if (empty($_POST["usernw"]) && empty($_POST["desact"]) && empty($_POST["delete"]))
   bark($lang_nowarn['text_selectuser']);

if (!empty($_POST["usernw"]))
{
$msg = sqlesc($lang_nowarn['text_yourwarningby'] . $CURUSER['username'] .$lang_nowarn['text_remove']."。");
$added = sqlesc(date("Y-m-d H:i:s"));
$userid = implode(", ", $_POST[usernw]);
//sql_query("INSERT INTO messages (sender, receiver, msg, added) VALUES (0, $userid, $msg, $added)") or sqlerr(__FILE__, __LINE__);

$r = sql_query("SELECT modcomment FROM users WHERE id IN (" . implode(", ", $_POST[usernw]) . ")")or sqlerr(__FILE__, __LINE__);
$user = mysql_fetch_array($r);
$exmodcomment = $user["modcomment"];
$modcomment = date("Y-m-d") .$lang_nowarn['text_warningby'] . $CURUSER['username'].$lang_nowarn['text_remove'] . "。\n". $modcomment . $exmodcomment;
sql_query("UPDATE users SET modcomment=" . sqlesc($modcomment) . " WHERE id IN (" . implode(", ", $_POST[usernw]) . ")") or sqlerr(__FILE__, __LINE__);

$do="UPDATE users SET warned='no', warneduntil='0000-00-00 00:00:00' WHERE id IN (" . implode(", ", $_POST[usernw]) . ")";
$res=sql_query($do);}

if (!empty($_POST["desact"])){
$do="UPDATE users SET enabled='no' WHERE id IN (" . implode(", ", $_POST['desact']) . ")";
$res=sql_query($do);}
}
}
header("Refresh: 0; url=warned.php");
?>
