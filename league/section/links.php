<?php
function section_links(&$templ)
{
	// Administration stuff
	if($_SESSION['authenticated'] &&($_SESSION['level'] == "admin")) {
		echo '<center><a href="index.php?link=linksadmin&'.SID.'"><img border=0 src="images/add.gif"></a></center><br>';
	}
	$res = mysql_query("select * from l_link order by name");
	echo "<table aligne=center border=0 cellspacing=0 cellpadding=4>";
	while($obj = mysql_fetch_object($res)) {
		echo '<tr>
		<td><a href="'.$obj->url.'" target="_blank">'.$obj->name.'</a></td>
		<td>'.nl2br($obj->comment).'</td>
		</tr>';
	}
	echo "</table>";
}
?>