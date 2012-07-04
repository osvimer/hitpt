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
<td align='center' class='navigation'><a href='./bet_gameinfo.php'>".$lang_bet['text_all_bet_info']."</a></td>
<td align='center' class='navigation'><a href='./bet_gamefinish.php'><font color='#393'>".$lang_bet['text_end_bet']."</font></a></td>
</tr>
</table>
<br />";

$HTMLOUT .="<h1>".$lang_bet['text_warning']."<br /><br />".$lang_bet['text_click_win']."<br /></h1>";

$end = time();
$active = sql_query("SELECT * FROM betgames where active = 0 AND endtime <".sqlesc($end)."") or sqlerr(__FILE__, __LINE__);
while($active1 = mysql_fetch_assoc($active))
{
$HTMLOUT .="<br /><br /><font size=\"3\" >".$lang_bet['text_bet_title'].":   <b><u>".htmlspecialchars($active1['heading'])."</u></b><br />".$lang_bet['text_bet_on'].":   <b><u>".htmlspecialchars($active1['undertext'])."</u></b></font>";

$a = sql_query("SELECT * FROM betoptions where gameid =".sqlesc($active1["id"])." ORDER BY id ASC") or sqlerr(__FILE__, __LINE__);
while($b = mysql_fetch_assoc($a))
{
$HTMLOUT .="<br /><br /><a href='bet_gamefinish2.php?id=".$b['id']."'><font size=\"3\" >".htmlspecialchars($b['text'])."</a>   (赔率：".htmlspecialchars($b['odds']).")</font>";
}
}
stdhead($lang_bet['head_bet']);print  $HTMLOUT ; stdfoot();
?>
