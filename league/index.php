<?php

/*
changed from old code. Sessions
	$_SESSION['authenticated']					// Guest or known user. was $s_logedin (true/false)
	$_SESSION['callsign']						// Name of user. was $s_callsign
	$_SESSION['level']							// cotnains whatever (from mysql)l_player.level field does. was $s_level
	$_SESSION['playerid']						// contains whatever l.player.id does. Was: $s_teamid
	$_SESSION['teamid']							// coontains the currect teamid. Was: $s_teamid
	$_SESSION['leader']							// Is a teamleader or not. was $s_leader.
*/

require_once("lib/db.php");
require_once("lib/bTemplate.php");
require_once("lib/session.php");

session_start();

define("SID_FORM", "<input type=\"hidden\" name=\"" . session_name() . "\" value=\"" . session_id() . "\" />");

if(isset($_POST['logout_x']) || isset($_GET['logout_x']) )
{
	session_destroy();
	unset($_SESSION);
}

// Sections

$validSections = array(
	'home',	'404','login','news','newsadmin','todo','messages','bzforums',
	'fights','teams','teaminfo','leaveteam', 'jointhisteam','jointeam',
	'playerinfo', 'playeradmin','sendmessage','dismissteam','online',
	'banplayer','teamadmin','faq','links','linksadmin','contact',
	'admin','entermatch','createteam','register','players','invite');

if( isSet( $_GET['link'] ) )
	$section = $_GET['link'];
elseif( isset( $_POST['link'] ) )
	$section = $_POST['link'];
else
	$section = 'home';

if(! in_array($section,$validSections) ) {
	$section = '404';
}

// aliased sections (!)
if( $section == 'jointhisteam' )
	$section = 'jointeam';
if( $section == 'playeradmin' )
	$section = 'playerinfo';

// By now, we have a valid section.

$func = 'section_' . $section;
$file = 'section/' . $section . '.php';

require_once($file);

if(! function_exists($func) ) {
	echo "FATAL ERROR: function <b>$func</b> dosnt exist in <b>$file</b>!";
	exit;
}

//
// Generic info for all pages
//

$templ = new bTemplate();

//**MOTD**

$obj = mysql_fetch_object(mysql_query("select motd from l_siteconfig"));
$motd = nl2br($obj->motd);

$templ->set('motd',$motd);

// PAGE STUFF
$page = array();
$page['title'] = 'BZFlag - CTF League';
$page['content'] = 'No data';

$templ->set('page',$page);

// more or less static stuff
$templ->set('utc_time',gmdate('M d Y H:i'));
$templ->set('current_year',gmdate('Y'));

$num = mysql_fetch_object(mysql_query("select count(distinct callsign) num from l_session where playerid != 0 and adddate(expire, INTERVAL 15 MINUTE) > now()"));
$templ->set('users_online', $num->num);

// Session

$templ->set('SID','&' . SID);

// HACK:
// Invoke the section. Currently some of the sectinos
// just prints the output. To put this into our template
// we capture this, by using ob_*
// print'ed output will override whatever page.content may
// contain.

ob_start();
$func(&$templ);
$content = ob_get_contents();
ob_end_clean();

if(! empty($content) )
	$templ->set('page.content',$content);

//**AUTH**

if( isset($_SESSION['authenticated']) && $_SESSION['authenticated'] ) {
	// bTemplate bug, you acnnot use <if:is_authenticated> more than once.. the other
	// is simply ignored :(
	$templ->set('is_authenticated', true, TRUE);		
	$templ->set('is_authenticated2', true, TRUE);
	$templ->set('is_guest', false, TRUE);

	$templ->set('user.name', $_SESSION['callsign'] );
	$templ->set('user.id', $_SESSION['playerid'] );
	$templ->set('user.level', $_SESSION['level'] );

	$m = mysql_fetch_object(mysql_query("select count(*) num from l_message
													where toid={$_SESSION['playerid']}
													and status='new'"));
	switch($m->num) {
		case 0:
			$msg = "You have no new BZmessages";
			break;
		case 1:
			$msg = "You received 1 new BZmessage";
			break;
		default:
			$msg = "You received $m->num new BZmessages";
			break;
	}

	$templ->set('login_message',$msg);

	if( $_SESSION['level'] == "admin" )
		$templ->set('is_admin', true, TRUE);
	else
		$templ->set('is_admin', false, TRUE);


} else {
	$templ->set('is_authenticated', false, TRUE);
	$templ->set('is_authenticated2', false, TRUE);
	$templ->set('is_guest', true, TRUE);
	$templ->set('login_message', 'Howdy stranger');
	$templ->set('is_admin', false, TRUE);
}

//echo $templ->fetch("main.templ");
echo $templ->fetch("old.templ");
/*
print "PHPSESSID: " . session_id() . '<br>';
print "<pre>";
print_r($_SESSION);
print "</pre>";

print "Last Login: " . date('M d Y H:i', $_SESSION['last_login']);
*/

//print '<br><a href="todo.txt">ToDo</a> <a href="changelog.txt">ChangeLog.txt</a>';
?>
