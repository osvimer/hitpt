<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
require_once(get_langfile_path("bet.php"));

if ($CURUSER["class"] < UC_USER)
	stderr($lang_bet['std_error'], $lang_bet['text_bet_request']);

$id = isset($_POST['id']) && is_valid_id($_POST['id']) ? $_POST['id'] : 0;
$bonus = (int) $_POST['bonus'];

if($CURUSER['seedbonus'] < $bonus){
stderr($lang_bet['std_error'], $lang_bet['text_not_enough']);
}

if($bonus < 1){
stderr($lang_bet['std_error'], $lang_bet['text_too_small']);
}

$res = sql_query("SELECT * FROM betoptions WHERE id =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
$a = mysql_fetch_assoc($res);
$gameid = (int) $a['gameid'];

if($gameid== 0){
header("location: {$TBDEV['baseurl']}/bet.php");
exit;
}


$res2 = sql_query("SELECT * from betgames where id =".sqlesc($gameid)."") or sqlerr(__FILE__, __LINE__);
$s = mysql_fetch_assoc($res2);

if($s['active'] == 0){
header("location: {$TBDEV['baseurl']}/bet.php");
exit;
}

$k = sql_query("SELECT * FROM bets WHERE optionid = ".sqlesc($a["id"])." AND userid =".sqlesc($CURUSER["id"])."") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($k) > 0)
{
    stderr($lang_bet['std_sorry'], $lang_bet['text_already_bet']);
}


$tid = time();

sql_query("INSERT INTO bets(gameid,bonus,optionid,userid,date) VALUES(".sqlesc($gameid).", ".sqlesc($bonus).", ".sqlesc($id).", ".sqlesc($CURUSER["id"]).", '$tid')") or sqlerr(__FILE__, __LINE__);
sql_query("UPDATE users SET seedbonus = seedbonus -".sqlesc($bonus)." WHERE id =".sqlesc($CURUSER["id"])."") or sqlerr(__FILE__, __LINE__);
sql_query("INSERT INTO betlog(userid,date,msg,bonus) VALUES($CURUSER[id], '$tid', 'Bet. ".$s['heading']." -> ".$a['text']."-".$bonus." Points.',-$bonus)") or sqlerr(__FILE__, __LINE__);

$e = sql_query("SELECT * FROM betoptions WHERE gameid =".sqlesc($gameid)."") or sqlerr(__FILE__, __LINE__);
while($f = mysql_fetch_assoc($e))
{

$optionid = $f['id'];
$total = 0;
$optiontotal = 0;

$b = sql_query("SELECT * FROM bets WHERE gameid = ".sqlesc($gameid)."") or sqlerr(__FILE__, __LINE__);
while($c = mysql_fetch_assoc($b))
{
$total += $c['bonus'];
if($c['optionid'] == $optionid)
$optiontotal += $c['bonus'];
}
if($optiontotal == 0)
$odds = 0.00;
else
$odds = number_format($total/$optiontotal, 2, '.', '');
sql_query("UPDATE betoptions SET odds = ".sqlesc($odds)." WHERE id = ".sqlesc($optionid)."") or sqlerr(__FILE__, __LINE__);
}

header("location: {$TBDEV['baseurl']}/bet_coupon.php");
?>
