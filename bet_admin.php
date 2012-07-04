<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
require_once(get_langfile_path("bet.php"));
require_once("bet_endtime.php");

if ($CURUSER['class'] < UC_MODERATOR)
    stderr($lang_bet['std_error'], $lang_bet['text_permission_denied']);

$HTMLOUT ="";
$HTMLOUT .= "<script type=\"text/javascript\" src=\"bet.js\"></script>";

$HTMLOUT .="<img src='{$TBDEV['pic_base_url']}betting.png' alt='Bet' title='Betting' width='400' height='125' />
<h1>".$lang_bet['text_bet_admin']."</h1>
<table align='center' class='main' width='200' cellspacing='0' cellpadding='5' border='0'>
<tr>
<td align='center' class='navigation'><a href='./bet.php'>".$lang_bet['text_bet_index']."</a></td>
<td align='center' class='navigation'><a href='./bet_gameinfo.php'>".$lang_bet['text_all_bet_info']."</a></td>
<td align='center' class='navigation'><a href='./bet_gamefinish.php'>".$lang_bet['text_end_bet']."</a></td>
</tr></table><br />";


$HTMLOUT .="<form method='post' action='bet_takenew.php'>
<table align='center' cellpadding='5'>
<tr><td>".$lang_bet['text_bet_title']."</td><td><input type='text' name='heading' size='50' /></td></tr>
<tr><td>".$lang_bet['text_bet_on']."</td><td><input type='text' name='undertext' size='50' /></td></tr>
<tr>
<td>".$lang_bet['text_endtime']." 
</td><td>";

$HTMLOUT .= sprint_endtime();
$date = date('Y-m-d');
$pattern = '/(\d+)-(\d+)-(\d+)/';
preg_match($pattern, $date, $matches);
$year = $matches[1];
$month = $matches[2];
$day = $matches[3];

$HTMLOUT .= "</td></tr>
<tr>
<td>".$lang_bet['text_order']."</td><td>
<input type='radio' name='sort' value='1' checked='checked' />".$lang_bet['text_order_byid']."&nbsp;&nbsp;<input type='radio' name='sort' value='0' />".$lang_bet['text_order_byodd']."</td></tr>
<tr><td colspan='2' align='center'>
<input type='submit' value='提交' onclick=\"javascript:get_endtime('".$year."','".$month."','".time()."');\" />
</td></tr></table></form>";

$HTMLOUT .="<br /><br />
<table align='center' cellpadding='5'>
<tr>
<td><b>".$lang_bet['text_creator']."</b></td>
<td><b>".$lang_bet['text_endtime']."</b></td>
<td><b>".$lang_bet['text_bet_title']."</b></td>
<td><b>".$lang_bet['text_bet_on']."</b></td>
<td><b>".$lang_bet['text_set_active']."</b></td>
<td><b>".$lang_bet['text_add_options']."</b></td>
<td><b>".$lang_bet['text_edit']."</b></td>
</tr>";
 
$a = sql_query("SELECT *, endtime as end FROM betgames order by endtime ASC") or sqlerr(__FILE__, __LINE__);
while($b = mysql_fetch_array($a))
{
$HTMLOUT .="<tr><td align='left'>".htmlspecialchars($b['6'])."</td>";
if (time() > $b["end"])
$HTMLOUT .="<td align='center'><i>".date('Y-m-d H:i:s', htmlspecialchars($b['3']))."</i></td>";
else
$HTMLOUT .="<td align='center'>".date('Y-m-d H:i:s', htmlspecialchars($b['3']))."</td>";
$HTMLOUT .="<td align='center'>".htmlspecialchars($b['1'])."</td>";
$HTMLOUT .="<td align='center'>".htmlspecialchars($b['undertext'])."</td>";
if (time() > $b["end"])
$HTMLOUT.="<td align='center'>".$lang_bet['text_invalid']."</td>";
else
$HTMLOUT .="<td align='center'><a href='./bet_active.php?id=".$b['0']."'><u>".($b['active'] ? $lang_bet['text_valid'] : $lang_bet['text_invalid'])."</u></a></td>";
$HTMLOUT .="<td align='center'><a href='./bet_opt.php?id=".$b['0']."'><u>".$lang_bet['text_add_options']."</u></a></td>";
$HTMLOUT .="<td align='center'><a href='./bet_opt2.php?id=".$b['0']."'><u>".$lang_bet['text_edit']."</u></a></td></tr>";

}
$HTMLOUT .="</table><br /><br />\n";


stdhead($lang_bet['head_bet']);print  $HTMLOUT ; stdfoot();
?>
