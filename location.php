<?php
ob_start();
require_once("include/bittorrent.php");
require_once(get_langfile_path());
dbconn();
loggedinorreturn();
if (get_user_class() < UC_SYSOP) {
	die("access denied.");
}
mysql_connect($mysql_host,$mysql_user,$mysql_pass);
mysql_select_db($mysql_db);
stdhead("Manage Locations");
begin_main_frame("",false,100);
begin_frame($lang_location['head_location'],true,10,"100%","center");

$sure = $_GET['sure'];
if($sure == "yes") {
	$delid = $_GET['delid'];
	$query = "DELETE FROM locations WHERE id=" .sqlesc($delid) . " LIMIT 1";
	$sql = sql_query($query);
	echo($lang_location['text_delete_done']."<a class=altlink href=" . $_SERVER['PHP_SELF'] .">".$lang_location['text_delete_here']."</a>".$lang_location['text_delete_back']);
	end_frame();
	stdfoot();
	die();
}
$delid = $_GET['delid'];
if($delid > 0) {
	echo($lang_location['text_delete_confirm']."( <strong><a href='". $_SERVER['PHP_SELF'] . "?delid=$delid&sure=yes'>".$lang_location['text_confirm_yes']."</a></strong> / <strong><a href='". $_SERVER['PHP_SELF'] . "'>".$lang_location['text_confirm_no']."</a></strong> )");
	end_frame();
	stdfoot();
	die();
}

$edited = $_GET['edited'];
if($edited == 1) {
	$id = 0 + $_GET['id'];
	$name = $_GET['name'];
	$flagpic = $_GET['flagpic'];
	$location_main = $_GET['location_main'];
	$location_sub = $_GET['location_sub'];
	$start_ip = $_GET['start_ip'];
	$end_ip = $_GET['end_ip'];
	$theory_upspeed = $_GET['theory_upspeed'];
	$practical_upspeed = $_GET['practical_upspeed'];
	$theory_downspeed = $_GET['theory_downspeed'];
	$practical_downspeed = $_GET['practical_downspeed'];
	
	if(validip_format($start_ip) && validip_format($end_ip))
	{
		if(ip2long($end_ip) > ip2long($start_ip))
		{
			$query = "UPDATE locations SET name = " . sqlesc($name) .",flagpic = " . sqlesc($flagpic) . ",location_main = " . sqlesc($location_main). ",location_sub= " . sqlesc($location_sub) . ",start_ip = " . sqlesc($start_ip) .  ",end_ip = " . sqlesc($end_ip) . ",theory_upspeed = " . sqlesc($theory_upspeed) .  ",practical_upspeed = " . sqlesc($practical_upspeed) .  ",theory_downspeed = " . sqlesc($theory_downspeed) .  ",practical_downspeed = " . sqlesc($practical_downspeed). " WHERE id=".sqlesc($id);
			$sql = sql_query($query) or sqlerr(__FILE__, __LINE__);
			if($sql) 
			{
				stdmsg("Success!","Location has been edited, click <a class=altlink href=" . $_SERVER['PHP_SELF'] .">here</a> to go back");
				stdfoot();
				die();
			}
		}
		else
			echo("<p><strong>".$lang_location['text_larger_ip']."</strong></p>");
	}
	else 
		echo("<p><strong>".$lang_location['text_invalid_ip']." </strong></p>");

}

