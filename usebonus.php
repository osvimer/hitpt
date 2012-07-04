<?php
require "include/bittorrent.php";
require "memcache.php";
dbconn();
loggedinorreturn();
require_once(get_langfile_path());
parked();

function bonusarray($option){
	global $onegbupload_bonus,$fivegbupload_bonus,$tengbupload_bonus,$oneinvite_bonus,$customtitle_bonus,$vipstatus_bonus, $basictax_bonus, $taxpercentage_bonus, $bonusnoadpoint_advertisement, $bonusnoadtime_advertisement;
	global $lang_mybonus;
	$bonus = array();
	switch ($option)
	{
		case 1: {//碰运气
			$bonus['points'] = 25;
			$bonus['art'] = 'luck';
			$bonus['menge'] = 0;
			$bonus['name'] = $lang_mybonus['text_luck'];
			$bonus['description'] = $lang_mybonus['text_luck_note'];
			break;
			}
		default: break;
	}
	return $bonus;
}

$action = htmlspecialchars($_GET['action']);

stdhead($CURUSER['username'] . $lang_mybonus['head_karma_page']);

	$bonus = number_format((int)$CURUSER['seedbonus'], 0);
if (!$action) {
	print("<table align=\"center\" width=\"940\" border=\"1\" cellspacing=\"0\" cellpadding=\"3\">\n");
	print("<tr><td class=\"colhead\" colspan=\"4\" align=\"center\"><font class=\"big\">".$SITENAME.$lang_mybonus['text_karma_system']."</font></td></tr>\n");
?>
<tr><td class="text" align="center" colspan="4"><?php echo $lang_mybonus['text_exchange_your_karma']?><?php echo $bonus?><?php echo $lang_mybonus['text_for_goodies'] ?>
<br /><b><?php echo $lang_mybonus['text_no_buttons_note'] ?></b></td></tr>
<?php

print("<tr><td class=\"colhead\" align=\"center\">".$lang_mybonus['col_option']."</td>".
"<td class=\"colhead\" align=\"left\">".$lang_mybonus['col_description']."</td>".
"<td class=\"colhead\" align=\"center\">".$lang_mybonus['col_points']."</td>".
"<td class=\"colhead\" align=\"center\">".$lang_mybonus['col_trade']."</td>".
"</tr>");
for ($i=1; $i <=2; $i++)
{
	$bonusarray = bonusarray($i);
	print("<tr>");
	print("<form action=\"?action=exchange\" method=\"post\">");
	print("<td class=\"rowhead_center\"><input type=\"hidden\" name=\"option\" value=\"".$i."\" /><b>".$i."</b></td>");
	if ($i==1){  //碰运气
			$otheroption = "<table width=\"100%\"><tr><td class=\"embedded\"><b>".$lang_mybonus['text_to_be_play']."</b><input type=\"text\" name=\"luckbonus\" id=\"luckbonus\" style='width: 80px' />".$lang_mybonus['text_karma_points']."</td></tr></table>";
			print("<td class=\"rowfollow\" align='left'><h1>".$bonusarray['name']."</h1>".$bonusarray['description']."$otheroption</td><td class=\"rowfollow nowrap\" align='center'>".$lang_mybonus['text_min']."25<br />".$lang_mybonus['text_max']."1,000</td>");
	}
	if ($i==2){  //21点
			$otheroption = "<table width=\"100%\"><tr><td class=\"embedded\"><b><a href=play21.php>".$lang_mybonus['text_play21']."</a></b></td></tr></table>";
			print("<td class=\"rowfollow\" align='left'><h1>".$otheroption."</h1></td><td></td>");
	}

	if($CURUSER['seedbonus'] >= $bonusarray['points'])
	{
		if ($i==1){
			if($memcache->get('app_luck_'.$CURUSER['id'])!='')
			print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" disabled=\"true\" value=\"".$lang_mybonus['submit_karma_luck']."\" /><br />上次时间：<br />".$memcache->get('app_luck_'.$CURUSER['id'])."</td>");
			else
			print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".$lang_mybonus['submit_karma_luck']."\" /></td>");
		}
		elseif ($i==2){
			print("<td class=\"rowfollow\" align=\"center\"></td>");
		}
		else
		{
			if ($CURUSER['downloaded'] > 0){
				if ($CURUSER['uploaded'] > $dlamountlimit_bonus * 1073741824)//Uploaded amount reach limit
					$ratio = $CURUSER['uploaded']/$CURUSER['downloaded'];
				else $ratio = 0;
			}
			else $ratio = $ratiolimit_bonus + 1; //Ratio always above limit
			if ($ratiolimit_bonus > 0 && $ratio > $ratiolimit_bonus){
				print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".$lang_mybonus['text_ratio_too_high']."\" disabled=\"disabled\" /></td>");
			}
			else print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".$lang_mybonus['submit_exchange']."\" /></td>");
		}
	}
	else
	{
		print("<td class=\"rowfollow\" align=\"center\"><input type=\"submit\" name=\"submit\" value=\"".$lang_mybonus['text_more_points_needed']."\" disabled=\"disabled\" /></td>");
	}
	print("</form>");
	print("</tr>");
	
}
print("</table><br />");
}

