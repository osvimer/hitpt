<?php
header("Expires: Mon, 26 Jul 1970 05:00:00 GMT");

require_once("include/bittorrent.php");
dbconn();
require_once(get_langfile_path());
loggedinorreturn();
parked();

if ($CURUSER["uploadpos"] == 'no')
	stderr($lang_upload['std_sorry'], $lang_upload['std_unauthorized_to_upload'],false);

if ($enableoffer == 'yes')
	$has_allowed_offer = get_row_count("offers","WHERE allowed='allowed' AND userid = ". sqlesc($CURUSER["id"]));
else $has_allowed_offer = 0;
$uploadfreely = user_can_upload("torrents");
$allowtorrents = ($has_allowed_offer || $uploadfreely);
$allowspecial = user_can_upload("music");

if (!$allowtorrents && !$allowspecial)
	stderr($lang_upload['std_sorry'],$lang_upload['std_please_offer'],false);
$allowtwosec = ($allowtorrents && $allowspecial);

$brsectiontype = $browsecatmode;
$spsectiontype = $specialcatmode;
$showsource = (($allowtorrents && get_searchbox_value($brsectiontype, 'showsource')) || ($allowspecial && get_searchbox_value($spsectiontype, 'showsource'))); //whether show sources or not
$showmedium = (($allowtorrents && get_searchbox_value($brsectiontype, 'showmedium')) || ($allowspecial && get_searchbox_value($spsectiontype, 'showmedium'))); //whether show media or not
$showcodec = (($allowtorrents && get_searchbox_value($brsectiontype, 'showcodec')) || ($allowspecial && get_searchbox_value($spsectiontype, 'showcodec'))); //whether show codecs or not
$showstandard = (($allowtorrents && get_searchbox_value($brsectiontype, 'showstandard')) || ($allowspecial && get_searchbox_value($spsectiontype, 'showstandard'))); //whether show standards or not
$showprocessing = (($allowtorrents && get_searchbox_value($brsectiontype, 'showprocessing')) || ($allowspecial && get_searchbox_value($spsectiontype, 'showprocessing'))); //whether show processings or not
$showteam = (($allowtorrents && get_searchbox_value($brsectiontype, 'showteam')) || ($allowspecial && get_searchbox_value($spsectiontype, 'showteam'))); //whether show teams or not
$showaudiocodec = (($allowtorrents && get_searchbox_value($brsectiontype, 'showaudiocodec')) || ($allowspecial && get_searchbox_value($spsectiontype, 'showaudiocodec'))); //whether show languages or not

stdhead($lang_upload['head_upload']);
//加入做种版权类问题公告信息

/*?>
<table align="center">
<tr><td><?=$lang_upload['text_zhuyi']?></td></tr>
<tr><td></td></tr>
</table>*/
print("<div align=\"center\"><form method=\"get\" action=\"torrents.php?\" target=\"_blank\">".$lang_upload['text_search_offer_note']."&nbsp;&nbsp;<input type=\"text\" name=\"search\">&nbsp;&nbsp;<input type=\"hidden\" name=\"incldead\" value=0>");
print("<input type=\"submit\" class=\"btn\" value=\"".$lang_upload['submit_search']."\" /></form></div>");

?>
	<form id="compose" enctype="multipart/form-data" action="takeupload.php" method="post" name="upload">
			<?php
			print("<p align=\"center\">".$lang_upload['text_red_star_required']."</p>");
			?>
			<table border="1" cellspacing="0" cellpadding="5" width="1100">
				<tr>
					<td class='colhead' colspan='2' align='center'>
						<?php echo $lang_upload['text_tracker_url'] ?>: &nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo  get_protocol_prefix() . $announce_urls[0]?></b>
						<?php
						if(!is_writable($torrent_dir))
						print("<br /><br /><b>ATTENTION</b>: Torrent directory isn't writable. Please contact the administrator about this problem!");
						if(!$max_torrent_size)
						print("<br /><br /><b>ATTENTION</b>: Max. Torrent Size not set. Please contact the administrator about this problem!");
						?>
					</td>
				</tr>
