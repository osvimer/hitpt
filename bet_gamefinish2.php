<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
require_once(get_langfile_path("bet.php"));
require_once(get_langfile_path("forums.php"));

$HTMLOUT ="";

$subject ="";

global $CURUSER, $TBDEV;

if ($CURUSER["class"] < UC_MODERATOR)
	stderr($lang_bet['std_error'], $lang_bet['text_permission_denied']);

function auto_bet($subject = "Error - Subject Missing", $body = "Error - No Body")
{
    global $CURUSER, $TBDEV;
    
    $forumid = 24;
    $subject = sqlesc($subject);
    $res = sql_query("SELECT id FROM topics WHERE forumid=$forumid AND subject=$subject");

    if (mysql_num_rows($res) >= 1)
    {
	$type = "old";
        $arr = mysql_fetch_array($res);
        $topicid = $arr['id'];
	sql_query("UPDATE forums SET postcount=postcount+1 WHERE id=".sqlesc($forumid));
    }
    else 
    {
	//---- Create topic
	$type = "new";
        sql_query("INSERT INTO topics (userid, forumid, subject) VALUES(".$TBDEV['bot_id'].", $forumid, $subject)") or sqlerr(__FILE__, __LINE__);
	$topicid = mysql_insert_id() or stderr($lang_forums['std_error'],$lang_forums['std_no_topic_id_returned']);
	sql_query("UPDATE forums SET topiccount=topiccount+1, postcount=postcount+1 WHERE id=".sqlesc($forumid));
    }

    $added = date("Y-m-d H:i:s");
    sql_query("INSERT INTO posts (topicid, userid, added, body, ori_body) VALUES($topicid, ".$TBDEV['bot_id'].", ".sqlesc($added).", $body, $body)") or sqlerr(__FILE__, __LINE__);
    $postid = mysql_insert_id() or die($lang_forums['std_post_id_not_available']);

    if ($type == 'new')
    {
	sql_query("UPDATE topics SET firstpost=$postid, lastpost=$postid WHERE id=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
    }
    else
    {
	sql_query("UPDATE topics SET lastpost=$postid WHERE id=".sqlesc($topicid)) or sqlerr(__FILE__, __LINE__);
    }
    sql_query("UPDATE users SET last_post=".sqlesc($added)." WHERE id=".$TBDEV['bot_id']) or sqlerr(__FILE__, __LINE__);
}



$res1 = mysql_fetch_array(sql_query("SELECT id FROM topics ORDER BY id DESC LIMIT 1"));
$res_id = 1 + $res1['id'];
$forumlink = "[url]forums.php?action=viewtopic&topicid=".$res_id."[/url]";
//==End

$date = time();
$id = isset($_GET['id']) && is_valid_id($_GET['id']) ? $_GET['id'] : 0;
$a = sql_query("SELECT * FROM betoptions WHERE id =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
$b = mysql_fetch_array($a);
$gameid = $b['gameid'];
if($gameid < 1){
header("location: {$TBDEV['baseurl']}/bet_gamefinish.php");
exit;
}
$res3 = sql_query("SELECT * FROM betgames WHERE id =".sqlesc($gameid)." AND fix = 0") or sqlerr(__FILE__, __LINE__);
$o = @mysql_fetch_array($res3);
$c = sql_query("SELECT * FROM bets WHERE optionid =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);

if(@mysql_num_rows($res3) == 1)
{
sql_query("UPDATE betgames SET fix = 1 WHERE id =".sqlesc($gameid)."") or sqlerr(__FILE__, __LINE__);
}
else
{
print stdhead($lang_bet['head_bet']);

$HTMLOUT .="<img src='{$TBDEV['pic_base_url']}betting.png' alt='Bet' title='Betting' width='400' height='125' />
<table class='main' width='200' cellspacing='0' cellpadding='5' border='0'>
<tr>
<td align='center' class='navigation'><a href='./bet_admin.php'>".$lang_bet['text_add_bets']."</a></td>
<td align='center' class='navigation'><a href='./bet_gameinfo.php'>".$lang_bet['text_all_bet_info']."</a></td>
<td align='center' class='navigation'><a href='./bet_gamefinish.php'><font color='#393'>".$lang_bet['text_end_bet']."</font></a></td>
</tr>
</table>
<br />";
}
while($d = mysql_fetch_array($c))
{
$dividend = round(($d['bonus']*$b['odds'])*0.97,0);
if(mysql_num_rows(sql_query("SELECT * FROM bettop WHERE userid =".sqlesc($d["userid"])."")) == 0){
sql_query("INSERT INTO bettop(userid, bonus) VALUES(".sqlesc($d["userid"]).", ".sqlesc($dividend-$d["bonus"]).")") or sqlerr(__FILE__, __LINE__);
}
else{
sql_query("UPDATE bettop SET bonus = bonus + ".sqlesc($dividend -$d["bonus"])." WHERE userid =".sqlesc($d["userid"])."") or sqlerr(__FILE__, __LINE__);
}

$dividend = round(($d['bonus']*$b['odds'])*0.97,0);
$subjectwin = $lang_bet['text_bet_win'];
$msg = "Bet profit +".$dividend." points";
$msg2 = <<<EOD
祝贺你在有奖竞猜中获得了 {$dividend} 魔力值！
你在 [b]{$o['heading']} - {$o['undertext']}[/b] 的竞猜活动中，选择了 {$b['text']} ，赔率为 {$b['odds']} ，投注额为 {$d['bonus']} 魔力值。

 [em23] 

在下面的链接中可以查阅到本次有奖竞猜活动的详细结果:

{$forumlink}

EOD;

sql_query("UPDATE users set seedbonus = seedbonus + ".sqlesc($dividend)." WHERE id = ".sqlesc($d["userid"])."") or sqlerr(__FILE__, __LINE__);
sql_query("INSERT INTO betlog(userid,msg,date,bonus) VALUES(".sqlesc($d["userid"]).", ".sqlesc($msg).", '$date', ".sqlesc($dividend).")") or sqlerr(__FILE__, __LINE__);
sql_query("INSERT INTO messages (sender, receiver, msg, added, subject) VALUES(0,$d[userid], ".sqlesc($msg2).", ".sqlesc(date("Y-m-d H:i:s")).", ".sqlesc($subjectwin).")") or sqlerr(__FILE__, __LINE__);
}

$total_cnt_res = sql_query("SELECT COUNT(*) from bets where gameid =".sqlesc($gameid)."") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($total_cnt_res) < 1)
    $total_cnt = 0;
else
{
    $s = mysql_fetch_array($total_cnt_res);
    $total_cnt = $s[0];
}
$total_bonus_res = sql_query("SELECT SUM(bonus) from bets where gameid =".sqlesc($gameid)) or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($total_bonus_res) < 1)
    $total_bonus = 0;
else
{
    $s = mysql_fetch_array($total_bonus_res);
    $total_bonus = $s[0];
}

$body = "[size=3][b]".htmlspecialchars($o['heading'])."[/b] - ".htmlspecialchars($o['undertext'])."[/size]\n\n";
$body.= "本次有奖竞猜投注人数: [b] ".htmlspecialchars($total_cnt)."[/b]\n";
$body.= "投注总量: [b] ".htmlspecialchars($total_bonus)." ".$lang_bet['text_points']."[/b]\n";
$body.= "正确答案: [b] ".htmlspecialchars($b['text'])."[/b]\n";
$body.= "竞猜关闭人: [b] [url=http://pt.hit.edu.cn/userdetails.php?id=".$CURUSER["id"]."]".get_username($CURUSER["id"],false,false,false,false,false,false,"",false,true)."[/url][/b]\n\n";

$body.= "[b]".$lang_bet['text_option_and_odd']." :[/b]\n";
$res = sql_query("SELECT * FROM betgames WHERE id =".sqlesc($gameid)."") or sqlerr(__FILE__, __LINE__);
$a = mysql_fetch_array($res);
if($a['sort']==0)
$sort = "odds ASC";
elseif($a['sort']==1)
$sort = "id ASC";
$res2 = sql_query("SELECT * from betoptions where gameid =".sqlesc($a["id"])." ORDER BY $sort") or sqlerr(__FILE__, __LINE__);
while($b = mysql_fetch_array($res2)){
$body.= " ".htmlspecialchars($b['text'])."   赔率: [b]".htmlspecialchars($b['odds'])."[/b]\n";
}

// TOP 20 WINNER
$m = sql_query("SELECT userid, bonus FROM bets WHERE optionid =".sqlesc($id)." and gameid =".sqlesc($gameid)." order by bonus DESC LIMIT 20") or sqlerr(__FILE__, __LINE__);
$body.= "\n[b]Top 20 ".$lang_bet['text_winner']." :[/b]\n";
$odds = mysql_fetch_array(sql_query("SELECT * FROM betoptions WHERE id =".sqlesc($id)."")) or sqlerr(__FILE__, __LINE__);
while($k = mysql_fetch_array($m)){
$body .= "[b][url=http://pt.hit.edu.cn/userdetails.php?id=".$k["userid"]."]".get_username($k["userid"],false,false,false,false,false,false,"",false,true)."[/url] 奖励 ".round($k['bonus']*$odds['odds']*0.97,0)." ".$lang_bet['text_points']."，[/b] 投注 ".htmlspecialchars($k['bonus'])." ".$lang_bet['text_points']."\n";
}

//TOP 20 LOSER
$m = sql_query("SELECT userid, SUM(bonus) FROM bets WHERE optionid <> $id and gameid = ".sqlesc($gameid)." GROUP BY userid ORDER BY SUM(bonus) DESC LIMIT 20") or sqlerr(__FILE__, __LINE__);
$body.= "\n[b]Top 20 ".$lang_bet['text_loser']." :[/b]\n";
while($k = mysql_fetch_array($m)){
$body .= "[b][url=http://pt.hit.edu.cn/userdetails.php?id=".$k["userid"]."]".get_username($k["userid"],false,false,false,false,false,false,"",false,true)."[/url] 失去 ".htmlspecialchars($k[1])." ".$lang_bet['text_points']."[/b]\n";
}

$body = sqlesc($body);
auto_bet($lang_bet['text_result']." - ".$o['heading']." - ".$o['undertext'], $body);

$c = sql_query("SELECT * FROM bets WHERE optionid <> $id AND gameid =".sqlesc($gameid)."") or sqlerr(__FILE__, __LINE__);
while($a = mysql_fetch_array($c))
{
if(mysql_num_rows(sql_query("SELECT * FROM bettop WHERE userid =".sqlesc($a["userid"])."")) == 0){
sql_query("INSERT INTO bettop(userid, bonus) VALUES(".sqlesc($a["userid"]).", -".sqlesc($a["bonus"]).")") or sqlerr(__FILE__, __LINE__);
}
else{
sql_query("UPDATE bettop SET bonus = bonus - ".sqlesc($a["bonus"])." WHERE userid =".sqlesc($a["userid"])."") or sqlerr(__FILE__, __LINE__);
}
$k = mysql_fetch_array(sql_query("SELECT * from betgames where id =".sqlesc($gameid)."")) or sqlerr(__FILE__, __LINE__);
$msg2 = <<<EOD
很遗憾，你在 [b]{$k['heading']} - {$k['undertext']}[/b] 的竞猜活动中没有获奖。
谢谢你的积极参与，希望你下次好运！

 [em12] 

在下面的链接中可以查阅到本次有奖竞猜活动的详细结果:

{$forumlink}

EOD;

$subjectloss = $lang_bet['text_bet_lose'];
sql_query("INSERT INTO messages (sender, receiver, msg, added, subject) VALUES(0, ".sqlesc($a["userid"]).", ".sqlesc($msg2).", ".sqlesc(date("Y-m-d H:i:s")).", ".sqlesc($subjectloss).")") or sqlerr(__FILE__, __LINE__);
}

sql_query("DELETE FROM betgames WHERE id =".sqlesc($gameid)."") or sqlerr(__FILE__, __LINE__);
sql_query("DELETE FROM betoptions WHERE gameid =".sqlesc($gameid)."") or sqlerr(__FILE__, __LINE__);
sql_query("DELETE FROM bets WHERE gameid =".sqlesc($gameid)."") or sqlerr(__FILE__, __LINE__);
header("location: {$TBDEV['baseurl']}/bet_gamefinish.php");
stdhead($lang_bet['head_bet']); print  $HTMLOUT ; stdfoot();
?>
