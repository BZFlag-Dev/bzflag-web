<?php // $Id$ vim:sts=2:et:sw=2

session_set_save_handler(
  "mysql_session_open",
  "mysql_session_close",
  "mysql_session_read",
  "mysql_session_write",
  "mysql_session_destroy",
  "mysql_session_gc" );


function mysql_session_open($session_path, $session_name){
}


function mysql_session_close(){
  return 1;
} 



// Set STATIC info here (to reduce sql queries)
function sessionStaticInfo (){

  if(!isset ($_SESSION['themes'])) {
    $res = sqlQuery ('SELECT * FROM bzl_themes');
    while ($row = mysql_fetch_object($res)){
      $themes[$row->tag] = array($row->displayname, $row->themedir);
    }
    $_SESSION['themes'] = $themes;
  }
  if(! isset($_SESSION['permissions'])) {
    $res = mysql_query("SELECT id,name FROM bzl_permissions WHERE role_id = " . GUEST_PERMISSION) or die(mysql_error());
    $_SESSION['role_id'] = GUEST_PERMISSION;
    $_SESSION['permissions'] = array();
    while($row = mysql_fetch_array($res)) {
      $_SESSION['permissions'][$row[0]] = 1;
    }
  }
}

function unserialize_session_data( $serialized_string ) {
   $variables = array(  );
   $a = preg_split( "/(\w+)\|/", $serialized_string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
   for( $i = 0; $i < count( $a ); $i = $i+2 ) {
       $variables[$a[$i]] = unserialize( $a[$i+1] );
   }
   return( $variables );
}


$dbugfd = null;

function dbug ($msg){
  global $dbugfd;
  if (!$dbugfd && ($dbugfd = fopen ("/tmp/LEAGUEDEBUG.log", "a")) === false)
    return;
  fwrite ($dbugfd, $msg . "\n");
}



function mysql_session_read($SID){
  $ip = $_SERVER['REMOTE_ADDR'];
  $sql = "
    SELECT data,refresh,playerid 
    FROM l_session 
    WHERE id = '$SID' 
    and expire > now() 
  ";


  $res = mysql_query($sql);
  if( $res ){
    $row = mysql_fetch_assoc($res);
    if( $row ) {
      $session =  stripslashes($row['data']);
      if( $row['refresh'] == 'Y' and !empty($row['playerid']) )  {
        $_SESSION = unserialize_session_data($session);
        session_data_reload($row['playerid']);
        $session = session_encode();
        unset($_SESSION);

        $sql = "UPDATE l_session SET refresh='N' WHERE id='$SID'";
        mysql_query($sql);
      }

      return $session;
    }
    else {
      mysql_session_destroy($SID);
    }
  }
  sessionStaticInfo();
  return "";
}


function mysql_session_write($SID,$value) {
  $expiration = time() + SESSION_LIFETIME;

  $ip = $_SERVER['REMOTE_ADDR'];
  $SID = addslashes($SID);
  $value = addslashes($value);

  if( isset( $_SESSION['callsign'] ) ) {
    $callsign = "'" . addslashes($_SESSION['callsign']) . "'";
    $playerid = $_SESSION['playerid'];
  } else {
    $callsign = 'NULL';
    $playerid = 0;
  }

  $sql = "INSERT INTO l_session (id, expire, data, callsign, playerid,ip) ".
      "VALUES( '$SID',FROM_UNIXTIME($expiration),'$value',$callsign,$playerid,'$ip') ";

  $res = mysql_query($sql);

  // No point in much errorchecking, as
  // we cannot do anything about it. (this is executed after output is generated)

  if(! $res ) { // If error, we try to update
    $sql = "UPDATE l_session SET ".
          "expire = FROM_UNIXTIME($expiration), ".
          "data = '$value', ".
          "callsign = $callsign, ".
          "playerid = $playerid ".
        "WHERE id = '$SID' ";

    mysql_query($sql);
  }

  return true;
}

function mysql_session_destroy($SID) {
  $SID = addslashes($SID);
  $sql = "DELETE FROM l_session WHERE id = '$SID'";
  mysql_query($sql);
  unset ($_SESSION);
  sessionStaticInfo();
}


function mysql_session_gc($lifetime) {
  $sql = "DELETE FROM l_session ".
      "WHERE expire < FROM_UNIXTIME(" . (time() - $lifetime) . ")";

  $res = mysql_query($sql);

  if( $res )
    return mysql_affected_rows();
  else
    return 0;
}

function session_refresh_all() {
  if( $_SESSION['player_id'] ) {
    sqlQuery("UPDATE l_session SET refresh='Y' WHERE playerid!='{$_SESSION['playerid']}'");
    session_refresh();
  }
  else {
    sqlQuery("UPDATE l_session SET refresh='Y'");
  }
}

function session_refresh($id = null) {
  // If no id specified, use current playerid
  if( $id === null ) 
    $id = (isset($_SESSION['playerid']) ? $_SESSION['playerid'] : null );

  // This is a guest user, ignore it
  if( $id === null ) return;

  // If $id is current user, then dont flag. Just reload data
  if( $id == $_SESSION['playerid'] ) 
    session_data_reload($id);
  else 
    sqlQuery("UPDATE l_session SET refresh='Y' WHERE playerid='$id'");
}

function session_data_reload($id) {
  $old_role_id = $_SESSION['role_id'];
  $row = mysql_fetch_object (sqlQuery("SELECT p.team, r.name as level, r.id as role_id, p.team FROM l_player p, bzl_roles r WHERE r.id = p.role_id AND p.id=$id")) or die(mysql_error());
  $_SESSION['teamid']  = $row->team;
  $_SESSION['level']   = $row->level;
  $_SESSION['role_id'] = $row->role_id;
  $row = mysql_fetch_object (sqlQuery("SELECT leader FROM l_team WHERE id={$_SESSION['teamid']}"));
  $_SESSION['leader'] = ($row->leader == $id);
  $_SESSION['playerid'] = $id;

  // mail - detect new messages
  if( ! isset($_SESSION['lst_msg_read_ts']) )
    $_SESSION['last_msg_read_ts'] = $_SESSION['last_login'];
  $row = mysql_fetch_object(mysql_query("
    SELECT count(*) as num, max(unix_timestamp(datesent)) latest_ts 
    FROM l_message 
    WHERE toid=$id and status='new'
  "));
  $_SESSION['mail_unread'] = $row->num; 
  $_SESSION['new_mail'] = ($row->latest_ts > $_SESSION['last_msg_read_ts']);

   // match
  $res = sqlQuery ('select unix_timestamp(tsactual) as ts from '. TBL_MATCH .' order by tsactual desc limit 1');
  $row = mysql_fetch_object($res);
  if ( $_SESSION['last_login'] < $row->ts )
    $_SESSION['new_match'] = 1;
  else
    $_SESSION['new_match'] = 0;

  if (ENABLE_SEASONS){
     // season match
    $res = sqlQuery ("select unix_timestamp(m.tsactual) as ts from ". TBL_MATCH ." m, l_season s where m.season = s.id and s.id > 1 and s.active = 'yes' order by m.tsactual desc limit 1");
    $row = mysql_fetch_object($res);
    if ( $_SESSION['last_login'] < $row->ts )
      $_SESSION['new_league_match'] = 1;
    else
      $_SESSION['new_league_match'] = 0;

     // season match
    $res = sqlQuery ("select unix_timestamp(s.enddate) as ts from l_season s where s.active = 'no' and s.finished = 'yes' order by s.enddate desc limit 1");
    $row = mysql_fetch_object($res);
    if ( $_SESSION['last_login'] < $row->ts )
      $_SESSION['end_season'] = 1;
    else
      $_SESSION['end_season'] = 0;
  }
  // forum
  $res = sqlQuery ('select unix_timestamp(datesent) as ts from l_forummsg order by datesent desc limit 1');
  $row = mysql_fetch_object($res);
  if ( $_SESSION['last_login'] < $row->ts )
    $_SESSION['new_forum'] = 1;
  else
    $_SESSION['new_forum'] = 0;

  //news
  $row = mysql_fetch_object (sqlQuery ('select unix_timestamp(newsdate) as ts from bzl_news order by newsdate desc limit 1'));
  if ( $_SESSION['last_login'] < $row->ts )
    $_SESSION['new_news'] = 1;
  else
    $_SESSION['new_news'] = 0;

  // Get permissions, if changed
  if( $old_role_id != $_SESSION['role_id'] ) {
    $res = mysql_query("SELECT name FROM bzl_permissions WHERE role_id = {$_SESSION['role_id']}") or die(mysql_error());
    $_SESSION['permissions'] = array();
    while($row = mysql_fetch_array($res)) {
      $_SESSION['permissions'][$row[0]] = 1;
    }
  }
}
?>