<script type="text/javascript">
$(document).ready(function(){
	uplist("source_sel",new Array(['0','请先选择一级类型']));
	$("#browsecat").change(function(){
		secondtype($(this).val());
		$.get("guize.php?id="+$(this).val()+"&t="+new Date(), function(result){
			$("#gstishi").html(result);
		});
	});
	$("#qr").click(function(){
		var err = "";
		if($("#browsecat").val() == 0)  err += "请选择[类型]\n\n";
		if($("#source_sel").val() == 0)	err += "请选择[子类型]\n\n";
		if($("#torrent").val() == "") err += "请选择[种子文件]\n\n";
      //	if($("#name").val().length < 10) err += "[标题]内容不得少于10个字符\n\n";
		if($("#descr").val().length < 50) err += "[简介]内容不得少于50个字符\n\n";
		if(err == "") return true;
		jAlert(err);
		return false;
	});
});
function uplist(name,list) {
	var childRet = document.getElementById(name);
	for (var i = childRet.childNodes.length-1; i >= 0; i--) { 
		childRet.removeChild(childRet.childNodes.item(i)); 
	} 
	for (var j=0; j<list.length; j++) {
		var ret = document.createDocumentFragment();
		var newop = document.createElement("option");
		newop.id = list[j][0];
		newop.value = list[j][0]; 
		newop.appendChild(document.createTextNode(list[j][1])); 
		ret.appendChild(newop); 
		document.getElementById(name).appendChild(ret); 
	}
}

