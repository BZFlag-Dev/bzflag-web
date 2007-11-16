<?php // $Id$ vim:sts=2:et:sw=2

function section_createteam(){
  $vars = array('f_ok_x','f_name','f_logo','f_password','f_password2');
  foreach($vars as $var) {
    $$var = isset($_POST[$var]) ? $_POST[$var] : $_GET[$var];
    $$var = stripslashes($$var);
  }

  $error = 1;
  if($f_ok_x) {
    $error = 0;
    // Check fields

    // Check team name
    if($f_name == "") {
      $error = 1;
      echo "<div class=error>Team name must not be empty</div>";
    } else {
      // Check unicity
      $row = mysql_fetch_object(mysql_query("select id, status from l_team where name='" . addslashes($f_name).  "'"));
      if ($row){
        $error = 1;
        if ($row->status == 'deleted')
          echo "<div class=error><BR><CENTER>This team previously existed, but was deleted.  
              If you need to revive this team name, contact a site administrator.";
        else
          echo "<div class=error><BR><CENTER>This team name already exists</div>.";
      }
    }

    // Check logo
    //$msg = checkLogoSize($f_logo); - empty function
    $msg = '';
    if($msg != '') {
      $error = 1;
      echo "<div class=error>$msg</div>";
    }

    // Check admin password
    if($f_password != $f_password2) {
      $error = 1;
      echo "<div class=error>The passwords don't match</div>";
    } else {
      if($f_password == "") {
        $error = 1;
        echo "<div class=error>The admin password must not be empty</div>";
      } else {
        $cypher = crypt($f_password);
      }
    }

  }

  if($error) {
    echo '<form method=post>'.SID_FORM.'<table align=center border=0 cellspacing=0 cellpadding=1>
    <tr><td>Team name:</td><td><input type=text size=32 maxlength=32 name=f_name value="'.$f_name.'"></td></tr>
    <tr><td colspan=2><hr></td></tr>
    <tr><td>Admin password:</td><td><input type=password size=8 maxlength=8 name=f_password value="'.$f_password.'"></td></tr>
    <tr><td>Verify:</td><td><input type=password size=8 maxlength=8 name=f_password2 value="'.$f_password2.'"></td></tr>
    <tr><td colspan=2><hr></td></tr>
    <tr><td>Optional team logo URL (400x300 max!):</td><td><input type=text size=40 maxlength=200 name=f_logo value="'.$f_logo.'"></td></tr>
    <tr><td colspan=2><hr></td></tr>
    <tr><td colspan=2>Optional (but recommended) description:<br><textarea cols=50 rows=6 name=f_comment>'.$f_comment.'</textarea></td></tr>
    <tr><td colspan=2><hr></td></tr>
    <tr><td colspan=2 align=center>

    <TABLE><TR><TD>'. htmlFormButton ('Submit', 'f_ok_x') 
    . '</td><TD width=10></td><TD> '. htmlURLButton ('Cancel', 'teams', null, CLRBUT)
    . '</td></tr></table>
    </td></tr></table></form>';
  } else {
    // Insert data into table
    mysql_query("insert into l_team (id, name, comment, leader, logo, status, score, password, adminclosed, created) 
        values(0, '".addSlashes($f_name)."', '".addSlashes($f_comment)."', ".$_SESSION['playerid'].", 
        '".addSlashes($f_logo)."', 'opened', 1200, '".$cypher."', 'no', NOW())");
    
    // Update me so I belong to my team
    mysql_query("update l_player set team=" . mysql_insert_id() . " where id=".$_SESSION['playerid']);
    session_refresh();
    mysql_query("update l_team set status='opened' where id=".$_SESSION['teamid']);
    echo '<center>Thank you, <a href="index.php?link=playerinfo&id='.$_SESSION['playerid'].'&'.SID.'">'.$_SESSION['callsign'].'</a>, for creating the brand new <a href="index.php?link=teaminfo&id='.$_SESSION['teamid'].'&'.SID.'">'.$f_name.'</a> team.<br>
    <br>We hope that your team will show courage on the battlefield ;-)</center>';
  }
}
?>
