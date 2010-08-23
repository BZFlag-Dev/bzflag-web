<?php // $Id $ vim:sts=2:et:sw=2

require_once ('lib/formedit.php');


function section_createaccount_permissions () {
  return array(
    'create_account' => 'Create an Account'
  );
}

function section_createaccount (){
  $se = new FormEdit ();
  $se->trimAll();
  $se->stripAll();

  if ($se->SUB){
    section_createaccount_validate($se);
    if (!$se->isError()){
      section_createaccount_doSubmit($se);
      return;
    }
  }
  section_createaccount_presentEditForm ($se);
}





function section_createaccount_validate (&$se){
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
}




function section_createaccount_doSubmit (&$se){
  // player's utc timezone offset (aquired from user's client via jscript)
  $uz = 0 - ($_POST['tzoffset']/60);

  $cypher = crypt($f_password1);
  $call = addSlashes ($se->callsign);
  $pass = md5 ($se->password);

  sqlQuery("insert into l_player (callsign, team, status, role_id, md5password, created, last_login,
          utczone, country, email) 
      values( '$call', 0, 'registered', " . NEW_USER_PERMISSION . ", '$pass', now(), now(), 
          '$uz', '999', '$se->email')");

  sendBzMail (0, mysql_insert_id(), 'WELCOME, '.$se->callsign, 
      "Welcome to the league!<BR>
      Please read the FAQ, and edit your profile to make it easier for others to find you (this REALLY helps for organizing matches).<br>
      Now you can join a team, or create a new team and recruit members.
      <BR>See you on the battlefield!"  );
  echo '<center><BR>ACCOUNT CREATED:<br>
    Login: '.$se->callsign.'<br>
    Password: '.$se->password.'<br></center>';
}




function section_createaccount_presentEditForm (&$se){
  // New user
  echo '<BR><center><font size=+1>Create a new player account.<br><br><HR>';
  echo '<script type="text/javascript">
    now = new Date();
    document.write ("<input type=hidden name=tzoffset value=" + now.getTimezoneOffset() +">");
    </script>';

  echo '<CENTER>';
  echo '<BR>';
  $se->formStart (array (link, id), 'ppedit');

  $se->formDescript ('Enter the BZflag callsign here.  The callsign is also 
    the user name for logging in to this site.', ST_FORMDESC);
  $se->formText ('callsign', 'Callsign', 20, 40, ST_FORMREQ);

  $se->formRow ('<HR>');
  $se->formDescript ('Enter the desired password here.', ST_FORMDESC);
  $se->formPassword ('password', 'Password', 8, 8);
  $se->formPassword ('password2', 'Verify', 8, 8);


  $se->formRow ('<HR>');
  echo '<tr><td align=center colspan=2>'
    . htmlFormButton ('Submit', 'SUB') . '&nbsp;&nbsp;'
    . htmlFormButton ('Cancel', 'CAN', CLRBUT)
    .'</td></tr>';

  $se->formEnd();
}


?>
