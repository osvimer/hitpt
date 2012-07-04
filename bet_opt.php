<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
require_once(get_langfile_path("bet.php")); 

if ($CURUSER["class"] < UC_MODERATOR)
	stderr($lang_bet['std_error'], $lang_bet['text_permission_denied']);

$HTMLOUT ="";

$id = isset($_GET['id']) && is_valid_id($_GET['id']) ? $_GET['id'] : 0;

$HTMLOUT .="<img src='{$TBDEV['pic_base_url']}betting.png' alt='Bet' title='Betting' width='400' height='125' />
<h1>".$lang_bet['text_bet_admin']."</h1>
<table class='main' width='200' cellspacing='0' cellpadding='5' border='0'>
<tr>
<td align='center' class='navigation'><a href='./bet.php'>".$lang_bet['text_bet_index']."</a></td>
<td align='center' class='navigation'><a href='./bet_admin.php'>".$lang_bet['text_add_bets']."</a></td>
<td align='center' class='navigation'><a href='./bet_gameinfo.php'>".$lang_bet['text_all_bet_info']."</a></td>
<td align='center' class='navigation'><a href='./bet_gamefinish.php'>".$lang_bet['text_end_bet']."</a></td>
</tr>
</table>
<br />";

$HTMLOUT .="<h2>".$lang_bet['text_add_bet_options']."</h2><table cellpadding='5'>";

$a = sql_query("SELECT * FROM betgames WHERE id =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
while($b = mysql_fetch_array($a))
{

$HTMLOUT .="<tr><td>".htmlspecialchars($b['1'])."</td>";
$HTMLOUT .="<td><i>".htmlspecialchars($b['undertext'])."</i></td>";
$HTMLOUT .="</tr>";

}
$HTMLOUT .="</table><br />";
  $res = sql_query("SELECT id, gameid, text FROM betoptions WHERE gameid =".sqlesc($id)." ORDER BY id asc") or sqlerr(__FILE__, __LINE__);
    $HTMLOUT .="<table border='1' cellspacing='0' cellpadding='5'>\n";
    $HTMLOUT .="<tr>
    <td colspan='2' class='colhead' align='left'>".$lang_bet['text_option']."</td></tr>\n";
    while ($arr = mysql_fetch_array($res))
    {
     $HTMLOUT .="<tr><td>".htmlspecialchars($arr['text'])."</td><td><a href='./bet_delopt.php?id=$arr[id]&amp;b=$id'>".$lang_bet['text_del_option']."</a></td></tr>\n";
    }
    $HTMLOUT .="</table><br /><br/>";


$HTMLOUT .="<form action='bet_addopt.php' method='post'>
".$lang_bet['text_option'].": <input type='text' size='10' name='opt' />
<input type='hidden' name='id' value='".htmlspecialchars($id)."' />
<input type='submit' value='".$lang_bet['text_add_option_to_game']."' />
</form>
<br /><br />
<form action='bet_add1x2.php' method='post'>
</form>";
//<input type='hidden' name='id' value='".htmlspecialchars($id)."' />
//<input type='submit' value='快速添加 1, X, 2' />

stdhead($lang_bet['head_bet']);print  $HTMLOUT ; stdfoot();
?>
