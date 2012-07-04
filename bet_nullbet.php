<?php
require "include/bittorrent.php";
dbconn(false);
loggedinorreturn();
require_once(get_langfile_path("bet.php"));

if ($CURUSER["class"] < UC_MODERATOR)
	stderr($lang_bet['std_error'], $lang_bet['text_permission_denied']);

$HTMLOUT ="";

$id = isset($_GET['id']) && is_valid_id($_GET['id']) ? $_GET['id'] : 0;

$res = sql_query("SELECT * FROM betgames where id = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($res) < 1)
stderr($lang_bet['std_error'], $lang_bet['text_no_bet_id']);
$res = mysql_fetch_array($res);
$message = $res["heading"];


$res1 = sql_query("SELECT * FROM bets where gameid = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($res1) < 1)
stderr($lang_bet['std_error'], $lang_bet['text_no_bet_id']);
$bets = mysql_num_rows($res1);

$a = sql_query("SELECT * FROM `betlog` WHERE `msg` LIKE '%".$message."%'") or sqlerr(__FILE__, __LINE__);
//if(mysql_num_rows($a) < 1 || mysql_num_rows($a) > 1000)
if(mysql_num_rows($a) < 1 || mysql_num_rows($a) > 100000)
stderr($lang_bet['std_error'], $lang_bet['text_no_bet_log']);

$whoopsie = 0;

$log = mysql_num_rows($a);

if(isset($_GET["shite"]))

$shite = 1;
else
$shite = 0;

$res3 = sql_query("SELECT * FROM bets where gameid = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
$bets = mysql_num_rows($res3);
if($log != $bets && $shite == 0)
{
stderr($lang_bet['std_error'], "Number of operations and bonus logs entered did not match. ".htmlspecialchars($log). " vs ".htmlspecialchars($bets)." Contact the coder...<br />Fuck it... <a href='bet_nullbet.php?id=".$id."&amp;shite=1'><u>Do it anyway</u></a>");
}
else
{
$added = sqlesc(time());
$date = sqlesc(date("Y-m-d H:i:s"));
while($res3 = mysql_fetch_array($a))
	{
	$uid = (int) $res3['userid'];
	$s = strrpos($res3['msg'], "-");
	$points = substr($res3['msg'], $s);
	$s = strpos($points,"Points");
	$points = substr($points, 0, $s);	
	$HTMLOUT .="".$points." -> ";
	$HTMLOUT .="".$res3['msg']."<br />";	
	sql_query("UPDATE users SET seedbonus = seedbonus-".sqlesc($points)." WHERE id =".sqlesc($uid)." LIMIT 1") or sqlerr(__FILE__, __LINE__);	
	$subject = sqlesc("魔力值返还");
	$msg = sqlesc("您获得了 ".$message." 竞猜项目 ".$points." 魔力值投注的返还。该竞猜项目因意外错误或未能完成的比赛而被重置了，对此我们深表抱歉。");	
	sql_query("INSERT INTO messages (sender, receiver, added, msg, subject) VALUES(0, $uid, $date, $msg, $subject)") or sqlerr(__FILE__, __LINE__);
	$msg2 = sqlesc("竞猜项目及投注额: ".$message." <b>".$points." 魔力值</b>");
	sql_query("INSERT INTO betlog(userid,msg,date,bonus) VALUES($uid, $msg2, $added, $points)") or sqlerr(__FILE__, __LINE__);
  $whoopsie -= $points;
	}
sql_query("DELETE FROM betgames WHERE id =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
sql_query("DELETE FROM bets WHERE gameid = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
sql_query("DELETE FROM bets WHERE id = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
sql_query("DELETE FROM betoptions WHERE gameid = ".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
sql_query("DELETE FROM betlog WHERE msg LIKE '%".$message."%'") or sqlerr(__FILE__, __LINE__);
print stdhead("返还魔力值", false) . $HTMLOUT . stdfoot();
}

?>
