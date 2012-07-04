<?php

function sprint_endtime()
{
    $date = date('Y-m-d');
    $pattern = '/(\d+)-(\d+)-(\d+)/';
    preg_match($pattern, $date, $matches);
    $year = $matches[1];
    $month = $matches[2];
    $day = $matches[3];

    $str = "";
    $str .= $year."年".$month."月
<select name='end_day' id='end_day'>
<option value='0'>---</option>";
for($i=0; $i<7; $i++)
    $str .= "<option value='".sprintf("%02d", $day+$i)."'>".($day+$i)."</option>";
$str .= "</select>日 
<select name='end_hour' id='end_hour'>";
for($i=0; $i<=23; $i++)
    $str .= "<option value='".sprintf("%02d", $i)."'>".sprintf("%02d", $i)."</option>";
$str .= "</select>时 
<select name='end_min' id='end_min'>";
for($i=0; $i<=59; $i++)
    $str .= "<option value='".sprintf("%02d", $i)."'>".sprintf("%02d", $i)."</option>";
$str .= "</select>分 
<select name='end_sec' id='end_sec'>";
for($i=0; $i<=59; $i++)
    $str .= "<option value='".sprintf("%02d", $i)."'>".sprintf("%02d", $i)."</option>";
$str .= "</select>秒
<input type='hidden' value='' name='endtime' id='endtime' />";

return $str;
}

?> 
