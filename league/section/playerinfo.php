<?php // $Id$ vim:sts=2:et:sw=2

require_once ('lib/formedit.php');

function section_playerinfo_permissions() {
  return array(
    'edit_any_players' => 'Allow user edit any player'
  );
}


function section_playerinfo ( ){
  $editAny = isFuncAllowed('edit_any_players');
  $se = new FormEdit ();
  $se->trimAll();
  $se->stripAll();

  if (!$se->checkRequired (array ('id', 'link')))
    return errorPage ('missing argument');

  echo '<BR>';
  $query="select P.id, P.callsign, P.status, P.comment, 
      P.logo, T.name as teamname, T.id teamid, T.leader, r.name as level,
      C.flagname, C.name as countryname, C.numcode as country,
      P.state, S.abbrev as stateabbr, P.logobg,
      P.email, P.emailpub, P.aim, P.altnik1, P.altnik2,
      P.ircnik1, P.emailpub, P.utczone, P.zonename, P.icq, P.city,
      P.yim, P.msm, P.jabber, P.md5password, 
      unix_timestamp(P.created) as created, 
      unix_timestamp(P.last_login) as last_login,
      r.id as role_id
      from (l_player P, bzl_roles r )
      left join l_team T on P.team = T.id
      left join bzl_countries C on P.country = C.numcode
      left join bzl_states S on P.state = S.id
      where P.id = '{$se->id}'
      and r.id = P.role_id 
      ";

  $se->setDataRow (mysql_fetch_assoc (sqlQuery ($query)));

  if($se->f_cancel_x) {
    $se->link = "playerinfo";
  }

  if($se->link == 'playeradmin') {    // present edit form
    // security check ...
    if ($se->id!=$_SESSION['playerid'] && !$editAny){
      errorPage ('You are not authorized to edit this profile.');
      section_playerinfo_displayPlayer($se);
      return;
    }

    if ($se->f_ok_x){
      section_playerinfo_validate($se);
      if (!$se->isError()){
        section_playerinfo_doSubmit($se);
        $se->setNextState(FESTATE_INITIAL );
        $se->setDataRow (mysql_fetch_assoc (sqlQuery ($query)));
        section_playerinfo_displayPlayer($se);
        return;
      }
    }
    $se->setNextState (FESTATE_SUBMIT);
    section_playerinfo_presentEditForm ($se);
  } else {
    section_playerinfo_displayPlayer($se);
  }
}




function section_playerinfo_validate (&$se){
  if(strlen($se->password) || strlen($se->password2)) {
    // Password entered, let's check
    if($se->password != $se->password2) {
      $se->setError ('password', "Passwords don't match");
      $se->password = "";
      $se->password2 = "";
    }
  }

  if ($se->isChanged ('callsign')){
    if ( ($msg = $se->validateName ($se->callsign, 'Callsign')) != ''){
      $se->setError ('callsign', $msg);   
      $se->revert('callsign');
    } else {
      // Check callsign unicity
      $res = mysql_query("select callsign from l_player where id!=$se->id and callsign=\"".$se->callsign."\"");
      if(mysql_num_rows($res) != 0) {
        $se->setError ('callsign', "The callsign [$se->callsign] already exists.");
        $se->revert('callsign');
      }
    }
  }

  // Check logo
  if ($se->logo != ''){
    $msg = section_playerinfo_checkLogo($se->logo, 500, 400, 100000); 
    if($msg != '') 
      $se->setError ('logo', "LOGO: $msg");
  }

  if ($se->isChanged ('role_id') && $se->oldVal('role_id')==ADMIN_PERMISSION ){
    // Make sure we're not losing the last admin !!
    $res = mysql_query("select 1 from l_player where id!=$se->id and role_id=" . ADMIN_PERMISSION);
    if(mysql_num_rows($res) == 0) {
      $se->setError ('level', "You can not UNadmin yourself. There are no other admins!");
      $se->revert('level');
    }
  }
}




