<?php
require "include/bittorrent.php";
dbconn();
loggedinorreturn();
require_once(get_langfile_path());
if (get_user_class() < UC_MODERATOR) stderr("错误", "你的权限不允许访问。");

if ($_SERVER["REQUEST_METHOD"] == "POST")
	$ip = $_POST["ip"];
else
	$ip = $_GET["ip"];
if ($ip)
{
	$nip = ip2long($ip);
	if ($nip == false || $nip == -1)
	  stderr("错误", "输入的IP地址不合法。");
	else
	{
		$res = sql_query("SELECT * FROM bans WHERE '$nip' >= first AND '$nip' <= last") or sqlerr(__FILE__, __LINE__);
		if (mysql_num_rows($res) == 0)
	  	stderr("结果", $lang_testip['text_resultip']."<b>". htmlspecialchars($ip) ."</b>".$lang_testip['text_notbanned'],false);
		else
		{
	  	$banstable = "<table class=main border=0 cellspacing=0 cellpadding=5>\n" .
	    	"<tr><td class=colhead>".$lang_testip['text_firstip']."</td><td class=colhead>".$lang_testip['text_lastip']."</td><td class=colhead>".$lang_testip['text_comment']."</td></tr>\n";
	  	while ($arr = mysql_fetch_assoc($res))
	  	{
	    		$first = long2ip($arr["first"]);
	    		$last = long2ip($arr["last"]);
	    		$comment = htmlspecialchars($arr["comment"]);
	    		$banstable .= "<tr><td>$first</td><td>$last</td><td>$comment</td></tr>\n";
	  	}
	  	$banstable .= "</table>\n";
	  	stderr("结果", "<table border=0 cellspacing=0 cellpadding=0><tr><td class=embedded style='padding-right: 5px'><img src=pic/smilies/excl.gif></td><td class=embedded>".$lang_testip['text_thisip']."<b>". htmlspecialchars($ip) ."</b>".$lang_testip['text_isbanned']."</td></tr></table><p>". $banstable ."</p>",false);
		}
	}
}
stdhead($lang_testip['head_testip']);

?>
<h1><?php echo $lang_testip['head_testip']?></h1>
<form method=post action=testip.php>
<table border=1 cellspacing=0 cellpadding=5>
<tr><td class=rowhead><?php echo $lang_testip['text_ip']?></td><td><input type=text name=ip></td></tr>
<tr><td colspan=2 align=center><input type=submit class=btn></td></tr>
</form>
</table>

<?php
stdfoot();
?>
