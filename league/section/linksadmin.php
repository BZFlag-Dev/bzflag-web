<?php
function section_linksadmin(&$templ)
{
	$link = $_GET['link'];
	$f_name = $_POST['f_name'];
	$f_url	= $_POST['f_url'];
	$f_comment = $_POST['f_comment'];
	$f_ok_x = $_POST['f_ok_x'];

	if($f_ok_x) {
		mysql_query("insert into l_link values(\"".$f_name."\", \"".$f_url."\", \"".$f_comment."\")");
		echo "<center>Thank you, ".$s_callsign.", for this great link!</center>";
	} else {
		echo '<form method=post>'.SID.'<table align=center border=0 cellspacing=0 cellpadding=1>
		<input type=hidden name=link value='.$link.'>
		<tr><td>Link title: <input type=text size=50 maxlength=80 name=f_name></td></tr>
		<tr><td>Link URL: <input type=text size=50 maxlength=120 name=f_url></td></tr>
		<tr><td>Link comment:<br><textarea cols=60 rows=10 name=f_comment></textarea></td></tr>
		<tr><td><hr></td></tr>
		<tr><td align=center><input type=image name=f_ok src="images/ok.gif"></td></tr>
		</form></table>';
	}
}
?>