<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
require_once(get_langfile_path("bet.php"));

$HTMLOUT ="";

if ($CURUSER["class"] < UC_USER)
{
    stderr($lang_bet['std_error'], $lang_bet['text_bet_request']);
}

$id = isset($_GET['id']) && is_valid_id($_GET['id']) ? $_GET['id'] : 0;

$res = sql_query("SELECT * FROM betoptions WHERE id =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
$a = mysql_fetch_assoc($res);

$sp = (int) $a['gameid'];

$res2 = sql_query("SELECT * from betgames where id =".sqlesc($sp)."") or sqlerr(__FILE__, __LINE__);
$b = mysql_fetch_assoc($res2);

if($b['active'] == 0){
header("location: {$TBDEV['baseurl']}/bet.php");
exit;
}
$HTMLOUT .="<img src='{$TBDEV['pic_base_url']}betting.png' alt='Softbet' title='Betting' width='400' height='125' />
<table class='main' width='200' cellspacing='0' cellpadding='5' border='0'>
<tr>
<td align='center' class='navigation'><a href='./bet.php'>".$lang_bet['text_bet_index']."</a></td>
<td align='center' class='navigation'><a href='./bet_coupon.php'>".$lang_bet['text_wagers']."</a></td>
<td align='center' class='navigation'><a href='./bet_info.php'>".$lang_bet['text_bet_info']."</a></td>
</tr>
</table>
<br />
<form method='post' action='bet_odds2.php'>
<table cellpadding='6'>
<tr>
<td  align='center' class='colhead' width='200'>".$lang_bet['text_bet_title']."</td>
<td  align='center' class='colhead' width='200'>".$lang_bet['text_bet_on']."</td>
<td  align='center' class='colhead' width='100'>".$lang_bet['text_my_option']."</td>
<td  align='center' class='colhead'>".$lang_bet['text_odds']."</td>
<td  align='center' class='colhead'>".$lang_bet['text_bet_amount']."</td>
<td  align='center' class='colhead'>&nbsp;</td>
</tr>
<tr>
<td>{$b['heading']}</td>
<td>{$b['undertext']}</td>
<td>{$a['text']}</td>
<td>{$a['odds']}</td>
<td>
<input type='text' name='bonus' size='7' maxlength='10' />  <b> ".$lang_bet['text_points']."</b>
<input type='hidden' name='id' value='".htmlspecialchars($id)."' /></td>
<td align='center'><input type='submit' value='".$lang_bet['text_take_bet']."' /><br />(".$lang_bet['text_take_bet_note'].")</td></tr></table></form>";

stdhead($lang_bet['head_bet']);print  $HTMLOUT ; stdfoot(); 
?>
