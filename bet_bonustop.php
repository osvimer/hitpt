<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
require_once(get_langfile_path("bet.php")); 

$HTMLOUT ="";

$order = isset($_GET['a']) && is_valid_id($_GET['a']) ? $_GET['a'] : 0;

if($order == 1)

$order = 'asc';
else
$order = 'desc';


$HTMLOUT .="<img src='{$TBDEV['pic_base_url']}betting.png' alt='Bet' title='Betting' width='400' height='125' />
<table class='main' width='50%' cellspacing='0' cellpadding='5' border='0'>
<tr>
<td align='center' class='navigation'><a href='./bet.php'>".$lang_bet['text_bet_index']."</a></td>
<td align='center' class='navigation'><a href='./bet_coupon.php'>".$lang_bet['text_wagers']."</a></td>
<td align='center' class='navigation'><a href='./bet_bonustop.php'><font color='#393'>".$lang_bet['text_top_list']."</font></a></td>
<td align='center' class='navigation'><a href='./bet_info.php'>".$lang_bet['text_bet_info']."</a></td>
</tr>
</table>
<br />";

$res = sql_query("SELECT * FROM bettop WHERE userid = ".sqlesc($CURUSER['id'])."") or sqlerr(__FILE__, __LINE__);

while($arr = mysql_fetch_assoc($res))
{
$HTMLOUT .="<table border='1' cellspacing='0' cellpadding='5'>\n";
$HTMLOUT .="<tr><td class='colhead' align='center'>".$lang_bet['text_user']."</td><td class='colhead' align='center'>".$lang_bet['text_points']."</td></tr>\n";
$HTMLOUT .="<tr><td><a href='userdetails.php?id=$CURUSER[id]'>".htmlspecialchars($CURUSER["username"])."</a></td><td align='right'><b>".htmlspecialchars($arr["bonus"])." ".$lang_bet['text_points']."</b></td></tr></table>\n";
}

  $number = 0;
  $res = sql_query("SELECT users.username, bettop.userid, bettop.bonus FROM bettop INNER JOIN users ON bettop.userid = users.id order by bettop.bonus $order limit 50") or sqlerr(__FILE__, __LINE__);
  $HTMLOUT.="<h1>".$lang_bet['text_top_list']."</h1>\n";

if($order == "desc")
$HTMLOUT .= "<h2><font color='white'>".$lang_bet['text_winner']."</font> - <a href='bet_bonustop.php?a=1'>".$lang_bet['text_loser']."</a></h2>";
else
$HTMLOUT .= "<h2><a href='bet_bonustop.php?a=2'>".$lang_bet['text_winner']."</a>   -   <font color='white'>".$lang_bet['text_loser']."</font></h2>";

    $HTMLOUT .="<table border='1' cellspacing='0' cellpadding='5'>\n";
    $HTMLOUT .="<tr><td class='colhead' align='left'>".$lang_bet['text_position']."</td><td class='colhead' align='left'>".$lang_bet['text_user']."</td><td class='colhead' align='left'>".$lang_bet['text_points']."</td></tr>\n";
    while ($arr = mysql_fetch_assoc($res))
    {
    $number++;
    $HTMLOUT .="<tr><td align='center'>".htmlspecialchars($number)."</td><td><a href='userdetails.php?id=$arr[userid]'>".htmlspecialchars($arr["username"])."</a></td><td align='right'><b>".htmlspecialchars($arr["bonus"])." ".$lang_bet['text_points']."</b></td></tr>\n";
    }
    $HTMLOUT .="</table>";
  
stdhead($lang_bet['head_bet']);print  $HTMLOUT ; stdfoot();
?>