$editid = 0 + $_GET['editid'];
if($editid > 0) {
	
	$query = "SELECT * FROM locations WHERE id=" . sqlesc($editid);
	$sql = sql_query($query);
	$row = mysql_fetch_array($sql);
	
	$name = $row['name'];
	$flagpic = $row['flagpic'];
	$location_main = $row['location_main'];
	$location_sub = $row['location_sub'];
	$start_ip = $row['start_ip'];
	$end_ip = $row['end_ip'];
	$theory_upspeed = $row['theory_upspeed'];
	$practical_upspeed = $row['practical_upspeed'];
	$theory_downspeed = $row['theory_downspeed'];
	$practical_downspeed = $row['practical_downspeed'];

	echo("<form name='form1' method='get' action='" . $_SERVER['PHP_SELF'] . "'>");
	echo("<input type='hidden' name='id' value='$editid'><table class=main cellspacing=0 cellpadding=5 width=50%>");
	echo("<tr><td class=colhead align=center colspan=2>Editing Locations</td><input type='hidden' name='edited' value='1'></tr>");
	echo("<tr><td class=rowhead>Name:</td><td class=rowfollow align=left><input type='text' size=10 name='name' value='$name'></td></tr>");
	echo("<tr><td class=rowhead><nobr>Main Location:</nobr></td><td class=rowfollow align=left><input type='text' size=50 name='location_main' value='$location_main'></td></tr>");
	echo("<tr><td class=rowhead><nobr>Sub Location:</nobr></td><td class=rowfollow align=left><input type='text' size=50 name='location_sub' value='$location_sub'></td></tr>");
	echo("<tr><td class=rowhead><nobr>Start IP:</nobr></td><td class=rowfollow align=left><input type='text' size=30 name='start_ip' value='" . $start_ip . "'></td></tr>");
	echo("<tr><td class=rowhead><nobr>End IP:</nobr></td><td class=rowfollow align=left><input type='text' size=30 name='end_ip' value='" . $end_ip. "'></td></tr>");
	echo("<tr><td class=rowhead><nobr>Theory Up:</nobr></td><td class=rowfollow align=left><input type='text' size=10 name='theory_upspeed' value='$theory_upspeed'></td></tr>");
	echo("<tr><td class=rowhead><nobr>Theory Down:</nobr></td><td class=rowfollow align=left><input type='text' size=10 name='theory_downspeed' value='$theory_downspeed'></td></tr>");
	echo("<tr><td class=rowhead><nobr>Practical Up:</nobr></td><td class=rowfollow align=left><input type='text' size=10 name='practical_upspeed' value='$practical_upspeed'></td></tr>");
	echo("<tr><td class=rowhead><nobr>Practical Down:</nobr></td><td class=rowfollow align=left><input type='text' size=10 name='practical_downspeed' value='$practical_downspeed'></td></tr>");
	echo("<tr><td class=rowhead>Picture:</td><td class=rowfollow align=left><input type='text' size=50 name='flagpic' value='$flagpic'></td></tr>");
	echo("<tr><td class=toolbox align=center colspan=2><input class=btn type='Submit'></td></tr>");
	echo("</table></form>");
	end_frame();
	stdfoot();
	die();
}

$add = $_GET['add'];
$success = false;
if($add == 'true') {
	$name = $_GET['name'];
	$flagpic = $_GET['flagpic'];
	$location_main = $_GET['location_main'];
	$location_sub = $_GET['location_sub'];
	$start_ip = $_GET['start_ip'];
	$end_ip = $_GET['end_ip'];
	$theory_upspeed = $_GET['theory_upspeed'];
	$practical_upspeed = $_GET['practical_upspeed'];
	$theory_downspeed = $_GET['theory_downspeed'];
	$practical_downspeed = $_GET['practical_downspeed'];
	
	if(validip_format($start_ip) && validip_format($end_ip))
	{
		if(ip2long($end_ip) > ip2long($start_ip))
		{
			$query = "INSERT INTO locations (name, flagpic, location_main, location_sub, start_ip, end_ip, theory_upspeed, practical_upspeed, theory_downspeed, practical_downspeed) VALUES (" . sqlesc($name) ."," . sqlesc($flagpic) . "," . sqlesc($location_main). "," . sqlesc($location_sub) . "," . sqlesc($start_ip) .  "," . sqlesc($end_ip) . "," . sqlesc($theory_upspeed) .  "," . sqlesc($practical_upspeed) .  "," . sqlesc($theory_downspeed) .  "," . sqlesc($practical_downspeed) . ")";
			$sql = sql_query($query)  or sqlerr(__FILE__, __LINE__);
			if($sql) {
				$success = true;
			} else {
				$success = false;
			}
		}
		else
			echo("<p><strong>".$lang_location['text_larger_ip']."</strong></p>");
	}
	else 
		echo("<p><strong>".$lang_location['text_invalid_ip']." </strong></p>");

}