function secondtype(value) {
<?
	$cats = genrelist($browsecatmode);
        foreach ($cats as $row){
	$catsid = $row['id'];
	$secondtype = searchbox_item_list("sources",$catsid);
	$secondsize = count($secondtype,0);
	$cachearray = $cachearray."var lid".$catsid." = new Array(['0','请选择子类型']";
	for($i=0; $i<$secondsize; $i++){
		$cachearray = $cachearray.",['".$secondtype[$i]['id']."','".$secondtype[$i]['name']."']";
	}
	$cachearray = $cachearray.");\n";
	}
        $cats = genrelist($browsecatmode);
	$cachearray = $cachearray."switch(value){\n";
        foreach ($cats as $row){
        $catsid = $row['id'];
	$cachearray = $cachearray."\tcase \"".$catsid."\": ";
	$cachearray = $cachearray."uplist(\"source_sel\",lid".$catsid.");";
	$cachearray = $cachearray."break;\n";
	}
	$cachearray = $cachearray."}\n";
	print($cachearray);
?>
}
</script>
<?php
if ($allowtorrents){
		$disablespecial = " onchange=\"disableother('browsecat','specialcat')\"";
		$s = "<select name=\"type\" id=\"browsecat\" ".($allowtwosec ? $disablespecial : "").">\n<option value=\"0\">".$lang_upload['select_choose_one']."</option>\n";
		$cats = genrelist($browsecatmode);
                                        foreach ($cats as $row)
                                                $s .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";
                                        $s .= "</select>\n";
                                }
                                else $s = "";
                                if ($allowspecial){
                                        $disablebrowse = " onchange=\"disableother('specialcat','browsecat')\"";
                                        $s2 = "<select name=\"type\" id=\"specialcat\" ".$disablebrowse.">\n<option value=\"0\">".$lang_upload['select_choose_one']."</option>\n";
                                        $cats2 = genrelist($specialcatmode);
                                        foreach ($cats2 as $row)
                                                $s2 .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";
                                        $s2 .= "</select>\n";
                                }
                                else $s2 = "";
                                tr($lang_upload['row_type']."<font color=\"red\">*</font>", ($allowtwosec ? $lang_upload['text_to_browse_section'] : "").$s.($allowtwosec ? $lang_upload['text_to_special_section'] : "").$s2.($allowtwosec ? $lang_upload['text_type_note'] : ""),1);

				if ($showsource || $showmedium || $showcodec || $showaudiocodec || $showstandard || $showprocessing){
                                        if ($showsource){
                                               $source_select = torrent_selection($lang_upload['text_source'],"source_sel","sources");
                                        }
                                        else $source_select = "";

                                        if ($showmedium){
                                                $medium_select = torrent_selection($lang_upload['text_medium'],"medium_sel","media");
                                        }
                                        else $medium_select = "";

                                        if ($showcodec){
                                                $codec_select = torrent_selection($lang_upload['text_codec'],"codec_sel","codecs");
                                        }
                                        else $codec_select = "";

                                        if ($showaudiocodec){
                                                $audiocodec_select = torrent_selection($lang_upload['text_audio_codec'],"audiocodec_sel","audiocodecs");
                                        }
                                        else $audiocodec_select = "";

                                        if ($showstandard){
                                                $standard_select = torrent_selection($lang_upload['text_standard'],"standard_sel","standards");
                                        }
                                        else $standard_select = "";

                                        if ($showprocessing){
                                                $processing_select = torrent_selection($lang_upload['text_processing'],"processing_sel","processings");
                                        }
                                        else $processing_select = "";

                                        //tr($lang_upload['row_quality']."<font color=red>*</font>", $source_select . $medium_select. $codec_select . $audiocodec_select. $standard_select . $processing_select, 1 );
                                        tr($lang_upload['row_quality']."<font color=red>*</font>", "<select id='source_sel' name='source_sel'></select>", 1 );
                                }

				//tr($lang_upload['row_torrent_file']."<font color=\"red\">*</font>", "<input type=\"file\" class=\"file\" id=\"torrent\" name=\"file\" onchange=\"getname()\" />\n", 1);
				tr($lang_upload['row_torrent_file']."<font color=\"red\">*</font>", "<input type=\"file\" class=\"file\" id=\"torrent\" name=\"file\" />\n", 1);  //注意这里和上句被注释的区别在于是否onchange时获取种子名称
				if ($altname_main == 'yes'){
					tr($lang_upload['row_torrent_name'], "<b>".$lang_upload['text_english_title']."</b>&nbsp;<input type=\"text\" style=\"width: 250px;\" name=\"name\" />&nbsp;&nbsp;&nbsp;
<b>".$lang_upload['text_chinese_title']."</b>&nbsp;<input type=\"text\" style=\"width: 250px\" name=\"cnname\"><br /><font class=\"medium\">".$lang_upload['text_titles_note']."</font>", 1);
				}
				else
					tr($lang_upload['row_torrent_name'], "<input type=\"text\" style=\"width: 650px;\" id=\"name\" name=\"name\" /><br /><font class=\"medium\">".$lang_upload['text_torrent_name_note']."</font>", 1);
				if ($smalldescription_main == 'yes')
				tr($lang_upload['row_small_description'], "<input type=\"text\" style=\"width: 650px;\" name=\"small_descr\" /><br /><font class=\"medium\">".$lang_upload['text_small_description_note']."</font>", 1);
				//tr($lang_upload['row_description_note'],"<br /><font size=+2 color=brown>".$lang_upload['text_description_note']."</font>", 1);
				
				get_external_tr();
				get_dbexternal_tr();
				if ($enablenfo_main=='yes')
					tr($lang_upload['row_nfo_file'], "<input type=\"file\" class=\"file\" name=\"nfo\" /><br /><font class=\"medium\">".$lang_upload['text_only_viewed_by'].get_user_class_name($viewnfo_class,false,true,true).$lang_upload['text_or_above']."</font>", 1);
				print("<tr><td class=\"rowhead\" style='padding: 3px' valign=\"top\">".$lang_upload['row_description']."<font color=\"red\">*</font></td><td class=\"rowfollow\">");
				textbbcode("upload","descr","",false);
				print("</td></tr>\n");


				if ($showteam){
					if ($showteam){
						$team_select = torrent_selection($lang_upload['text_team'],"team_sel","teams");
					}
					else $showteam = "";

					tr($lang_upload['row_content'],$team_select,1);
				}

				//==== offer dropdown for offer mod  from code by S4NE
				$offerres = sql_query("SELECT id, name FROM offers WHERE userid = ".sqlesc($CURUSER[id])." AND allowed = 'allowed' ORDER BY name ASC") or sqlerr(__FILE__, __LINE__);
				if (mysql_num_rows($offerres) > 0)
				{
					$offer = "<select name=\"offer\"><option value=\"0\">".$lang_upload['select_choose_one']."</option>";
					while($offerrow = mysql_fetch_array($offerres))
						$offer .= "<option value=\"" . $offerrow["id"] . "\">" . htmlspecialchars($offerrow["name"]) . "</option>";
					$offer .= "</select>";
					tr($lang_upload['row_your_offer']. (!$uploadfreely && !$allowspecial ? "<font color=red>*</font>" : ""), $offer.$lang_upload['text_please_select_offer'] , 1);
				}
				//===end

				if(get_user_class()>=$beanonymous_class)
				{
					tr($lang_upload['row_show_uploader'], "<input type=\"checkbox\" name=\"uplver\" value=\"yes\" />".$lang_upload['checkbox_hide_uploader_note'], 1);
				}
				?>
				<tr><td class="toolbox" align="center" colspan="2"><b><?php echo $lang_upload['text_read_rules']?></b> <input id="qr" type="submit" class="btn" value="<?php echo $lang_upload['submit_upload']?>" /></td></tr>
		</table>
	</form>
<?php
stdfoot();
