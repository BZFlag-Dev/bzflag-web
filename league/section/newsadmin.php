<?php

function section_newsadmin(&$templ)
{
	$f_ok_x = $_POST['f_ok_x'];
	$f_news = $_POST['f_news'];

	if($f_ok_x) {
		$now = gmdate("Y-m-d H:i:s");
		mysql_query("insert into l_news values('$now', \"".$_SESSION['callsign']."\", \"".$f_news."\")");
		echo "<center>Thank you, ".$_SESSION['callsign'].", for this great news!</center>";
	} else {
		echo '<form method=post>'.SID_FORM.'<table align=center border=0 cellspacing=0 cellpadding=1>
			<input type=hidden name=link value='.$link.'>
			<tr><td>News text:<br><textarea class=areasmall cols=70 rows=20 name=f_news></textarea></td></tr>
			<tr><td align=center><input type=image name=f_ok src="images/ok.gif"></td></tr>
			</form></table>';
	}
}

?>