echo("<form name='form1' method='get' action='" . $_SERVER['PHP_SELF'] . "'>");
echo("<table class=main cellspacing=0 cellpadding=5 width=48% align= left>");
echo("<tr><td class=colhead align=center colspan=2>".$lang_location['text_addnew']."</td></tr>");
echo("<tr><td class=rowhead>".$lang_location['text_name']."</td><td class=rowfollow align=left><input type='text' size=10 name='name'></td></tr>");
echo("<tr><td class=rowhead><nobr>".$lang_location['text_main_location']."</nobr></td><td class=rowfollow align=left><input type='text' size=50 name='location_main'></td></tr>");
echo("<tr><td class=rowhead><nobr>".$lang_location['text_sub_location']."</nobr></td><td class=rowfollow align=left><input type='text' size=50 name='location_sub'></td></tr>");
echo("<tr><td class=rowhead><nobr>".$lang_location['text_firstip']."</nobr></td><td class=rowfollow align=left><input type='text' size=30 name='start_ip'></td></tr>");
echo("<tr><td class=rowhead><nobr>".$lang_location['text_lastip']."</nobr></td><td class=rowfollow align=left><input type='text' size=30 name='end_ip'></td></tr>");
echo("<tr><td class=rowhead><nobr>".$lang_location['text_theory_up']."</nobr></td><td class=rowfollow align=left><input type='text' size=10 name='theory_upspeed'></td></tr>");
echo("<tr><td class=rowhead><nobr>".$lang_location['text_theory_down']."</nobr></td><td class=rowfollow align=left><input type='text' size=10 name='theory_downspeed'></td></tr>");
echo("<tr><td class=rowhead><nobr>".$lang_location['text_practical_up']."</nobr></td><td class=rowfollow align=left><input type='text' size=10 name='practical_upspeed'></td></tr>");
echo("<tr><td class=rowhead><nobr>".$lang_location['text_practical_down']."</nobr></td><td class=rowfollow align=left><input type='text' size=10 name='practical_downspeed'></td></tr>");
echo("<tr><td class=rowhead>".$lang_location['text_picture']."</td><td class=rowfollow align=left><input type='text' size=50 name='flagpic'><input type='hidden' name='add' value='true'></td></tr>");
echo("<tr><td class=toolbox align=center colspan=2><input class=btn type='Submit'></td></tr>");
echo("</table>");
echo("</form>");

$range_start_ip = $_GET['range_start_ip'];
$range_end_ip = $_GET['range_end_ip'];

echo("<form name='form2' method='get' action='" . $_SERVER['PHP_SELF'] . "'>");
echo("<table class=main cellspacing=0 cellpadding=5 width=48% align=right>");
echo("<tr><td class=colhead align=center colspan=2>".$lang_location['text_check_ip']."</td></tr>");
echo("<tr><td class=rowhead><nobr>".$lang_location['text_firstip']."</nobr></td><td class=rowfollow align=left><input type='text' size=30 name='range_start_ip' value='" . $range_start_ip . "'></td></tr>");
echo("<tr><td class=rowhead><nobr>".$lang_location['text_lastip']."</nobr></td><td class=rowfollow align=left><input type='text' size=30 name='range_end_ip' value='" . $range_end_ip . "'><input type='hidden' name='check_range' value='true'></td></tr>");
echo("<tr><td class=toolbox align=center colspan=2><input class=btn type='Submit'></td></tr>");
echo("</table>");
echo("</form>");
///////////////////// E X I S T I N G C A T E G O R I E S \\\\\\\\\\\\\\\\\\\\\\\\\\\\

print("<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />");


unset($wherea);

