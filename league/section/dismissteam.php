<?php // $Id$ vim:sts=2:et:sw=2

function section_dismissteam(){
  require_once('lib/common.php');

  $id = addslashes($_GET['id']);
  $f_ok_x = $_GET['f_ok_x'];
  $f_no_x = $_GET['f_no_x'];
  $link = $_GET['link'];

  // Check permission
  $allowed = 0;
  $obj = mysql_fetch_object(mysql_query("select name, comment, leader, logo, password, status, adminclosed 
         from l_team where id=$id"));

  if (!$obj || $obj->status=='deleted'){
    echo '<BR><CENTER>Team already deleted!<BR>';
    return;
  }
  if(isAdmin()||($_SESSION['playerid']== $obj->leader)) {
    // Admin or team leader, allowed
    $allowed = 1;
  } else {
    // Check password
    $cypher = crypt($f_password, substr($obj->password, 0, 2));
    if($cypher == $obj->password) {
      // Good password, allowed
      $allowed = 1;
    }
  }
  if(!$allowed) {
    echo "<center>Sorry, you don't have the permission to dismiss the <a href=\"index.php?link=teaminfo&id=".$id."\">".$obj->name."</a> team.</center>";
  } else {
    if($f_ok_x) {
      deleteTeam ($id);
      echo "Team dismissed";
    } else {
      if($f_no_x) {
        echo "<center>Phew, the <a href=\"index.php?link=teaminfo&id=".$id."\">".$obj->name."</a> team is still alive!</center>";
      } else {
          echo "<center>Do you really want to dismiss the <a href=\"index.php?link=teaminfo&id=".$id."&".SID."\">".$obj->name."</a> team?";
          echo "<form method=get>
              <input type=hidden name=link value='$link'>
              <input type=hidden name=id value=$id><br>
              ". htmlFormButton ('Yes', 'f_ok_x') ."
              &nbsp;&nbsp;
              ". htmlFormButton ('No', 'f_no_x', CLRBUT) ."
              </form>";
      }
    }
  }
}
?>
