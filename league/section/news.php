<?php

function section_news(&$templ)
{
	// Administration stuff
	if( $_SESSION['authenticated'] && $_SESSION['level'] == "admin" )
	{
		echo '<center><a href="index.php?link=newsadmin&'.SID.'"><img border=0 src="images/add.gif"></a></center><br>';
	}

	$res = mysql_query("select * from l_news order by newsdate desc limit 0, 15");

	echo "<table align=center border=0 cellspacing=0 cellpadding=1>";

	while($obj = mysql_fetch_object($res))
	{
		echo '<tr class=newsbg><td><i>'.$obj->newsdate.'</i></td><td align=right><i>By:</i>'.$obj->writer.'</td></tr>
			<tr><td colspan=2>'.nl2br($obj->text).'</td></tr>
			<tr><td colspan=2 align=center><hr></td></tr>';
	}
	echo "</table>";
}

?>