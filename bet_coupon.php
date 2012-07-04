<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
require_once(get_langfile_path("bet.php"));

if ($CURUSER["class"] < UC_USER)
stderr($lang_bet['std_error'], $lang_bet['text_bet_request']);

$HTMLOUT ="";
$HTMLOUT .="<img src='{$TBDEV['pic_base_url']}betting.png' alt='Bet' title='Betting' width='400' height='125' />
<table class='main' width='200' cellspacing='0' cellpadding='5' border='0'>
<tr>
<td align='center' class='navigation'><a href='./bet.php'>".$lang_bet['text_bet_index']."</a></td>
<td align='center' class='navigation'><a href='./bet_coupon.php'><font color='#393'>".$lang_bet['text_wagers']."</font></a></td>
<td align='center' class='navigation'><a href='./bet_bonustop.php'>".$lang_bet['text_top_list']."</a></td>
<td align='center' class='navigation'><a href='./bet_info.php'>".$lang_bet['text_bet_info']."</a></td>
</tr>
</table>
<br />";

$main = sql_query("SELECT * FROM bets WHERE userid = ".sqlesc($CURUSER['id'])."") or sqlerr(__FILE__, __LINE__);
if(mysql_num_rows($main) == 0)
{
$HTMLOUT .=$lang_bet['text_no_active_game'];
}

while($more = mysql_fetch_assoc($main))
{
$id = $more['optionid'];

$res = sql_query("SELECT * FROM betoptions WHERE id =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
$a = mysql_fetch_array($res);
$res2 = sql_query("SELECT * from betgames where id = ".sqlesc($a['gameid'])."") or sqlerr(__FILE__, __LINE__);
$b = mysql_fetch_array($res2);

$HTMLOUT .="<table cellpadding='5'>
<tr>
<td colspan='1' class='colhead' width='200'>".$lang_bet['text_bet_title']."</td>
<td colspan='1' class='colhead' width='200'>".$lang_bet['text_bet_on']."</td>
<td colspan='1' class='colhead' width='100'>".$lang_bet['text_option']."</td>
<td colspan='1' class='colhead'>".$lang_bet['text_odds']."</td>
</tr>";

$odds = $a['odds'];

switch(strlen($odds))
{
case 1:
$odds = $odds.".00";
break;
case 3:
$odds = $odds."0";
break;
}

$HTMLOUT .="<tr>
<td>{$b['heading']}</td>
<td>{$b['undertext']}</td>
<td>{$a['text']}</td>
<td>{$odds}</td>
</tr>
<tr><td class='clear'>".$lang_bet['text_bet_amount']."</td><td class='clear' align='right'>{$more['bonus']} ".$lang_bet['text_points']."</td></tr>
<tr><td class='clear'>".$lang_bet['text_potential_pay']."</td><td class='clear' align='right'><b>".round(($more['bonus']*$a['odds'])*0.97)." ".$lang_bet['text_points']."</b></td></tr>
</table>";
}

stdhead($lang_bet['head_bet']);print  $HTMLOUT ; stdfoot();
?>
