<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
require_once(get_langfile_path("bet.php"));

if ($CURUSER["class"] < UC_MODERATOR)
	stderr($lang_bet['std_error'], $lang_bet['text_permission_denied']);
    
$HTMLOUT ="";
$HTMLOUT .="<img src='{$TBDEV['pic_base_url']}betting.png' alt='Bet' title='Betting' width='400' height='125' />
<h1>".$lang_bet['text_bet_admin']."</h1>
<table class='main' width='200' cellspacing='0' cellpadding='5' border='0'>
<tr>
<td align='center' class='navigation'><a href='./bet.php'>".$lang_bet['text_bet_index']."</a></td>
<td align='center' class='navigation'><a href='./bet_admin.php'>".$lang_bet['text_add_bets']."</a></td>
<td align='center' class='navigation'><a href='./bet_gameinfo.php'><font color='#393'>".$lang_bet['text_all_bet_info']."</font></a></td>
<td align='center' class='navigation'><a href='./bet_gamefinish.php'>".$lang_bet['text_end_bet']."</a></td>
</tr>
</table>
<br />";

$a = sql_query("SELECT * FROM betgames order by id ASC") or sqlerr(__FILE__, __LINE__);
while($b = mysql_fetch_array($a)){
$HTMLOUT .="<table cellpadding='2'>
<tr>
<td class='colhead'><a href='bet_gameinfo.php?showgames=".$b['id']."'>".
($_GET['showgames'] == $b['id'] ? "<font color='white'>" : "<font color='black'>")
 .htmlspecialchars($b['heading'])."</font></a></td></tr></table><br />";
}

if(isset($_GET['showgames'])){
$gameid = $_GET['showgames'];
$total = 0;
$totalbonus = 0;
$a = sql_query("SELECT * FROM bets WHERE gameid =".sqlesc($gameid)." ORDER BY date DESC") or sqlerr(__FILE__, __LINE__);
$HTMLOUT .="<table cellpadding='2'>
<tr>
<td colspan='1' class='colhead' width='200'>日期</td>
<td colspan='1' class='colhead' width='200'>用户名</td>
<td colspan='1' class='colhead' width='150'>竞猜选项</td>
<td colspan='1' class='colhead' width='100'>投注额</td></tr>";

$bet_options = sql_query("SELECT * FROM betoptions WHERE gameid=".sqlesc($gameid)) or sqlerr(__FILE__, __LINE__);
while($bet_option = mysql_fetch_array($bet_options))
{
    $option_text["".$bet_option['id']] = $bet_option['text'];
    $option_cnt["".$bet_option['id']] = 0;
    $option_bonus["".$bet_option['id']] = 0;
}

while($b = mysql_fetch_array($a))
{
$HTMLOUT .="<tr><td>" . date('Y-m-d H:i:s', $b['date']) . "</td>";
$HTMLOUT .="<td>".get_username($b['userid'])."</td>";
$HTMLOUT .="<td>".htmlspecialchars($option_text["".$b['optionid']])."</td>";
$HTMLOUT .="<td>".htmlspecialchars($b['bonus'])."</td></tr>";
$total++;
$totalbonus += $b['bonus'];
$option_cnt["".$b['optionid']]++;
$option_bonus["".$b['optionid']] += $b['bonus'];
}

$bet_options = sql_query("SELECT * FROM betoptions WHERE gameid=".sqlesc($gameid)) or sqlerr(__FILE__, __LINE__);
$option_num = mysql_num_rows($bet_options);
while($bet_option = mysql_fetch_array($bet_options))
{
$option_id = "".$bet_option['id'];
$HTMLOUT .="<tr><td>--- 小计 ---</td>";
$HTMLOUT .="<td>共 ".htmlspecialchars($option_cnt[$option_id])." 人次</td>";
$HTMLOUT .="<td>".htmlspecialchars($option_text[$option_id])."</td>";
$HTMLOUT .="<td>共 ".htmlspecialchars($option_bonus[$option_id])."</td></tr>";
}
$HTMLOUT .="<tr><td>--- 总计 ---</td>";
$HTMLOUT .="<td>共 ".htmlspecialchars($total)." 人次</td>";
$HTMLOUT .="<td>共 ".htmlspecialchars($option_num)." 种选项</td>";
$HTMLOUT .="<td>共 ".htmlspecialchars($totalbonus)."</td></tr>";

$HTMLOUT .="</table>";
}
stdhead($lang_bet['head_bet']);print  $HTMLOUT ; stdfoot();
?>
