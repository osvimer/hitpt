<?php
require_once ("include/bittorrent.php");
require_once ("include/user_functions.php");
dbconn();
loggedinorreturn();
require_once(get_langfile_path("bet.php"));

// Redone for 2009 by Puerto for HID .. LOL

$HTMLOUT='';

//Convert function by phonzie


function Convert_to_bytes($number, $size, $returnall=false)
{
    if($returnall == false)
    {
       switch($size)
       {
        case "t": 
        return $number*1024*1024*1024*1024;
        case "g":  
        return $number*1024*1024*1024;
        case "m":
        return $number*1024*1024;
        case "k":
        return $number*1024;
        case "b":
        return $number;  
        }
    }
    else
    {
        switch($size)
        {
            case "b":
            return array($number, $number/1024, $number/1024/1024, $number/1024/1024/1024, $number/1024/1024/1024/1024);
            break;
            case "k":
            return array($number*1024, $number, $number/1024, $number/1024/1024, $number/1024/1024/1024);
            break;
            case "m":
            return array($number*1024*1024, $number*1024, $number, $number/1024, $number/1024/1024);
            break;
            case "g":
            return array($number*1024*1024*1024, $number*1024*1024, $number*1024, $number, $number/1024);
            break;
            case "t":
            return array($number*1024*1024*1024*1024, $number*1024*1024*1024, $number*1024*1024, $number*1024, $number);
            break;
        }
    }
}
//--End

// Why not make this for all users ? Lol..
/*
if($CURUSER['class'] < UC_MODERATOR){
header("Location: 404.php");}
*/

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
if(isset($_POST['value']))
{
if(isset($_POST['size']))
{
$values = Convert_to_bytes($_POST["value"], $_POST["size"], true);



$HTMLOUT .="<table width='1%' border='0' cellpadding='5' cellspacing='1' align='center'>
<tr>
<td colspan='3' align='center' ><b>转换结果</b></td>
</tr>
<tr>
<td align='right'><b>字节</b></td>
<td align='center'  width='1%'>=</td>
<td align='left' >$values[0]</td>
</tr>
<tr>
<td align='right'  ><b>KB</b></td>
<td align='center'  width='1%'>=</td>
<td align='left'  >$values[1]</td>
</tr>

<tr>
<td align='right'  ><b>MB</b></td>
<td align='center'  width='1%'>=</td>
<td align='left'  >$values[2]</td>
</tr>
<tr>
<td align='right'><b>GB</b></td>

<td align='center'  width='1%'>=</td>
<td align='left'  >$values[3]</td>
</tr>
<tr>
<td align='right'  ><b>TB</b></td>
<td align='center' width='1%'>=</td>
<td align='left'  >$values[4]</td>

</tr></table>
<br />";

}
else{
stderr("Error", "Missing post data");
}
}
else{
stderr("Error", "Missing post data");
}
}


$HTMLOUT .="<p align='center'> Get the value of this in B, KB, MB, GB, and TB </p>
<form method='post' action='calc.php' name='calc'>
<input name='value' type='text' size='10' value='Insert here' />Value in:&nbsp;&nbsp;<select name='size'>
<option value='b'>字节</option>
<option value='k'>KB</option>
<option value='m'>MB</option>
<option value='g'>GB</option>
<option value='t'>TB</option>
</select>
<br />
<input type='submit' value='Do It' />
</form>";



print stdhead('calcule') . $HTMLOUT . stdfoot();


?>
