<?php // $Id: runmaintenance.php,v 1.4 2005/03/24 13:27:14 dennismp Exp $ vim:sts=2:et:sw=2

function section_runmaintenance(){
	require_once('lib/common.php');
	require_once('lib/maintfuncs.php');

	$id = $_GET['id'];
	$f_ok_x = $_GET['f_ok_x'];
	$f_no_x = $_GET['f_no_x'];

	if (!isFuncAllowed ('maintenance'))
		return errorPage ('You are not allowed to run site maintenance');		

	if ($_GET['isdup'])
		echo "<BR><center>Cancelled - Refresh/back button detected.</center><BR>\n";
	else if ($f_ok_x)
		section_runmaintenance_doIt ();
	else if ($f_no_x)
		echo "<BR><center>Site Maintenance cancelled.</center><BR>\n";
	else
		section_runmaintenance_confirmForm ();
}

function section_runmaintenance_permissions() {
	return array(
		'maintenance' => 'Allow user to invoke maintenance manually'
	);
};


function section_runmaintenance_doIt (){
	snCheck ($_GET['link'], 'isdup=1');
	echo '<BR><TABLE width=90% align=center><TR><TD>';
	$who = $_SESSION['callsign'];
	$result = site_maintenance ("Manually invoked by $who", true);
	echo '</td></tr></table><BR><CENTER><p>BZmail has been sent to all site admins.<BR>';
}




function section_runmaintenance_confirmForm (){
	$link	= $_GET['link'];

	$row = mysql_fetch_object (sqlQuery('select * from bzl_siteconfig where name="maintenance"'));
	echo '<center><BR><TABLE width=600><TR><TD>
	    <div class=feedback><Center>Running site maintenance will delete inactive players and teams!</center></div><BR>
<UL>
<LI>Teams which have not played a match in the last '. periodMsg(TEAMACTIVE_DAYS)   .' will be marked as inactive.
<LI>Teams which have not had any members logged on in the last '. periodMsg(TEAMNOLOGIN_DAYS) .' will be deleted (team can be revived by site admin). 
<LI>Teams which have never played a match, and are older than '. periodMsg(TEAMMATCHLESS_DAYS) .' will be permenantly deleted.
<LI>Players who do not belong to a team and who have not logged on in the last '. periodMsg(PLAYERNOLOGIN_DAYS)  .' will be deleted.
<p>
	    Maintenance is automatically invoked whenever a match is entered (but not more than once per 24 hours).<p>
</ul>
	    <CENTER><font size=+1>Site Maintenance was last run at: '. $row->text .'<p>
	    <HR><BR><div class=feedback><center>Are you sure that you want to run Site Maintenance now?</div></td></tr></table>';


	echo "<form method=get>
		<input type=hidden name=link value='$link'>
		<input type=hidden name=id value=$row->id>";
		snFormInit ();
		echo htmlFormButton ('Yes', 'f_ok_x') ."
		&nbsp;&nbsp;
		". htmlFormButton ('No', 'f_no_x', CLRBUT) ."
		</form>";
}



?>
