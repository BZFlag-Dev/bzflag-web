<?php // $Id: helpers.php,v 1.10 2006/08/29 00:29:49 dennismp Exp $ vim:sts=2:et:sw=2

define (DEFBUT, 'defbut');
define (CLRBUT, 'clrbut');
define (ADMBUT, 'admbut');
define (DISBUT, 'disabled');


define (LINK_ALERT, 'linkalert');
define (LINK_NEW, 'linknew');
define (LINK_BOLD, 'linkbold');
define (LINK_DEAD, 'linkdead');


// temporary, until figuring out how to do with stylesheet !
function themeStyle ($theme){
  if (THEME_NAME=='dark'){
    if ($theme==LINK_ALERT)
      return 'style="color: #ff5555; text-decoration: blink"';
    if ($theme==LINK_NEW)
      return 'style="color: #ff5555;"';
  } else {
    if ($theme==LINK_ALERT)
      return 'style="color: #880000; text-decoration: blink"';
    if ($theme==LINK_NEW)
      return 'style="color: #880000;"';
  }
  if ($theme==LINK_BOLD)
    return 'style="font-weight: bold;"';
  if ($theme==LINK_DEAD)
    return 'class="dim"';
  return '';
}

function formURL ($link, $args=null){
  if ($link){
    $l = "?link=$link";
    if ($args)
      $l = $l . "&$args";
  }
  return "index.php$l";
}


function redirect ($link, $args=null){
  header("Location: http://" . $_SERVER['HTTP_HOST']
             . dirname($_SERVER['PHP_SELF']) .'/'. formURL ($link, $args));
//echo "Location: http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) .'/'. formURL ($link, $args);
  exit;
}


function htmlLink ($text, $link, $args=null, $theme=null){
  $style = themeStyle ($theme);
  return "<A $style HREF=\"". formURL($link, $args) ."\">$text</a>";
}



function teamLink ($name, $id, $stat, $class=null){
  if ($stat == 'deleted'){    // deleted team
    $style = themeStyle ($class);
    if ($style=='') $style = 'class="deadlink"';
    return "<font $style TITLE=\"Team '$name' has been deleted\">$name</font>";
  } else {
    return htmlLink ($name, 'teaminfo', "id=$id", $class);
  }
}



function playerLink ($pid, $callsign){
//  $style = themeStyle ($theme);
  if ($pid)
    return '<a href="'. formURL('playerinfo', "id=$pid") ."\">$callsign</a>";
  else
    return $callsign;
}

function seasonLink($name, $season_id,$button=null)
{
  if ($button)
    $name = "<img border=0 width=19 height=17 src=\"" . THEME_DIR . "arrow_$name.gif\">";
  return htmlLink ($name, 'season', "season_id=$season_id", null);
}

function matchesLink($season_id,$image = null)
{
  if ($image)
    $name = "<img border=0 width=19 height=17 src=\"" . THEME_DIR . "arrow_$image.gif\">";
  else
    $name = "<img border=0 width=19 height=17 src=\"" . THEME_DIR . "matches.gif\">";
  return htmlLink ($name, 'fights', "season_id=$season_id", null);
}


function htmlFlush (){
  echo "<BR>\n";
  for ($x=0; $x<128; $x++)
    echo "                \n";
  echo "<BR>\n";
  flush (STDOUT);
}


function htmlURLbutton ($buttext, $link, $args='', $class=null){
  if (!$class)
    $class = DEFBUT;
  if ($args)
    $and = '&';
  return "<TABLE cellpadding=0 cellspacing=1 class=$class><TR><TD>
    <A HREF=\"index.php?link=$link$and$args\"><nobr>$buttext</nobr></a></td></tr>
    </table>";
}

function htmlURLbutSmall ($buttext, $link, $args='', $class=null){
  if ($class==DISBUT) 
    return "<TABLE cellpadding=0 cellspacing=1 class=$class><TR>
      <TD style=\"font-size: 12px; padding-top: 1px; padding-bottom: 1px;\"> 
      <nobr>$buttext</nobr></td></tr></table>";
  if (!$class)
    $class = DEFBUT;
  if ($args)
    $and = '&';
  return "<TABLE cellpadding=0 cellspacing=1 class=$class><TR><TD>
      <A style=\"font-size: 12px; padding-top: 1px; padding-bottom: 1px;\" 
      HREF=\"index.php?link=$link$and$args\"><nobr>$buttext</nobr></a></td></tr></table>";
}




function htmlFormButton ($buttext, $name, $class=null){
  if (!$class)
    $class = DEFBUT;
  return "<INPUT onmouseover=\"butRoll(this, '$class', 1)\" 
      onmouseout=\"butRoll(this, '$class', 0)\"
      class='$class' type=submit name=\"$name\" value=\"$buttext\">";
}

