function quick_reply_to(username,floor)
{
    parent.document.getElementById("replaytext").value = "回复 " + floor + " 楼 [@" + username + "] : "+parent.document.getElementById("replaytext").value;
}
