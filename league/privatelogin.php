<?php  // $Id: privatelogin.php,v 1.2 2005/05/06 15:49:49 menotume Exp $


$vars = array('f_ok','f_call','f_pass');

foreach($vars as $var)
  $$var = isset($_POST[$var]) ? $_POST[$var] : $_GET[$var];


if($f_ok) {
  if (!doSubmit ($f_call, $f_pass)){
    // Unknown callsign
    echo "<BR><div class=error><CENTER>Wrong callsign or password</div>";
    $gmnow = gmdate("Y-m-d H:i:s");
    sqlQuery ("INSERT INTO l_badpass (gmtime, ip, name)  VALUES ('$gmnow', '{$_SERVER['REMOTE_ADDR']}', '$f_call')");
    sleep (2);  // deter script-kiddies
    displayForm ($f_call);
  } else {
    redirect ('login');
    exit;
  }
} else {
  displayForm ();
}


// returns true if sucessful
function doSubmit ($call, $pass) {
  // Check the password
  $res = mysql_query("select p.id, p.callsign, p.password, p.md5password, 
         unix_timestamp(p.last_login) as last_login, p.utczone, p.country
         from l_player p,bzl_roles r where p.role_id = r.id AND p.callsign='".addSlashes($call)."'");

  if(mysql_num_rows($res) == 0)
    return false;
  $obj = mysql_fetch_object($res);
  if (md5($pass) != $obj->md5password)
    return false;

  // Logged in!
  // Insert an entry into the statistics table
  if (!$obj->country || $obj->country<=0)
    $_SESSION['required'] = true;
  $gmnow = gmdate("Y-m-d H:i:s");
  sqlQuery('insert into '.TBL_VISITS." (ts, pid, ip) 
      values ('$gmnow', $obj->id, '{$_SERVER['REMOTE_ADDR']}')");

  if (!isset($obj->utczone)){
    $uz = 0 - ($_POST['tzoffset']/60);
    sqlQuery ("update l_player set utczone=$uz where id={$obj->id}");
  }

  $now = gmdate("Y-m-d H:i:s");
  mysql_query("UPDATE l_player SET last_login='$now' WHERE id=" . $obj->id);

  $_SESSION['playerid'] = $obj->id;
  $_SESSION['callsign'] = $obj->callsign;
  $_SESSION['last_login'] = $obj->last_login;
  $_SESSION['seqnum'] = 1;
  session_refresh();

  // refresh cookie for 60 days ...
  setcookie ('themename', THEME_NAME, time()+60*60*24*60, '/' );
  return true;
}



function displayform ($callsign="") {
  echo '<BR><form name="login" method=post>' . SID_FORM . '
    <table align=center border=0 cellspacing=0 cellpadding=1>
    <input type=hidden name=link value="home">';


  echo '<tr><td align=right>User Name:</td><td><input type=text name=f_call value="'.$callsign.'" size=40 maxlength=40></td></tr>
    <tr><td align=right>Password:</td><td><input type=password name=f_pass size=8 maxlength=8></td></tr>
    <tr><td colspan=2 align=center><BR>
    '. htmlFormButton ("Login", 'f_ok') .'
    </td></tr>
    </table></form>';
echo '<script type="text/javascript">
if(document.login.f_call.value.length == 0) 
  document.login.f_call.focus();
else
  document.login.f_pass.focus();
now = new Date();
document.write ("<input type=hidden name=tzoffset value=" + now.getTimezoneOffset() +">");
</script>';
}
?>
