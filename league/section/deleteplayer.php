<?php // $Id: deleteplayer.php,v 1.4 2005/03/24 13:27:14 dennismp Exp $ vim:sts=2:et:sw=2

function section_deleteplayer(){
  require_once('lib/common.php');

  $id = $_GET['id'];
  $f_ok_x = $_GET['f_ok_x'];
  $f_no_x = $_GET['f_no_x'];

  if (!isFuncAllowed ('delete_player'))
    return errorPage ('You are not allowed to delete players');   

  $row = mysql_fetch_object (mysql_query ("select * from l_player where id=$id"));

  if ($f_ok_x)
    section_deleteplayer_deleteThePlayer ($row);
  else if ($f_no_x)
    section_deleteplayer_cancelDelete ($row);
  else
    section_deleteplayer_confirmDelete ($row);

}

function section_deleteplayer_permissions() {
  return array(
    'delete_player' => 'Allowed to remove players'
  );
}

function section_deleteplayer_cancelDelete ($row){
  echo "<BR><center>Thank you for sparing $row->callsign!</center><BR>\n";

}

function section_deleteplayer_deleteThePlayer ($row){
  echo '<BR><CENTER>';
  if (deletePlayer ($row->id))
    echo "Player '$row->callsign' has been deleted!<BR>";
  else
    echo '<p>Deleted failed';
}

function section_deleteplayer_confirmDelete ($row){
  $link = $_GET['link'];

  // if team leader ...
  if ($row->team != 0){
    $team = mysql_fetch_object (mysql_query("select * from l_team where id=$row->team"));
    if ($team->leader == $row->id){
      echo '<BR><CENTER>Whoa! The player ' .playerLink ($row->id, $row->callsign). 
          ' is the leader of team ' .teamLink( $team->name, $team->id, $team->status). 
          '<p> You must assign another team leader, or dismiss the team first.<p>';
      return;

    }
  }

  // if admin...
  if ($row->role_id == ADMIN_PERMISSION){
    echo '<BR><CENTER>Whoa! The player ' .playerLink ($row->id, $row->callsign). 
        ' is a site admin.<p>You can\'t delete a site admin.<p>';
    return;
  }


  echo '<center><BR>Do you really want to delete the player '  .playerLink($row->id, $row->callsign). ' ?';
  echo '<p>All inbox mail for ' .playerLink($row->id, $row->callsign). 
      ' will be permenantly deleted, although forum posts will be retained. This player\'s entries will be removed from 
      the "visits log"';
  if ($row->team != 0)
    echo "'$row->callsign' will be removed from team '$team->name'<p>";
  echo "<form method=get>
    <input type=hidden name=link value='$link'>
    <input type=hidden name=id value=$row->id><br>
    ". htmlFormButton ('Yes, delete', 'f_ok_x') ."
    &nbsp;&nbsp;
    ". htmlFormButton ('No, Cancel', 'f_no_x', CLRBUT) ."
    </form>";
}



?>
