<?php 
function section_admin(&$templ)
{
	$f_faq		= $_POST['f_faq'];
	$f_homepage	= $_POST['f_homepage'];
	$f_contact	= $_POST['f_contact'];
	$f_motd		= $_POST['f_motd'];
	$f_todo		= $_POST['f_todo'];
	$f_ok_x		= $_POST['f_ok_x'];
	$link		= 'admin';

	if($_SESSION['authenticated'] && ($_SESSION['level'] == "admin")) {
		if($f_ok_x) {
			// Update the table
			mysql_query("update l_siteconfig
						set faq=\"".$f_faq."\",
						homepage=\"".$f_homepage."\",
						contact=\"".$f_contact."\",
						motd=\"".$f_motd."\",
						todo=\"".$f_todo."\"");
			echo "<center>Site updated, thank you ".$s_callsign.".</center>";
		} else {
			$obj = mysql_fetch_object(mysql_query("select * from l_siteconfig"));

			echo '<form method=post>'.SID_FORM.'
			<table align=left border=0 cellspacing=0 cellpadding=1>
			<input type=hidden name=link value='.$link.'>
			<tr><td align=center></td>Quote of the day:<br><textarea class=areasmall name=f_motd cols=70 rows=15>'.$obj->motd.'</textarea></tr>
			<tr><td align=center></td>Home page:<br><textarea class=areasmall name=f_homepage cols=70 rows=15>'.$obj->homepage.'</textarea></tr>
			<tr><td align=center></td>Contact:<br><textarea class=areasmall name=f_contact cols=70 rows=15>'.$obj->contact.'</textarea></tr>
			<tr><td align=center></td>TODO:<br><textarea class=areasmall name=f_todo cols=70 rows=15>'.$obj->todo.'</textarea></tr>
			<tr><td align=center></td>F.A.Q.:<br>
			Format: &lt;HEAD&gt;Print a bold heading<br>
			&lt;Q&gt;This is my question [carriage return]<br>
			&lt;A&gt;This is the answer[carriage return]<br>
			<textarea class=areasmall name=f_faq cols=70 rows=15>'.$obj->faq.'</textarea></tr>
			<tr><td align=center><input type=image src="images/ok.gif" name=f_ok>&nbsp;<input type=image src="images/cancel.gif" name=f_cancel></td></tr>
			</table></form>';
		}
	} else {
		echo "Your have no permission to go here";
	}					
}
?>