if($action == "viewluck"){
	$lucklog = array();
	$lucklog = json_decode($memcache->get('app_luck_log'));
	$lucklog = array_reverse($lucklog);
		print("<table width=100%>");
		print("<tr><td height=30>");
		print("显示近30条玩家游戏记录");
		print("</tr></td>");
	foreach ($lucklog as $log){
		print("<tr><td height=30>");
		print($log."<br />");
		print("</tr></td>");
	}
		print("</table>");
}

// Bonus exchange
if ($action == "exchange") {
	if ($_POST["userid"] || $_POST["points"] || $_POST["bonus"] || $_POST["art"]){
		write_log("User " . $CURUSER["username"] . "," . $CURUSER["ip"] . " is trying to cheat at bonus system",'mod');
		die($lang_mybonus['text_cheat_alert']);
	}
	$option = (int)$_POST["option"];
	$bonusarray = bonusarray($option);

	$points = $bonusarray['points'];
	$userid = $CURUSER['id'];
	$art = $bonusarray['art'];

	$bonuscomment = $CURUSER['bonuscomment'];
	$seedbonus=$CURUSER['seedbonus']-$points;

	if($CURUSER['seedbonus'] >= $points) {
		//=== trade for upload
		if($art == "luck") {
	if($memcache->get('app_luck_'.$CURUSER['id'])!=''){
				stdmsg($lang_mybonus['text_error'], $lang_mybonus['text_cheat_alert']);
				stdfoot();
				die();
	}
			$luckbonus=0+$_POST['luckbonus'];
			if ($luckbonus < 25 || $luckbonus > 1000) {
				stdmsg($lang_mybonus['text_error'], $lang_mybonus['bonus_amount_not_allowed']);
				stdfoot();
				die();
			}
			$retluckbonus = mt_rand(1,$luckbonus*2);
			$sqlluckbonus = $retluckbonus - $luckbonus;
			sql_query("UPDATE users SET seedbonus = seedbonus + $sqlluckbonus WHERE id = ".sqlesc($userid)) or sqlerr(__FILE__, __LINE__);
		if($sqlluckbonus > 0)
			$retinfo = "恭喜".$CURUSER['username']."<b><font color=red>获得了".$sqlluckbonus."个</font></b>魔力值";
		elseif($sqlluckbonus == 0)
			$retinfo = $CURUSER['username']."即没有得到也没有失去魔力值";
		else
			$retinfo = "很遗憾，".$CURUSER['username']."<b><font color=green>失去了".abs($sqlluckbonus)."个</font></b>魔力值";
			$message = $CURUSER['username']."使用了：".$luckbonus."个魔力值，获得了：".$retluckbonus.$lang_mybonus['text_point']."，".$retinfo;
			stdmsg($lang_mybonus['text_success'], $message);
			$date = date("H:i:s");
			$memcache->set('app_luck_'.$CURUSER['id'],$date,false,600) or die ("");
			//碰运气记录开始
				$lucklog = json_decode($memcache->get('app_luck_log'));
				$lucklog[] = $date." ".$message;
if(count($lucklog))
	$lucklog = array_slice($lucklog,-30);
				$memcache->set('app_luck_log',json_encode($lucklog),false,3600);
			//碰运气记录结束
			stdfoot();
			die();
		}
	}
}
stdfoot();
?>
