<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
require_once(get_langfile_path("bet.php"));

$HTMLOUT ="";
$HTMLOUT .="<img src='{$TBDEV['pic_base_url']}betting.png' alt='Bet' title='Betting' width='400' height='125' />
<table class='main' width='200' cellspacing='0' cellpadding='5' border='0'>
<tr>
<td align='center' class='navigation'><a href='./bet.php'>".$lang_bet['text_bet_index']."</a></td>
<td align='center' class='navigation'><a href='./bet_coupon.php'>".$lang_bet['text_wagers']."</a></td>
<td align='center' class='navigation'><a href='./bet_bonustop.php'>".$lang_bet['text_top_list']."</a></td>
<td align='center' class='navigation'><a href='./bet_info.php'><font color='#999999'>".$lang_bet['text_bet_info']."</font></a></td>
</tr>
</table>
<br />
<table class='main' width='500' border='0' cellspacing='0' cellpadding='0'><tr><td class='embedded'>
<table width='100%' border='1' cellspacing='0' cellpadding='10'>
<tr><td class='text'>
<b>".$lang_bet['row_bet_info']."</b><br />
<br />".$lang_bet['text_bet_info_detail']."
<br />
</td></tr></table>
</td></tr></table>";

stdhead($lang_bet['head_bet']);print  $HTMLOUT ; stdfoot();
?>
