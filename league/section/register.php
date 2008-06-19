<?php // $Id$ vim:sts=2:et:sw=2

require_once ('lib/formedit.php');
define ('PROXY_LOG', '/usr/web/my.bzflag.org/league/logs/proxy.log');

function section_register (){
  $se = new FormEdit ();
  $se->trimAll();
  $se->stripAll();

// meno 2007/05/01 ....
  if (PRIVATE_LEAGUE != 0){
    return errorPage ('Nuh uh, no reg for you');
  }
  $headers = apache_request_headers();
  $fromIP = $_SERVER['REMOTE_ADDR'];
  $fd = fopen (PROXY_LOG, 'a');
  fwrite ($fd, "\n----------------------- ". date('r') . " --------------------\n");
  fwrite ($fd, "*** FROM IP: {$_SERVER['REMOTE_ADDR']}\n");
  foreach ($headers as $n=>$v)
    fwrite ($fd, "$n: $v\n");
  foreach ($headers as $header => $value) {
    if (strncmp($fromIP, '172', 3)==0 || strcasecmp ($header, 'X-Forwarded-For')==0 || strcasecmp($header,'Via')==0){
      fwrite ($fd, "************************* PROXY DETECT ***********************\n");
      fclose ($fd);
      return errorPage ("We're sorry, currently we cannot accept new registrations from AOL users, or users connecting through a proxy.");
    }
  }
  fclose ($fd);


  if($_SESSION['callsign'])
    return errorPage ('You are already registered with the league system.');

  if ($se->SUB){
    section_register_validate($se);
    if (!$se->isError()){
      section_register_doSubmit($se);
      return;
    }
  }
  section_register_presentEditForm ($se);
}





function section_register_validate (&$se){
  if ( ($msg = $se->validateName ($se->callsign, 'Callsign')) != '')
    $se->setError ('callsign', $msg);   
  else if (sqlQuerySingle("select callsign from l_player where callsign='$se->callsign' and status!='deleted'") )
    $se->setError ('callsign', 'That callsign is already used by another player.');   
  if ($se->password != $se->password2){
    $se->setError ('password', 'The passwords do not match!');
    $se->password = $se->password2 = '';
  } else if (strlen($se->password) < 3){
    $se->setError ('password', 'Password must be at least 3 characters long');
    $se->password = $se->password2 = '';
  }
  if ($se->country == null)
    $se->setError ('country', 'Please select your location (country)');
  if (!$se->validateEmail ($se->email))
    $se->setError ('email', 'Enter a valid email address');   
}




function section_register_doSubmit (&$se){
  // player's utc timezone offset (aquired from user's client via jscript)
  $uz = 0 - ($_POST['tzoffset']/60);

  $cypher = crypt($f_password1);
  $call = addSlashes ($se->callsign);
  $pass = md5 ($se->password);

  sqlQuery("insert into l_player (callsign, team, status, role_id, md5password, created, last_login,
          utczone, country, email) 
      values( '$call', 0, 'registered', " . NEW_USER_PERMISSION . ", '$pass', now(), now(), 
          '$uz', '$se->country', '$se->email')");

  // Assign session variables
  $_SESSION['callsign'] = $se->callsign;
  $_SESSION['playerid'] = mysql_insert_id();

  // Insert an entry into the statistics table
  $gmnow = gmdate("Y-m-d H:i:s");
  sqlQuery('insert into '.TBL_VISITS." (ts, pid, ip) 
        values ('$gmnow', {$_SESSION['playerid']}, '{$_SERVER['REMOTE_ADDR']}')");

  $_SESSION['teamid'] = 0;
  $_SESSION['leader'] = 0;

  sendBzMail (0, $_SESSION['playerid'], 'WELCOME, '.$_SESSION['callsign'], 
      "Thank you for registering, and welcome to the league!<BR>
      Please read the FAQ, and edit your profile to make it easier for others to find you (this REALLY helps for organizing matches).<br>
      Now you can join a team, or create a new team and recruit members.
      <BR>See you on the battlefield!"  );
  echo '<center>Welcome onboard, <a href="index.php?link=playerinfo&id='.$_SESSION['playerid'].'">'.$_SESSION['callsign'].'</a>.<br><br>
    You are now a registered user on the league system, and are allowed to join a team, or create a new one.<br>
    Please read the F.A.Q. to know how.
    <hr>
    This is your personal information:<br>
    Login: '.$_SESSION['callsign'].'<br>
    Password: '.$se->password.'<br>
    <hr>
    If you lose your password, the only way for you to have a new one is to contact one of the site admins.<br>
    See <a href="index.php?link=contact">the contact page</a> if you wan'."'".'t to do that.
    </center>';

  $res = mysql_query("SELECT name FROM bzl_roles WHERE id = " . NEW_USER_PERMISSION ) or die(mysql_error());
  if( $row = mysql_fetch_array($res)) 
    $_SESSION['level']    = $row[0];
  else
    $_SESSION['level']    = "player";

  session_refresh();
}




function section_register_presentEditForm (&$se){
  // New user
  echo '<BR><center><font size=+1>Welcome to the <a href="http://BZFlag.org/" target="_blank">BZFlag</a>
    Capture The Flag League registration page!<br><br>
    If you haven'."'".'t read the F.A.Q. yet, please <a href="index.php?link=faq"><b>do it now</b></a>
    .<p></font> When you are ready, please fill in the following information<BR>
     to setup a new account in the league system:<HR>';

  echo '<script type="text/javascript">
    now = new Date();
    document.write ("<input type=hidden name=tzoffset value=" + now.getTimezoneOffset() +">");
    </script>';

  echo '<CENTER>';
  echo '<BR>';
  $se->formStart (array (link, id), 'ppedit');

  $se->formDescript ('Enter your BZflag callsign here.  Your callsign is also
    your user name for logging in to this site.', ST_FORMDESC);
  $se->formText ('callsign', 'Callsign', 20, 40, ST_FORMREQ);

  $se->formRow ('<HR>');
  $se->formDescript ('Enter your desired password here.', ST_FORMDESC);
  $se->formPassword ('password', 'Password', 8, 8);
  $se->formPassword ('password2', 'Verify', 8, 8);

  $se->formRow ('<HR>');
  $se->formDescript ('Please enter your location.', ST_FORMDESC);
  $se->formSelector ('country', 'Country', 'select name, numcode from bzl_countries order by name', array('-- PLEASE SELECT --'=>null), null, ST_FORMREQ );

  $se->formRow ('<HR>');
  $se->formDescript ('Enter your email here.  This will NOT be shown to anyone else 
    (unless you choose to do so in the profile edit), but will be used to reset your 
    password in case you forget it.', ST_FORMDESC);
  $se->formText ('email', 'E-mail', 38, 40, ST_FORMREQ);

  $se->formRow ('<HR>');
  echo '<tr><td align=center colspan=2>'
    . htmlFormButton ('Submit', 'SUB') . '&nbsp;&nbsp;'
    . htmlFormButton ('Cancel', 'CAN', CLRBUT)
    .'</td></tr>';

  $se->formEnd();

}



?>