function htmlFormReset ($buttext){
  $class = CLRBUT;
  return "<INPUT onmouseover=\"butRoll(this, '$class', 1)\" 
      onmouseout=\"butRoll(this, '$class', 0)\"
      class='$class' type=reset  value=\"$buttext\">";
}


function htmlFormButSmall ($buttext, $name, $class=null){
  if (!$class)
    $class = DEFBUT;
  return "<INPUT style=\"font-size: 12\" onmouseover=\"butRoll(this, '$class', 1)\" 
      onmouseout=\"butRoll(this, '$class', 0)\"
      class='$class' type=submit name=\"$name\" value=\"$buttext\">";
}


function htmlOption ($val, $desc, $sel, $class=''){
  $ss = $val==$sel ? ' SELECTED' : '';    
  $cl = $class ? " class='$class'" : '';
  echo "<option$cl value=\"$val\"$ss>$desc</option>\n";
}



function htmlMiniTable ($colAry, $spacing=0, $tabOpts=''){
  echo "<TABLE $tabOpts><TR>";
  $x=0;
  while (isset($colAry[$x])){
    echo "<TD>{$colAry[$x++]}</td>";
    if ($spacing && $colAry[$x])
      echo "<TD width=$spacing></td>";
  }
  echo '</tr></table>';
}

function htmlPrint_r ($a, $msg=null){
  echo '<pre>';
  if ($msg)
    echo "[[$msg]]<BR>";
  print_r ($a);
  echo '</pre>';
}


function htmlRowClass (&$rowNum){
  if ($rowNum++ & 1)
    return '<TR class=rowOdd>';
  else
    return '<TR class=rowEven>';
}

/****
  'Common' library stuff ...
****/

function periodMsg ($days){
  return sprintf ("%2.1f months", ($days/30.5)+0.02);
}

function text_disp ($txt, $nl2br=true){
  $ret = str_replace ("<IMGPATH>", THEME_DIR , $txt);
  $ret = str_replace ("<SMILEYPATH>", THEME_DIR.'smilies/' , $ret);
  if ($nl2br)
    $ret = nl2br($ret);
  return $ret;
}

// convert date(YYYY-MM-DD)  &  time (HH:MM[:SS]) to unix - check for validity
function dt2Unix ($dte, $tme){
  if (sscanf ($dte, "%d-%d-%d", &$y, &$m, &$d) != 3)
    return -1;
  if (strlen($tme)!=5 && strlen($tme)!=8)
    return -1;
  if (($x=sscanf($tme, "%d:%d:%d", &$h, &$min, &$s))!=2 && $x!=3)
    return -1;
  if ($y<1900 || $y>2200 || !checkdate($m, $d, $y))
    return -1;
  if ($h<0 || $h>23 || $m<0 || $m>59 || $s<0 || $s>59)
    return -1;
  return strtotime ($dte . ' ' . $tme);
}

// same as time(), but GMT instead of localtime
function gmTime (){
  return strtotime (gmdate ("Y-m-d H:i:s"));
}

function errorPage ($msg){
  echo "<BR><CENTER><TABLE width=80%><TR class=errorPage><TD align=center> Error: $msg";
  echo '</td><tr><tr><td align=center><BR>This can occur if you have specified an invalid URL, or if you are not 
        authorized to perform the function (or your session timed out).</td></tr></table>';
}

function snFormInit (){
  if (!isset($_SESSION['seqnum']))
    $_SESSION['seqnum'] = 1;
  echo "<input type=hidden name='snckdup' value={$_SESSION['seqnum']}>";
}

function snForm (){
  echo "<input type=hidden name='snckdup' value={$_POST['snckdup']}>";
}

// do this check right before posting a transaction
function snCheck ($redirLink, $args=null){
  $sn = $_POST['snckdup'];
  if (!isset($sn))
    $sn = $_GET['snckdup'];

  if ($sn != $_SESSION['seqnum'])
    redirect ($redirLink, $args);
  $_SESSION['seqnum']++;    
}

function smallFlag ($flagname, $countryname=null){
  if ($flagname)
    return "<img TITLE='$countryname' border=1 width=30 height=18 src='". FLAG_DIR ."cs-$flagname.gif'>";
  else 
    return '';
}

