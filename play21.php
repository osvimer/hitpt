<?
require "include/bittorrent.php";
require "memcache.php";
dbconn();
loggedinorreturn();
require_once(get_langfile_path());
parked();
    if($CURUSER['class'] < 1){
		stderr($lang_play['text_head'],$lang_play['std_play21_system_disabled'],false);
    }
$gamename = 'play21_';
$action = $_GET['action'];
if($action == "retpark"){
		// json_decode json转数组
		// json_encode 数组转json
	$start = $memcache->get($gamename.$CURUSER['id']);
	if($start == 0){
		echo json_encode(array("park" => "nostart", "num" => "nostart"));
		die;
	}
	$parklist = json_decode($memcache->get($gamename."park_playpark_".$CURUSER['id']));
	$parknum = json_decode($memcache->get($gamename."park_playnum_".$CURUSER['id']));
	$numtotal = retnum($parknum);
	if($start == 1){
		foreach ($parklist as $i) {
			$parkliststr = $parkliststr.$i;
		}
		echo json_encode(array("playpark" => $parkliststr, "playnum" => $numtotal));
		die;
	}
	if($numtotal >= 21){
		echo json_encode(array("park" => "full", "num" => "full"));
		$memcache->set($gamename.$CURUSER['id'],'0',false,3600);
		die;
	}
	$park = retpark();
	$parklist[] = $park["park"];
	$parknum[] = $park["num"];
	foreach ($parklist as $i) {
		$parkliststr = $parkliststr.$i;
	}
	$numtotal = retnum($parknum);
	if($numtotal > 21)	$memcache->set($gamename.$CURUSER['id'],'0',false,3600);
	if($numtotal == 21)	$memcache->set($gamename.$CURUSER['id'],'1',false,3600);
	echo json_encode(array("playpark" => $parkliststr, "playnum" => $numtotal));
	$memcache->set($gamename."park_playpark_".$CURUSER['id'],json_encode($parklist),false,3600);
	$memcache->set($gamename."park_playnum_".$CURUSER['id'],json_encode($parknum),false,3600);
	die;
}
if($action == "stop"){
	$start = $memcache->get($gamename.$CURUSER['id']);
	if($start == 0){
		echo json_encode(array("park" => "nostart", "num" => "nostart"));
		die;
	}
	$comparklist = json_decode($memcache->get($gamename."park_compark_".$CURUSER['id']));
	$comparknum = json_decode($memcache->get($gamename."park_comnum_".$CURUSER['id']));
	$playparknum = json_decode($memcache->get($gamename."park_playnum_".$CURUSER['id']));

	$playnumtotal = retnum($playparknum);
	$comnumtotal = retnum($comparknum);
	while(((rand(0,1) && $comnumtotal>=16 && $comnumtotal<19) || $comnumtotal<16) && $comnumtotal < $playnumtotal){
		$park = retpark();
		$comparklist[] = $park["park"];
		$comparknum[] = $park["num"];
		$comnumtotal = retnum($comparknum);
	}
	foreach ($comparklist as $i) {
		$comparkliststr = $comparkliststr.$i;
	}
	$memcache->set($gamename."park_compark_".$CURUSER['id'],json_encode($comparklist),false,3600);
	$memcache->set($gamename."park_comnum_".$CURUSER['id'],json_encode($comparknum),false,3600);

	$bonus = $memcache->get($gamename."playbonus_".$CURUSER['id']);
	$playnumtotal = retnum($playparknum);
	if($comnumtotal > 21){
		$bonus = $bonus * 2;
		sql_query("UPDATE users SET seedbonus = seedbonus + $bonus WHERE id = ".sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
		$retarr = array("compark" => $comparkliststr, "comnum" => $comnumtotal,"playnum" => $playparknum, "playbonus" => $bonus);
	}else{
		if($playnumtotal > $comnumtotal){
			$bonus = $bonus * 2;
			sql_query("UPDATE users SET seedbonus = seedbonus + $bonus WHERE id = ".sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);
			$retarr = array("compark" => $comparkliststr, "comnum" => $comnumtotal,"playnum" => $playparknum, "playbonus" => $bonus);
		}else{
			$retarr = array("compark" => $comparkliststr, "comnum" => $comnumtotal,"playnum" => $playparknum, "playbonus" => 0);
		}
	}
	echo json_encode($retarr);
	$memcache->set($gamename.$CURUSER['id'],'0',false,3600);
	die;
}
if($action == "init"){
	$start = $memcache->get($gamename.$CURUSER['id']);
	if($start == "1" || $start == "2"){
		echo json_encode(array("park" => "start", "num" => "start"));
		die;
	}
	$bonus = 0 + $_GET['bonus'];
	if($bonus <= 0) die;
	if($bonus > $CURUSER['seedbonus']){
		echo json_encode(array("park" => "no", "num" => "no"));
		die;
	}
	initplay();
	$memcache->set($gamename."playbonus_".$CURUSER['id'],$bonus,false,3600);
	sql_query("UPDATE users SET seedbonus = seedbonus - $bonus WHERE id = ".sqlesc($CURUSER['id'])) or sqlerr(__FILE__, __LINE__);

for($i=0;$i<2;$i++){
	$park = retpark();
	$comparklist[] = $park["park"];
	$comparknum[] = $park["num"];
}
	$comparkliststr = $comparklist[0]."★";
	$comnumtotal = 0;//retnum($comparknum);

for($i=0;$i<2;$i++){
	$park = retpark();
	$playparklist[] = $park["park"];
	$playparknum[] = $park["num"];
}
	foreach ($playparklist as $i) {
		$playparkliststr = $playparkliststr.$i;
	}
	$playnumtotal = retnum($playparknum);
	$memcache->set($gamename."park_playpark_".$CURUSER['id'],json_encode($playparklist),false,3600);
	$memcache->set($gamename."park_playnum_".$CURUSER['id'],json_encode($playparknum),false,3600);
	$memcache->set($gamename."park_compark_".$CURUSER['id'],json_encode($comparklist),false,3600);
	$memcache->set($gamename."park_comnum_".$CURUSER['id'],json_encode($comparknum),false,3600);
	echo json_encode(array("playpark" => $playparkliststr, "playnum" => $playnumtotal, "compark" => $comparkliststr, "comnum" => $comnumtotal));
	if($playnumtotal==21) $memcache->set($gamename.$CURUSER['id'],'1',false,3600);
	$memcache->set($gamename.$CURUSER['id'],'2',false,3600);
	die;
}
function retnum($num){
	natsort($num);  //自然语言排序
	$numtotal = 0;
	foreach ($num as $i) {
		if($i!='A')
			$numtotal += $i;
		else{
			if(($numtotal + 11) <= 21)
				$numtotal += 11;
			else
				$numtotal += 1;
		}
	}
	return $numtotal;
}
function retpark(){
	global $memcache,$gamename,$CURUSER;
	$park = json_decode($memcache->get($gamename."park_".$CURUSER['id']));
	$num = $memcache->get($gamename."park_num_".$CURUSER['id']) + 1;
	$memcache->set($gamename."park_num_".$CURUSER['id'],$num,false,3600);
	$parknum = substr($park[$num],3,1);
			switch($parknum){
				case 'A': $parknum = 'A';break;
        		case 'J': $parknum = '10';break;
        		case 'Q': $parknum = '10';break;
        		case 'K': $parknum = '10';break;
        		case '1': $parknum = '10';break;
        		default: $parknum = $parknum;
    		}	
	if($num<52){
		if(substr($park[$num],0,3) == "♥" || substr($park[$num],0,3) == "♦")
			return array("park" => "<font color=red>".$park[$num]."</font>", "num" => $parknum);
		else
			return array("park" => $park[$num], "num" => $parknum);
	}else
		return array("park" => "牌已抽完", "num" => 0);
}
function initpark(){
	$park = array();
	for($i=1;$i<=4;$i++){
		switch($i){
			case 1: $temp="♠";break;
			case 2: $temp="♥";break;
			case 3: $temp="♣";break;
			case 4: $temp="♦";break;
		}
		for($j=1;$j<=13;$j++){
			switch($j){
				case 1: $park[] = $temp.'A';break;
        		case 11: $park[] = $temp.'J';break;
        		case 12: $park[] = $temp.'Q';break;
        		case 13: $park[] = $temp.'K';break;
        		default: $park[] = $temp.$j;
    		}
		}
	}
	if(shuffle($park))   //随机排序
		return $park;
	else
		return false;
}
function initplay(){
	global $memcache,$gamename,$CURUSER;
	srand((double)microtime()*1000000);
	if(!$memcache){
		die;
	}
		$memcache->set($gamename.$CURUSER['id'],'1',false,3600) or die ("");
		// json_decode json转数组
		// json_encode 数组转json
		$memcache->set($gamename."park_".$CURUSER['id'],json_encode(initpark()),false,3600);
		$memcache->set($gamename."park_num_".$CURUSER['id'],'0',false,3600);
		$memcache->set($gamename."park_playpark_".$CURUSER['id'],'',false,3600);
		$memcache->set($gamename."park_playnum_".$CURUSER['id'],'',false,3600);
		$memcache->set($gamename."park_compark_".$CURUSER['id'],'',false,3600);
		$memcache->set($gamename."park_comnum_".$CURUSER['id'],'',false,3600);
}
stdhead($lang_index['text_head']);
begin_main_frame();
?>
<script type="text/javascript">
$(document).ready(function(){
	$("#startpark").click(function(){
		jPrompt('魔力值:', '1', '请输入你要压多少魔力值', function(r) {
    if( r )
		if(r > 0 && r <= 100){
			$("#mess").html("您压了"+r+"点魔力值");
			$.getJSON("play21.php?action=init&bonus="+r+"&t="+new Date() ,function(data){ 
			var $park = data['park'];
			if($park=='start'){
				jAlert('您已开局');
			}else if($park=='no'){
				jAlert('您的魔力值不足');
			}
				$("#computerpark").html(data['compark']);
				$("#computernum").html(data['comnum']+"点");
				$("#playpark").html(data['playpark']);
				$("#playnum").html(data['playnum']+"点");
					if(data['playnum'] == 21)
						jAlert('黑杰克!');
			});
			}else{
				jAlert("测试阶段，只允许使用1至100个魔力值");
			}
		});
	});
	$("#stoppark").click(function(){
		$.getJSON("play21.php?action=stop&t="+new Date(),function(data){ 
			var $park = data['park'];
			if($park=='nostart'){
				jAlert('还未开局');
			}else{
				$("#computerpark").html(data['compark']);
				$("#computernum").html(data['comnum']+"点");
				jAlert("你获得了："+data['playbonus']+"点魔力值");
			}
		});
	});
	$("#retpark").click(function(){
		$.getJSON("play21.php?action=retpark&t="+new Date(),function(data){
			var $park = data['park'];
			var $playnum = data['playnum'];
				if($park=='nostart'){
					jAlert('还未开局');
				}else{
					$("#playpark").html(data['playpark']);
					$("#playnum").html(data['playnum']+"点");
					if($playnum > 21)
						jAlert('您爆掉了');
					if($playnum == 21)
						jAlert('恭喜您获得了21点');
				}
		});
	});
	$(".has_children").click(function(){
		$(".has_children:eq(1)>a:contains('c')").remove();
		var $a1 = $("<a>zz</a>");
		$(this).append($a1);			//添加控件
	        $(this).siblings().removeClass("highlight")   //siblings选择同样控件
        	        .children("a").slideUp().end();	//hide(),fadeOut()
							//slow,normal,fast
        	$(this).addClass("highlight")		//添加效果
                	.children("a").slideDown().end();	//show(),fadeIn()
		$(this).clone(true).appendTo("#menu2");  //克隆控件
		$("#menu2>.has_children").css("opacity","0.5");	//不透明度
	});
	$("a").click(function(event){
		//alert("a");
		event.stopPropagation();    //停止事件冒泡
	});
})

</script>
<h1 align="center">小游戏：21点(临时开放测试中，很多地方还不完善)</h1>
<table align="center">
<tr>
<td width=300>庄家的牌：<font id=computernum></font></td><td width=300>你的牌：<font id=playnum></font></td><td>操作：<font id=mess></font></td>
</tr>
<tr>
<td height=80>
<font size="+2" id=computerpark></font>
</td>
<td>
<font size="+2" id=playpark></font>
</td>
<td>
<input type=button id=startpark value="开始">
<input type=button id=retpark value="拿牌">
<input type=button id=stoppark value="停牌">
</td>
</tr>
</table>
<br /><br />
<table>
<tr><td>
玩法规则:
</td></tr>
<tr><td>
&nbsp&nbsp&nbsp&nbsp21点一般用到1-8副牌。庄家给每个玩家发两张牌，牌面朝下；给自己发两张牌，一张牌面朝上(叫明牌)，一张牌面朝下(叫暗牌)。大家手中扑克点数的计算是：K、Q、J 和 10 牌都算作 10 点。 A 牌既可算作1 点也可算作11 点，由玩家自己决定。其余所有2 至9 牌均按其原面值计算。
<br />&nbsp&nbsp&nbsp&nbsp首先玩家开始要牌，如果玩家拿到的前两张牌是一张 A 和一张10点牌，就拥有黑杰克 (Blackjack)；此时，如果庄家没有黑杰克，玩家就能赢得2倍的赌金（1赔2）。没有黑杰克的玩家可以继续拿牌，可以随意要多少张。目的是尽量往21点靠，靠得越近越好，最好就是21点了。在要牌的过程中，如果所有的牌加起来超过21点，玩家就输了——叫爆掉(Bust)，游戏也就结束了。假如玩家没爆掉，又决定不再要牌了，这时庄家就把他的那张暗牌打开来。庄家根据自己的情况拿牌，一般到17点或17点以上不再拿牌，但也有可能15到16点甚至12到13点就不再拿牌或者18到19点继续拿牌。假如庄家爆掉了，那他就输了。假如他没爆掉，那么你就与他比点数大小，大为赢。一样的点数为平手，你可以把你的赌注拿回来.
</td></tr>
</table>
<?
end_main_frame();
stdfoot();
?>
