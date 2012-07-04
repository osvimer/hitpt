<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
require_once(get_langfile_path("bet.php"));
require_once("bet_endtime.php");

if ($CURUSER["class"] < UC_MODERATOR)
	stderr($lang_bet['std_error'], $lang_bet['text_permission_denied']);

$HTMLOUT ="";
$HTMLOUT .= "<script type=\"text/javascript\" src=\"bet.js\"></script>";

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

$id = isset($_GET['id']) && is_valid_id($_GET['id']) ? $_GET['id'] : 0;

$a = sql_query("SELECT * FROM betgames where id =".sqlesc($id)."") or sqlerr(__FILE__, __LINE__);
$b = mysql_fetch_array($a);

$HTMLOUT .="<form method='post' action='bet_takeedit.php'>
<table cellpadding='5'>
<tr>
<td><input name='id' type='hidden' value='".htmlspecialchars($id)."' />
".$lang_bet['text_bet_title']."</td><td><input type='text' name='heading' size='50' value='".htmlspecialchars($b['heading'])."' />
</td>
</tr>
<tr>
<td>".$lang_bet['text_bet_on']."</td><td><input type='text' name='undertext' size='50' value='".htmlspecialchars($b['undertext'])."' /></td>
</tr>
<tr>
<td>".$lang_bet['text_endtime']."</td>
<td>";

$HTMLOUT .= sprint_endtime();
$date = date('Y-m-d');
$pattern = '/(\d+)-(\d+)-(\d+)/';
preg_match($pattern, $date, $matches);
$year = $matches[1];
$month = $matches[2];
$day = $matches[3];

$HTMLOUT .= "</td></tr>
<tr>
<td>".$lang_bet['text_order']."</td> <td><input type='radio' name='sort' value='1' checked='checked' />".$lang_bet['text_order_byid']."<input type='radio' name='sort' value='0' />".$lang_bet['text_order_byodd']."</td></tr>
</table><br />
<input type='submit' value='".$lang_bet['text_save_changes']."' onclick=\"javascript:get_endtime('".$year."','".$month."','".time()."');\" />
</form>
<br /><br />".$lang_bet['text_click']." <a href='bet_delgame.php?id=".$b['0']."'><u>".$lang_bet['text_here']."</u></a> ".$lang_bet['text_del_this_bet']."<br /><br />".$lang_bet['text_click']." <a href='bet_nullbet.php?id=".$b['0']."'><u>".$lang_bet['text_here']."</u></a> ".$lang_bet['text_del_and_payback'];
stdhead($lang_bet['head_bet']);print  $HTMLOUT ; stdfoot();
?>