// if 'fromid' is zero, it's an admin msg
function sendBzMail ($fromid, $toid, $sub, $msg, $toteam=false, $htmlok=false) {
  if ($fromid == 0)     // if site administrative message
    $msg = '<TABLE align=center><TR><TD><font size=+1><nobr><B>League Site System Message
        </b></nobr></font></center><HR></td></tr></table>' . $msg;
  $now = gmdate("Y-m-d H:i:s");
  $subject = strip_tags(addslashes($subject));
  $msg = addslashes($msg);
  $sub = addslashes($sub);
  $hok = 0;
  if ($htmlok)
    $hok=1;


  if ($toteam){       // to entire team...
    $res = mysql_query("select id from l_player where team=$toid");
    while($obj = mysql_fetch_object($res)) {
      session_refresh($obj->id);
      sqlQuery("insert into l_message (toid, fromid, datesent, subject, msg, status, team, htmlok)
        values ($obj->id, $fromid, '$now', '$sub', '$msg', 'new', 'yes', $hok)");
    }
  } else {
      session_refresh($toid);
      sqlQuery("insert into l_message (toid, fromid, datesent, subject, msg, status, team, htmlok)
      values ($toid, $fromid, '$now', '$sub', '$msg', 'new', 'no', $hok)");
  }
}

function sendBzMailToAll ($fromid, $toRole, $sub, $msg, $htmlok=false) {
  if ($toRole > 0)
    $res = sqlQuery ("select id from l_player where role_id=$toRole AND status!='deleted'");
  else
    $res = sqlQuery ("select id from l_player where status!='deleted'");
  while ($row = mysql_fetch_object($res))
    sendBzMail ($fromid, $row->id, $sub, $msg, false, $htmlok);
}

function deleteTeam ($id){
  $row = mysql_fetch_object (mysql_query ("select * from l_team where id=$id"));
  if (!$row || $row->status == 'deleted')
    return false;

  // Destroy the team
  sendBzMail (0, $id, 'Team dismissed!', 'Your team has been dismissed, so you are teamless now. :(' , true);

  // Teamless players
  mysql_query("update l_player set team=0 where team=$id");

  // Remove team
  if ( mysql_num_rows(sqlQuery("SELECT id FROM bzl_match where team1=$id OR team2=$id")) > 0){
    // team has matched!  Do not physically delete it.
    mysql_query("UPDATE l_team SET status = 'deleted', status_changed=now(), leader=0 WHERE id=$id");
  } else
    sqlQuery ("DELETE FROM l_team where id=$id");


  // ONLY IF MY TEAM !!!!!!  (not if admin dismisses)
  if ($_SESSION['teamid'] == $id  ){
    // Update session variables
    $_SESSION['teamid'] = 0;
    $_SESSION['leader'] = 0;
  }
  return true;
}

function deletePlayer($id){
  $row = mysql_fetch_object (mysql_query ("select * from l_player where id=$id"));
  if ($row->status == 'deleted'){
    echo 'Player already deleted.';
    return false;
  }

  $oldName = mysql_escape_string ($row->callsign);
  $newName = substr ($oldName, 0, 20) . ' (DELETED)';
  if (!sqlQuery ("update l_player set callsign='$newName', team=0, role_id=". NEW_USER_PERMISSION .", status='deleted',  
     comment='Previous callsign: $oldName\nDeleted on: " .gmdate('M d Y H:i'). "\n' WHERE id=$id")){
    echo "Unable to delete player $row->callsign!<BR>\n";
    return false;
  }
  sqlQuery ("DELETE FROM l_message WHERE toid = $id");
  sqlQuery ("DELETE FROM " .TBL_VISITS." WHERE pid = $id");

  return true;
}

function simpleNVparser ($filename, $nva){
  $res = fopen ($filename, "r", FALSE);
  $dat = fread ($res, 5000);
  fclose ($res);
  
  if (!res || !$dat)
    return;

  $line = strtok ($dat, "\n");
  while ($line) {
    $f = strcspn ($line, "#=");
    if ($f>0 && $line{$f}=='='){
      $name = trim(substr($line, 0, $f));
// doing this the 'hard way' to get around performing 5 substr()s, etc.
// is more efficient in 'C', dunno about php !
      $vl = strlen ($line);
      $x = $f+1;
      while ($line{$x}==' ' && $x<$vl)    // skip spaces
        ++$x;
      if (($c=$line{$x}) == '"'){
        $s = ++$x;
        while ($line{$x}!='"' && $x<$vl)
          ++$x;
        $val = substr ($line, $s, $x-$s);
      } elseif ($x==$v1 || $c=='#'){
        $val = '';
        continue;     
      } else {
        $s = $x;
        while (($c=$line{$x})!=' ' && $c!='#' && $x<$vl)
          ++$x;
        $val = substr ($line, $s, $x-$s);
      }

    $nva[$name] = $val; 
    }
    $line = strtok ("\n");
  }
}

function nowDateTime($database_time = false)
{
  if ($database_time == false)
    return gmdate("Y-m-d H:i:s");
  else
  {
    $res = sqlQuerySingle("select now() d");
    return $res->d;
  }
}

function nowDate($database_time = false)
{
  if ($database_time == false)
    return gmdate("Y-m-d");
  else
  {
    $res = sqlQuerySingle("select CURDATE() d");
    return $res->d;
  }
}

?>