function section_playerinfo_doSubmit (&$se){
  $fc = $se->fieldsChanged('logo, logobg, callsign, country, state, email, aim, yim, 
    msm, jabber, altnik1, altnik2, ircnik1, emailpub, utczone, zonename, city, 
    icq, comment, role_id');

  if ($se->isChanged('password'))
    $cypher = "md5password=md5('$se->password')";


  if (($sets = section_playerinfo_commaCat($fc, $cypher)) == null){
    echo $se->feedback ('NO changes made');
  } else {
    echo $se->feedback ('Changes submitted, thank you.');
    // Update the table
    sqlQuery("update l_player set $sets where id=". $se->id, false);
    $se->link="playerinfo";
    // Check if I changed my privileges ;-)
    //if($se->isChanged('level') && $_SESSION['playerid']==$se->id) {
     // $_SESSION['level'] = $se->level;
    //}
    // Update callsign if I changed mine
    if($se->id == $_SESSION['playerid'] ) {
      $_SESSION['callsign'] = $se->callsign;
    }

    session_refresh();
  }
  return true;
}


// style classnames:
define (ST_FORMDESC, 'edtdesc');
define (ST_FORMREQ, 'edtreq');

function section_playerinfo_presentEditForm (&$se){
  echo '<CENTER>';
  echo '<BR>';
  $se->formStart (array (link, id), 'ppedit');

  $se->formDescript ('You can change your callsign here.  Remember that it is also 
    your user name for logging in to this site.  If you have other callsigns that you use while
    playing BzFlag, you may enter them in the "alternate" fields.', ST_FORMDESC);
  $se->formText ('callsign', 'Callsign', 20, 40, ST_FORMREQ);
  $se->formText ('altnik1', 'Alternate', 20, 30);
  $se->formText ('altnik2', 'Alternate', 20, 30);

  $se->formRow ('<HR>');
  $se->formDescript ('To keep your current password, leave the following two fields blank.', ST_FORMDESC);
  $se->formPassword ('password', 'Password', 8, 8);
  $se->formPassword ('password2', 'Verify', 8, 8);

  $se->formRow ('<HR>');
  $se->formDescript ('Please enter your location below.  Note that <b>the country is required</b>.
      Other information is not required, but if you live in the United States or Canada, please
      select your state or province.', ST_FORMDESC);
  $se->formSelector ('country', 'Country', 'select name, numcode from bzl_countries order by name', array('-- PLEASE SELECT --'=>-1), null, ST_FORMREQ );
  $se->formSelector ('state', 'State/Prov', 'select name, id from bzl_states order by name', array(''=>-1), '(If USA or Canada)' );
  $se->formText ('city', 'City', 20, 20);

  $se->formSelector ('utczone', 'Time zone', null, section_playerinfo_makeZoneArray(), '');
  $se->formText ('zonename', 'Zone name', 8, 8);


  $se->formRow ('<HR>');
  $se->formDescript ('Enter some contact information.  The <b>Email field is required</b>, 
    but will <b>NOT</b> be shown to anyone else unless you check the "show email" box below.
    All other fields are optional, but please fill in as many as are appropriate, as
    this <i>really</i> helps with arranging matches, team meetings, etc.', ST_FORMDESC);
  $se->formText ('email', 'E-mail', 40, 40, ST_FORMREQ);
  $se->formText ('aim', 'AIM', 20, 20);
  $se->formText ('yim', 'YIM', 20, 20);
  $se->formText ('msm', 'MSM', 20, 30);
  $se->formText ('icq', 'ICQ', 10, 12);
  $se->formText ('jabber', 'Jabber', 20, 30);
  $se->formText ('ircnik1', 'iRc nick', 20, 20);

  $se->formCheckbox ('emailpub', 'Show email', 'Y', 'N',
    'Check this ONLY if you want your email shown to others. If so, it will be anti-spam encoded.');


  $se->formRow ('<HR>');
  $se->formDescript ('If you would like a logo (image) to show with your profile, enter a
      URL to the image below.  The url should start with "http://".  The maximum image 
      size allowed is 400x300, or 60KB.', ST_FORMDESC);
  $se->formText ('logo', 'Logo URL', 40, 200);

  $se->formSelector ('logobg', 'Background', null, 
      array('none'=>'', 'white'=>'ffffff', 'light gray'=>'cccccc', 'dark gray'=>'555555', 'black'=>'000000'), 
      'Choose a background color for displaying your logo');

  if($se->logo != "")
    $se->formRow ('<BR>CURRENT IMAGE:<BR>'. section_playerinfo_dispLogo($se->logo, $se->logobg));

  $se->formRow ('<HR>');
  $se->formDescript ('Enter any additional information that you would like displayed with 
      your profile here. HTML is not allowed, and will be disabled.', ST_FORMDESC);
  $se->formTextArea ('comment', 'Comment', 50, 6);

  $se->formRow ('<HR>');
  // Admins can change users level
  if(isAdmin()) {
    $res = mysql_query("SELECT id, name FROM bzl_roles ORDER BY name") or die(mysql_error());
    while($row = mysql_fetch_assoc($res)) 
      $options[$row['name']] = $row['id'];
    $se->formSelector ('role_id', 'User level', null, $options, 'Only Admins can set this.' );
    $se->formRow ('<HR>');
  }


  echo '<tr><td align=center colspan=2>'
    . htmlFormButton ('Submit', 'f_ok_x') . '&nbsp;&nbsp;'
    . htmlFormButton ('Cancel', 'f_cancel_x', CLRBUT)
    .'</td></tr>';


  $se->formEnd();
}




function section_playerinfo_displayPlayer (&$se){
  $s_level    = $_SESSION['level'];
  $s_logedin  = isAuthenticated();
  $s_playerid = $_SESSION['playerid'];
  $s_leader   = $_SESSION['leader'];
  $s_teamid   = $_SESSION['teamid'];
  $editAny    = isFuncAllowed('edit_any_players');


  echo '<table width=90% align=center border=0 cellspacing=0 cellpadding=1>
    <tr><td class=playername align=center>'.$se->callsign.'<BR><BR></td></tr>';
  // Logo if any
  if($se->logo != "")
    echo '<tr><td align=center>'. section_playerinfo_dispLogo ($se->logo, $se->logobg) .'<hr></td></tr>';

  // Bio if any
  if($se->comment != "")
    echo "<tr><td class=playerbio>".nl2br($se->comment)."<hr></td></tr>";

  // misc info ....
  echo '</td></tr><TR><TD><table align=center><TR><TD width=100 valign=top align=left>';
  if ($se->flagname)
    echo '<img src="'. FLAG_DIR. "c-$se->flagname.gif\">";
  echo '</td><TD><TABLE>';


  // Team if any
  if($se->teamname != "") {
    echo "<tr><td>";
    if($se->leader == $se->id)
      $d = '<nobr>Leader (<img src="'.THEME_DIR.'leader.gif">) of team</nobr>';
    else
      $d = "<nobr>Member of team</nobr>";
    section_playerinfo_tab2 ($d, htmlLink ($se->teamname, 'teaminfo', "id=$se->teamid"));
  } else {
    echo "<tr><td align=center colspan=2>Does not belong to any team</td></tr>";
  }

  if ($se->altnik1 || $se->altnik2){
    if ($se->altnik1 && $se->altnik2){      
      $plural = 's';
      $niks = $se->altnik1 .', &nbsp;&nbsp;'. $se->altnik2;
    } else if ($se->altnik1)
      $niks = $se->altnik1;
    else
      $niks = $se->altnik2;
    section_playerinfo_tab2 ("<nobr>Alternate callsign$plural</nobr>", $niks);  
  }

  if ($se->countryname){
    $loc = $se->countryname;
    if ($se->stateabbr){
      $loc .= '&nbsp;&nbsp;(';
      if ($se->city)
        $loc .= $se->city . ', ';
      $loc .= "$se->stateabbr)";
    }
    section_playerinfo_tab2 ('Location', $loc);
  }
  
  if ($se->utczone || $se->zonename)
    section_playerinfo_tab2 ('Time zone', 'GMT '. section_playerinfo_numPlus($se->utczone) ."&nbsp;&nbsp;($se->zonename)");

  section_playerinfo_tab2 ('Site Member Since', date ('Y-m-d',$se->created));
  if (isset ($se->last_login))
    section_playerinfo_tab2 ('Last login', date ('Y-m-d H:i', $se->last_login));
  
  echo '<TR><TD colspan=2><HR></td></tr>';
  if ($se->emailpub=='Y')
    section_playerinfo_tab2 ('email', section_playerinfo_obsMail ($se->email));
  section_playerinfo_tab2 ('AIM', $se->aim);
  section_playerinfo_tab2 ('IRC', $se->ircnik1);
  section_playerinfo_tab2 ('ICQ', $se->icq);
  section_playerinfo_tab2 ('YIM', $se->yim);
  section_playerinfo_tab2 ('MSM', $se->msm);
  section_playerinfo_tab2 ('Jabber', $se->jabber);

  echo '</table></td><TD width=50></td></tr></table></tr>';

  // Frequentation statistics
  if ($se->status!='deleted'){
    echo '<tr><td><BR><BR>';
    section_playerinfo_Frequentation($se->id);
    echo '<BR></td></tr>';
  }


  if ($se->status != 'deleted'){
    // Send a message to this player (but not to myself!)
    if($s_logedin && ($s_playerid != $se->id)) {
      echo '<tr><td><hr></td></tr><tr align=center><td><TABLE><TR><TD>'. 
           htmlURLbutton ('SEND BZmessage', 'sendmessage', "pid=$se->id");
      // If I am a team leader, and my team is not full, I can invite him
      if (isAuthenticated() && $_SESSION['leader']) {
        // Check if my team full
        $team = mysql_fetch_object(mysql_query("select count(*) as num from l_player where team=$s_teamid"));
        if($team->num < 20){
          $mytn = queryGetTeamName( $s_teamid );
          echo '<TD width=5></td><TD>'. htmlURLbutton ("INVITE to $mytn", 'invite', "id=$se->id") .'</td>';
        }
      } 
      echo '</tr></table></td></tr>';
    }

    // Administrators and owner can edit a player
    if($s_logedin&&($editAny||($s_playerid == $se->id))) {
      if( $editAny && ($s_playerid != $se->id))
        $class = ADMBUT;
      echo '<tr><td align=center><hr><TABLE><TR><TD>'
           .htmlURLbutton ('Edit Profile', 'playeradmin', "id=$se->id&edt_st=1", $class);
      if (isFuncAllowed('visitlog::visit_log'))
        echo '</td><TD width=5></td><TD>'. htmlURLbutton ('Visits', 'visitlog', "id=$se->id", ADMBUT);
      if (isFuncAllowed('deleteplayer::delete_player') && $se->role_id!=ADMIN_PERMISSION )
        echo '</td><TD width=5></td><TD>'. htmlURLbutton ('DELETE Player', 'deleteplayer', "id=$se->id", ADMBUT);
      echo '</td></tr></table></td></tr>';
    }
  }
  echo "</table>";
}


function section_playerinfo_dispLogo ($url, $bg){
  if ($bg){
    return "<TABLE cellpadding=0 cellspacing=0 style='border: outset 4px #888; padding:0' 
      bgcolor=#$bg><TR><TD><img src='$url'></td></tr></table>";
  } else 
    return "<img src='$url'>";  
}


function section_playerinfo_obsMail ($em){
  $ret = str_replace ('@', '[at]', $em);
  return str_replace ('.', '[dot]', $ret);
}


function section_playerinfo_makeZoneArray (){
  $a = array();
  for ($x=-12; $x<13; $x++)
    $a[$x<0?"GMT $x hours":"GMT +$x hours"] = $x; 
  return $a;
}



function section_playerinfo_tab2 ($desc, $val){
  if ($val)
    echo "<TR><TD align=right><b><nobr>$desc:</nobr></b>&nbsp;</td><TD>&nbsp;$val</td></tr>";
}


function section_playerinfo_Frequentation ($pid) {
  $res = mysql_query('select substring(ts, 12, 2) hour, count(*) hits
    from '. TBL_VISITS ." where pid=$pid group by 1 order by 1");

  $tot = mysql_fetch_object(sqlQuery(
    'select count(*) num from '. TBL_VISITS ." where pid=$pid"));
  echo '<table border=1 cellspacing=0 cellpadding=1 align=center><tr>
      <td class=tabhead colspan=24 align=center>
      Frequentation statistics (hours are grayed, and GMT)</td></tr><tr class=freqhours>';

  for($i = 0 ; $i < 24; $i++) {
    $visit[$i] = 0;
    echo '<td align=center>'.$i.'</td>';
  }
  while($row = mysql_fetch_object($res)) {
    $hour = (int)$row->hour;
    $visit[$hour] = $row->hits;
  }
  echo '</tr><tr>';
  for($i=0; $i<24; $i++) {
    if($visit[$i] > $tot->num / 24 )
      echo '<td class=freqhigh align=center>&nbsp;'.$visit[$i].'&nbsp;</td>';
    else 
      echo '<td align=center>&nbsp;'.$visit[$i].'&nbsp;</td>';
  }
  echo '</tr></table>';
}


function section_playerinfo_numPlus ($num){
  if ($num >= 0)
    return "+$num";
  return "$num";
}

function section_playerinfo_commaCat ($s1, $s2){
  if (!$s1 && !$s2)
    return null;
  if (!$s1)
    return $s2;
  if (!$s2)
    return $s1;
  return $s1 .','. $s2;
}

function section_playerinfo_checkLogo ($url, $maxwidth, $maxheight, $maxbytes){
  if (strncmp ($url, "http://", 7))
    return ('Invalid url');
  return '';
}
?>
