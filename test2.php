<?php
require_once("include/bittorrent.php");
dbconn();
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?php
/*if(isset($_POST['endtime']))
	echo $_POST['endtime'];
	else
	echo "No result!\n";

	echo date('Y-m-d H:i:s');
echo "--".time();
*/

$res  = sql_query("SELECT userid, SUM(bonus) FROM bets WHERE optionid <> 11 and gameid = ".sqlesc(36)." GROUP BY userid order by SUM(bonus) DESC LIMIT 20") or sqlerr(__FILE__, __LINE__);
while($m = mysql_fetch_array($res))
    echo $m[0]."--".$m[1]."<br />";

?>
 
</body>