$check_range = $_GET['check_range'];
if($check_range == 'true') {

	//stderr("",$range_start_ip . $range_end_ip . validip_format($range_start_ip) . validip_format($range_end_ip));
	if(validip_format($range_start_ip) && validip_format($range_end_ip))
	{
		if(ip2long($range_end_ip) > ip2long($range_start_ip))
		{
			$wherea = "WHERE INET_ATON(start_ip) <=" . ip2long($range_start_ip) . " AND INET_ATON(end_ip) >=" . ip2long($range_end_ip);
			echo("<p><strong>".$lang_location['text_conform_ip']."</strong></p>");
		}
		else
			echo("<p><strong>".$lang_location['text_larger_ip']."</strong></p>");
	}
	else 
		echo("<p><strong>".$lang_location['text_invalid_ip']."</strong></p>");
}
else
{
	echo("<p><strong>" .  ($success == true ? "(Updated!)" : "") . $lang_location['text_existing']."</strong></p>");
}
echo("<table class=main cellspacing=0 cellpadding=5>");
echo("<td class=colhead align=center><b>".$lang_location['text_id']."</b></td> <td class=colhead align=left><b>".$lang_location['text_name']."</b></td> <td class=colhead align=center><b>".$lang_location['text_picture']."</b></td> <td class=colhead align=center><b><nobr>".$lang_location['text_main_location']."</nobr></b></td> <td class=colhead align=center><b><nobr>".$lang_location['text_sub_location']."</nobr></b></td> <td class=colhead align=center><b>".$lang_location['text_firstip']."</b></td> <td class=colhead align=center><b>".$lang_location['text_lastip']."</b></td> <td class=colhead align=center><b>".$lang_location['text_theory_up']."</b></td> <td class=colhead align=center><b>".$lang_location['text_practical_up']."</b></td>  <td class=colhead align=center><b>".$lang_location['text_theory_down']."</b></td> <td class=colhead align=center><b>".$lang_location['text_practical_down']."</b></td> <td class=colhead align=center><b>".$lang_location['text_edit']."</b></td><td class=colhead align=center><b>".$lang_location['text_delete']."</b></td>");

$res = sql_query("SELECT COUNT(*) FROM locations ".$wherea);
$row = mysql_fetch_array($res);
$count = $row[0];
$perpage = 50;
list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "location.php?");

$query = "SELECT * FROM locations " . $wherea ." ORDER BY name ASC, start_ip ASC ".$limit;
$sql = sql_query($query);
$maxlen_sub_location = 40;
while ($row = mysql_fetch_array($sql)) {
	$id = $row['id'];
	$name = $row['name'];
	$flagpic = $row['flagpic'];
	$location_main = $row['location_main'];
	$location_sub = $row['location_sub'];
	$start_ip = $row['start_ip'];
	$end_ip = $row['end_ip'];
	$theory_upspeed = $row['theory_upspeed'];
	$practical_upspeed = $row['practical_upspeed'];
	$theory_downspeed = $row['theory_downspeed'];
	$practical_downspeed = $row['practical_downspeed'];
	
	$count_location_sub=strlen($location_sub);
	if($count_location_sub > $maxlen_sub_location)
		$location_sub=substr($location_sub, 0, $maxlen_sub_location) . "..";
	
	echo("<tr><td class=rowfollow align=center><strong>$id</strong></td>" .
	"<td class=rowfollow align=left><strong>$name</strong></td>" .
	"<td class=rowfollow align=center>" . ($flagpic != "" ? "<img src='" . get_protocol_prefix() . "$BASEURL/pic/location/$flagpic' border='0' />" : "-") . "</td>" .
	"<td class=rowfollow align=left>$location_main</td>" .
	"<td class=rowfollow align=left>$location_sub</td>" .
	"<td class=rowfollow align=left>" . $start_ip . "</td>" .
	"<td class=rowfollow align=left>" . $end_ip . "</td>" .
	"<td class=rowfollow align=left>$theory_upspeed</td>" .
	"<td class=rowfollow align=left>$practical_upspeed</td>" .
	"<td class=rowfollow align=left>$theory_downspeed</td>" .
	"<td class=rowfollow align=left>$practical_downspeed</td>" .
	"<td class=rowfollow align=center><a href='" . $_SERVER['PHP_SELF'] . "?editid=$id'>".$lang_location['text_edit']."</a></td>".
	"<td class=rowfollow align=center><a href='" . $_SERVER['PHP_SELF'] . "?delid=$id'>".$lang_location['text_delete']."</a></td>" .
	"</tr>");
}
print("</table>");
echo $pagerbottom;

end_frame();
end_frame();
stdfoot();

?>
