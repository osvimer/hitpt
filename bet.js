function get_endtime(year, month, nowtime)
{
	var day=parent.document.getElementById("end_day").value;
	var hour=parent.document.getElementById("end_hour").value;
	var min=parent.document.getElementById("end_min").value;
	var sec=parent.document.getElementById("end_sec").value;
	
	if(day == '0')
	    var commonTime = parseInt(nowtime, 10);
	else
	    var commonTime = Math.round(new Date(Date.UTC(parseInt(year, 10), parseInt(month, 10)-1, parseInt(day, 10), parseInt(hour, 10)-8, parseInt(min, 10), parseInt(sec, 10))).getTime()/1000);
//	alert(year+' '+month+' '+day+' '+hour+' '+min+' '+sec+'--'+commonTime);	
	parent.document.getElementById("endtime").value=commonTime;
}
