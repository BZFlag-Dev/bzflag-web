<?php
function section_online(&$templ)
{
	$res = mysql_query("select distinct playerid, callsign from l_session where playerid != 0 and adddate(expire, INTERVAL 15 MINUTE) > now() order by callsign");
	if( $res )
	{
		$num = mysql_num_rows($res);
		if($num == 0) {
			echo "<center>Nobody's online</center>";
		} else {
			// Display list
			echo "<center>";
			while($row = mysql_fetch_object($res)) {
				echo '<a href="index.php?link=playerinfo&id='.$row->playerid.'&'.SID.'">'.$row->callsign.'</a><br>';
			}
			echo "</center>";
		}
	}
	else
	{
		print "Data problems..";
	}
}
?>
