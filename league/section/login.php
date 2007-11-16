<?php // $Id$ vim:sts=2:et:sw=2

function section_login(){
  $vars = array('f_ok','f_call','f_pass', 'link');

  foreach($vars as $var)
    $$var = isset($_POST[$var]) ? $_POST[$var] : $_GET[$var];

  echo '<BR>';

  if($f_ok) {
    // Check the password
    $res = mysql_query("select p.id, p.callsign, p.password, p.md5password, 
           unix_timestamp(p.last_login) as last_login, p.utczone, p.country
           from l_player p,bzl_roles r where p.role_id = r.id AND p.callsign='".addSlashes($f_call)."'");

    if(mysql_num_rows($res) != 0) {
      $obj = mysql_fetch_object($res);
      // Check MD5 password, or password, and do the conversion
      $cyphermd5 = md5($f_pass);

      // Do check the password
      if($obj->md5password == '') {
        $cypher = crypt($f_pass, substr($obj->password, 0, 2));

        if($cypher == $obj->password) {
          $idsuccess = 1;
          // Update MD5 password in database
          mysql_query("update l_player set md5password='$cyphermd5' where id=".$obj->id);
        } else {
          $idsuccess = 0;
        }

      } else {
        if($cyphermd5 == $obj->md5password)
          $idsuccess = 1;
        else
          $idsuccess = 0;
      }
      if($idsuccess == 1) {
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
      }
    }
  }
  if($f_ok and $idsuccess == 0) {
    // Unknown callsign
    echo "<div class=error><CENTER>Wrong callsign or password</div>";
    $gmnow = gmdate("Y-m-d H:i:s");
    sqlQuery ("INSERT INTO l_badpass (gmtime, ip, name)  VALUES ('$gmnow', '{$_SERVER['REMOTE_ADDR']}', '$f_call')");
    sleep (2);  // deter script-kiddies
  }

  if(isAuthenticated()) {
    echo '<center>Welcome back, <a href="index.php?link=playerinfo&id='.$_SESSION['playerid'].'&'.SID.'">'.$_SESSION['callsign'].'</a><br><br>';
    if($_SESSION['teamid'] == 0) {
      echo "You don't belong to any team. What are you waiting for? ;-)";
    } else {
      // Get team name
      $obj=mysql_fetch_object(mysql_query("select name from l_team where id=".$_SESSION['teamid']));
      echo 'You are a member of the magnificent <a href="index.php?link=teaminfo&id='.$_SESSION['teamid'].'&'.SID.'">'.$obj->name.'</a> team.';
    }
    echo '<br><br><form method=post>'.SID_FORM
      . htmlFormButton ("Log Out", 'logout_x') .'
      </form></center>';

  } else {
    echo '<form name="login" method=post>' . SID_FORM . '
      <table align=center border=0 cellspacing=0 cellpadding=1>
      <input type=hidden name=link value='.$link.'>';

      
    echo '<tr><td>Callsign:</td><td><input type=text name=f_call value="'.$f_call.'" size=40 maxlength=40></td></tr>
      <tr><td>Password:</td><td><input type=password name=f_pass size=8 maxlength=8></td></tr>
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
}
?>
