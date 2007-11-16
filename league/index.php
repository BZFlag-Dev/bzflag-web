<?php // $Id$ vim:sts=2:et:sw=2

define (CODE_VERSION, "2.03.00-devel");


//echo "Temporarily disabled - we'll be right back!";
//exit (0);




require_once(".config/config.php");
require_once("lib/helpers.php");
require_once("lib/common.php");
require_once("lib/sql.php");
sqlConnect(SQL_CONFIG);
require_once("lib/session.php");

// GLOBALS ...
$Authenticated = false;
$UserName = 'guest';
$UserID = null;
$UserLevel = 'guest';

session_start();

define("SID_FORM", "<input type=\"hidden\" name=\"" . session_name() . "\" value=\"" . session_id() . "\" />");

if(isset($_POST['logout_x']) || isset($_GET['logout_x'])){
  session_destroy();
  unset($_SESSION);
  sessionStaticInfo();
}


/////////  added here for php5 ? ... menotume 2006/4/15
sessionStaticInfo();
/////////////////


if (isset($_POST['settheme'])){
  $theme = $_POST['settheme'];
  setcookie ('themename', $theme, time()+60*60*24*60, '/' );
} else
  $theme = $_COOKIE['themename'];
if (!$theme)
  $theme = DEFAULT_THEME;


if ($_SESSION['required']){
  $_POST['link'] = 'required';
}


getSection ($phpFile, $phpFunc, $SectionTitle);
require_once($phpFile);

if(!function_exists($phpFunc) ) {
  echo "FATAL ERROR: function <b>$func</b> does not exist in <b>$phpFile</b>!";
  exit;
}


define (THEME_NAME, $theme);
define (THEME_DIR, $_SESSION['themes'][$theme][1] .'/');
define (FLAG_DIR, "templates/flags/");


if( isAuthenticated() ) {
  $Authenticated = true;
  $UserName = $_SESSION['callsign'];
  $UserID = $_SESSION['playerid'];
  $UserLevel = $_SESSION['level'];

  // ADDED BY BLAST007 TO COMBAT SPAM
  if (strtolower($UserName) == 'sportcunt') exit;
  // END OF WHAT WAS ADDED

} else if (PRIVATE_LEAGUE > 1){
  require_once ("privatelogin.php");

}


ob_start();
$phpFunc();
$PageContent = ob_get_contents();
ob_end_clean();
if ( empty($PageContent))
  $PageContent = '<CENTER>No Data :(<BR>';


/***  setup some vars for the main include file ...  ****/
$row = mysql_fetch_object (sqlQuery("select count(distinct callsign) num from l_session where playerid != 0 and expire > now()"));
$UsersOnline = $row->num;
$PageTitle = "BZflag CTF League: $SectionTitle";

$UserLevel='guest';
if( isAuthenticated() ) {
  $Authenticated = true;
  $UserName = $_SESSION['callsign'];
  $UserID = $_SESSION['playerid'];
  $UserLevel = $_SESSION['level'];
}

include ( 'template.php'  );

