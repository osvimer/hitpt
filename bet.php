<?php
require "include/bittorrent.php"; 
dbconn();
loggedinorreturn();
require_once(get_langfile_path());

$HTMLOUT ="";

$HTMLOUT .="<img src='{$TBDEV['pic_base_url']}betting.png' alt='Bet' title='Betting' width='400' height='125' />
<table class='main' width='40%' cellspacing='0' cellpadding='5' border='0'>
<tr>
<td align='center' class='navigation'><a href='{$TBDEV['baseurl']}/bet.php'><font color='#999999'>".$lang_bet['text_cur_bets']."</font></a></td>";
if( $CURUSER['class'] >= UC_MODERATOR )
{
$HTMLOUT .= "<td align='center' class='navigation'><a href='{$TBDEV['baseurl']}/bet_admin.php'>".$lang_bet['text_bet_admin']."</a></td>";
}
$HTMLOUT .="<td align='center' class='navigation'><a href='{$TBDEV['baseurl']}/bet_coupon.php'>".$lang_bet['text_wagers']."</a></td>
<td align='center' class='navigation'><a href='{$TBDEV['baseurl']}/bet_bonustop.php'>".$lang_bet['text_top_list']."</a></td>
<td align='center' class='navigation'><a href='{$TBDEV['baseurl']}/bet_info.php'>".$lang_bet['text_bet_info']."</a></td>
</tr></table><br />";

$tid = time();

sql_query("UPDATE betgames set active = 0 WHERE endtime < $tid") or sqlerr(__FILE__, __LINE__);

$res = sql_query("SELECT * FROM betgames WHERE active = 1 ORDER BY endtime ASC") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($res) == 0)
{
$HTMLOUT .= $lang_bet['text_no_bet'];
}


while($a = mysql_fetch_assoc($res))
{
if($a['sort']==0)
$sort = "odds ASC";
elseif($a['sort']==1)
$sort = "id ASC";

$res2 = sql_query("SELECT * from betoptions where gameid =".sqlesc($a["id"])." ORDER BY $sort") or sqlerr(__FILE__, __LINE__);
$HTMLOUT .= "<table width='40%' cellpadding='5'>
<tr>
<td colspan='3' class='colhead'>".htmlspecialchars($a["heading"])."<br />".htmlspecialchars($a["undertext"])."";
$HTMLOUT .= "</td></tr>";

while($b = mysql_fetch_assoc($res2))
{
$odds = $b['odds'];

switch(strlen($odds))
{
case 1:
$odds = $odds.".00";
break;
case 3:
$odds = $odds."0";
break;
}

$HTMLOUT .="<tr><td class='clear' width='40%'><a href='{$TBDEV['baseurl']}/bet_odds.php?id=".$b['id']."'>".htmlspecialchars($b['text'])."</a></td><td class='clear'><a href='{$TBDEV['baseurl']}/bet_odds.php?id=".$b['id']."'>".htmlspecialchars($odds)."</a></td></tr>";
}
$HTMLOUT .="<tr><td class='clear' width='40%'><font size='1' face='微软雅黑'>".$lang_bet['text_endtime'].": <b>". date('Y-m-d H:i:s', $a['endtime']) ."</b>&nbsp;<br />".$lang_bet['text_lefttime'].": <b>".mkprettytime(($a['endtime']) - time())."</b></font></td></tr>";
$HTMLOUT .="</table>";
}

stdhead($lang_bet['head_bet']);
print  $HTMLOUT ; stdfoot();
?>