function getSection (&$phpFile, &$phpFunc, &$title){
// linkname => section title [php module, php function name]
$sectionInfo = array(
  'home' =>     array ('HOME' ),
  '404' =>    array ('Document not found'),
  'login' =>    array ('Log In'),
  'news' =>     array ('NEWS', 'news.php'),
  'newsadmin' => array ('News Page Administration', 'news.php', 'newsadmin'),
  'shame' =>     array ('Bans', 'shame.php'),
  'shameadmin' => array ('Bans Administration', 'shame.php', 'shameadmin'),
  'messages' => array ('BZ Mail'),
  'bzforums' => array ('BZ Forums'),
  'fights' =>   array ('Matches'),
  'teams' =>    array ('Teams'),
  'teaminfo' => array ('Team Information'),
  'leaveteam' => array ('Leave Team,'),
  'jointhisteam' => array ('Join Team', 'jointeam.php', 'jointeam'),  // "alias"
  'jointeam' => array ('Join Team'),
  'playerinfo' => array ('Player Profile'),
  'playeradmin' => array ('Player Profile EDIT', 'playerinfo.php', 'playerinfo'), // "alias"
  'sendmessage' => array ('Send Message'),
  'dismissteam' => array ('Dismiss Team'),
  'deleteplayer' => array ('Delete Player'),
  'online' => array ("Who's Online"),
  'banplayer' => array ('Ban Player'),
  'teamadmin' => array ('Team Administration'),
  'faq' => array ('Frequently Asked Questions'),
  'rules' => array ('Match Rules and How-tos'),
  'links' => array ('Links'),
  'linkadmin' => array ('Links Page Administration', 'links.php', 'linkadmin'),
  'contact' => array ('Contact Information'),
  'entermatch' => array ('Enter Match'),
  'editmatch' => array ('Edit/Delete Match', 'entermatch.php', 'entermatch'),
  'createteam' => array ('Create Team'),
  'register' => array ('Register'),
  'players' => array ('Players'),
  'invite' => array ('Invite'),
  'oppsumm' => array ('Opponent Summary'),
  'adminlist' => array ('List of Administrators'),
  'debuginfo' => array ('Debug info'),
  'visitlog' => array ('Visitor Log'),
  'admintext' => array ('Administrate Site Text'),
  'bulkmail' => array ('Bulk Mail'),
  'reviveteam' => array ('Revive a Deleted Team'),
  'runmaintenance' => array ('Run Site Maintenance'),
  'debug' => array ('Debugging Information'),
  'dispchangelog' => array ('Display Code Change Log'),
  'ipreport' => array ('Duplicate Player IPs'),
  'badpass' => array ('Possible password hack attempts'),
  'permissions' => array('Permissions'),
  'required' => array ('Enter REQUIRED Information'),
  'createaccount' => array ('Create New Account'),
  'standings' =>    array ('Standings'),
  'seasons' =>    array ('Seasons'),
  'season' =>    array ('Season Standings'),
  'addseason' => array ('Manage Season'),
);


  if( isset( $_POST['link'] ) )
    $sName = $_POST['link'];
  else if( isSet( $_GET['link'] ) )
    $sName = $_GET['link'];
  else
    $sName = 'home';

  if (!$sectionInfo[$sName])
    $sName = '404';

  // By now, we have a valid section.
  if (isset($sectionInfo[$sName][2])) 
    $phpFunc = 'section_' . $sectionInfo[$sName][2];
  else
    $phpFunc = 'section_' . $sName;

  if (isset($sectionInfo[$sName][1]))
    $phpFile = 'section/' . $sectionInfo[$sName][1];
  else
    $phpFile = 'section/' . $sName . ".php";
  $title = $sectionInfo[$sName][0];

  $GLOBALS['MODULE_NAME'] = substr($phpFunc,8);
}




function adminMenu (){
  //if ($GLOBALS['UserLevel']=='player' || $GLOBALS['UserLevel']=='guest')
  //  return;

  $admMenu = array (
    array ('entermatch::enter_match', 'Enter Match', 'entermatch', '', LINK_BOLD),
    array ('admintext::edit_homepage', 'Homepage', 'admintext', 'func=homepage'),
    array ('news::edit_news', 'News Edit', 'newsadmin'),
    array ('shame::edit_shame', 'HOS Edit', 'shameadmin'),
    array ('links::edit_links', 'Links Edit', 'linkadmin'),
    array ('admintext::edit_contacts', 'Contacts', 'admintext', 'func=contact'),
    array ('admintext::edit_faq', 'FAQ Edit', 'admintext', 'func=faq'),
    array ('admintext::edit_rules', 'Rules Edit', 'admintext', 'func=rules'),
    array ('adminlist::list_admins', 'Admin List', 'adminlist'),
    array ('runmaintenance::maintenance', 'Maintenance', 'runmaintenance'),
    array ('reviveteam::revive_team', 'Revive Team', 'reviveteam'),
    array ('visitlog::visit_log', 'Visits Log', 'visitlog'),
    array ('ipreport::ipreport', 'IP Report', 'ipreport'),
    array ('dispchangelog::disp_changelog', 'ChangeLog', 'dispchangelog'),
    array ('badpass::badpass', 'BadLogin', 'badpass'),
    array ('admintext::edit_todo', 'Scratchpad', 'admintext', 'func=todo'),
    array ('debug::debug', 'Debug', 'debug'),
    array ('permissions::permissions', 'Permissions', 'permissions'),
    array ('permissions::permissions', 'Roles', 'permissions','func=roles'),
    array ('createaccount::create_account', 'Create Acct', 'createaccount'),
    array ('addseason::add_season', 'Seasons', 'addseason'),
  );
  $colCount=0;
  $out = '<TABLE class=admback width=100%><TR><TD>';
  $out .= '<TABLE  align=center class=admbar cellpadding=0 cellspacing=0><TR>';
  foreach ($admMenu as $mi){
    if (isFuncAllowed ($mi[0])){
      $out .= '<TD align=center width=90><nobr>' 
      .htmlLink ($mi[1], $mi[2], $mi[3], $mi[4]) .'</nobr></td>';
      if ( (++$colCount%8)==0 )
        $out .= '</tr><TR>';
    }
  }
  $out .= '</tr></table></td></tr></table>';
  if( $colCount ) print $out;
}

?>
