<?php // $Id: maintfuncs.php,v 1.3 2005/04/18 14:23:07 menotume Exp $ vim:sts=2:et:sw=2

function doTeamDelete ($id, $name){
  $msg = '&nbsp; &nbsp; Deleting team '. teamLink($name, $id, null) ." &nbsp;&nbsp; (#$id) ... ";
  if (deleteTeam($id))
    $msg .= 'OK<BR>';
  else
    $msg .= '<font color=cc0000 size=+1>FAILED!</font><BR>';
  return $msg;
}


function doPlayerDelete ($id, $name){
  $msg = '&nbsp; &nbsp; Deleting player '. playerLink($id, $name) ." &nbsp;&nbsp; (#$id) ... ";
  if (deletePlayer($id))
    $msg .= 'OK<BR>';
  else
    $msg .= '<font color=cc0000 size=+1>FAILED!</font><BR>';
  return $msg;
}


function doTeamInactive ($teamrow, $days){
  $msg = '&nbsp; &nbsp; De-activating team '. teamLink($teamrow->name, $teamrow->id, null) ." &nbsp;&nbsp; (#$teamrow->id) ... ";
  if ( sqlQuery ("UPDATE l_team set active='no' where id=$teamrow->id") ){
    $msg .= 'OK<BR>';
    sendBzMail (0, $teamrow->leader, "$teamrow->name is now INACTIVE!", 
        'Your team, '. teamLink($teamrow->name, $teamrow->id, false) .' has been marked as INACTIVE since you 
        have not played a match in ' . round($days) .' days! Get out there and play!');
  } else
    $msg .= '<font color=cc0000 size=+1>FAILED!</font><BR>';
  return $msg;
}



function site_maintenance ($how, $echo=false){
  $result = true;
  $teamsDeleted = 0;
  $teamsMarkedInactive = 0;
  $playersDeleted = 0;
  $topicsLocked = 0;

  $msg = "<b>SITE MAINTENANCE $how at: ". gmdate('M d Y  H:i') . ' (UTC)</b><p>';

  $msg .= '<b>Checking for teams with no login in last '. periodMsg(TEAMNOLOGIN_DAYS) .' ...</b><BR>';
  $sql =  'SELECT t.name, t.id, p.id, max(p.last_login) 
    FROM  l_team t
    LEFT JOIN l_player p ON p.team = t.id 
    WHERE t.id <> 1 
    GROUP BY p.team 
    HAVING (max(p.last_login) < subdate(now(), INTERVAL '. TEAMNOLOGIN_DAYS  .' DAY) ) ORDER BY t.name ';

  if (!($res = sqlQueryMsg ($sql, $qmsg))){
    $msg .= " $qmsg\n";
    $result = false;
  } else {
    while($row = mysql_fetch_array($res)){
      $msg .= doTeamDelete($row[1], $row[0]);
      $teamsDeleted++;
    }
  }



  if ($result){
    $msg .= '<b>Teams which never played a match, older than '. periodMsg(TEAMMATCHLESS_DAYS) .' ...</b><BR>';
    $res = sqlQueryMsg ('SELECT T.id, T.name, COUNT(distinct M.id) as nummatches FROM l_team T
        LEFT JOIN bzl_match M ON (team1 = T.id OR team2 = T.id)
        WHERE T.status!="deleted" AND ADDDATE(T.created, INTERVAL '. TEAMMATCHLESS_DAYS .' DAY)<NOW()
        GROUP BY T.id', $msg);
    while ($row = mysql_fetch_object($res))
      if ($row->nummatches==0){
        $msg .= doTeamDelete ($row->id, $row->name);
        $teamsDeleted++;
      }
  }

  if ($result){
    $msg .= '<b>Checking for teamless players who have not logged on in '. periodMsg(PLAYERNOLOGIN_DAYS) .' ...</b><BR>';
    if (!($res = sqlQueryMsg ('SELECT p.callsign, p.id FROM l_player p WHERE p.team = 0 
       AND p.last_login < subdate(now(), INTERVAL '. PLAYERNOLOGIN_DAYS  .' DAY) 
       AND p.status <> "deleted" ORDER BY p.callsign', $qmsg))){
      $msg .= " $qmsg\n"; 
      $result = false;
    } else {
      while($row = mysql_fetch_array($res)){
        $msg .= doPlayerDelete($row[1], $row[0]);
        $playersDeleted++;
      }
    }
  }


  if ($result){
    $msg .= '<b>Checking for teams which have not matched in '. periodMsg(TEAMACTIVE_DAYS) .' ...</b><BR>';
    if (!($res = sqlQueryMsg ('SELECT t.id, t.name, t.leader, MAX(m.tsactual) as lastmatch
        FROM l_team t, bzl_match m
        WHERE t.active="yes" AND t.status!="deleted"
        AND  (t.id=m.team1 OR t.id=m.team2)
        GROUP BY t.id', $qmsg))){
      $msg .= " $qmsg\n"; 
      $result = false;
    } else {
      $now = strtotime ('now');
      while ($row = mysql_fetch_object($res)){
        $days = ($now - strtotime($row->lastmatch)) / (60*60*24) ;
        if ($days > TEAMACTIVE_DAYS){
          $msg .= doTeamInactive ($row, $days);
          $teamsMarkedInactive++;
        }
      }
    }
  }


  if ($result){
    $msg .= 'Deleting expired invitations.<BR>';
    sqlQueryMsg ('DELETE FROM bzl_invites WHERE expires < NOW()', $msg);
    $msg .= 'Deleting badpass entries older than 6 months.<BR>';
    sqlQueryMsg ('DELETE FROM l_badpass WHERE ADDDATE(gmtime, INTERVAL 6 MONTH) < NOW()', $msg); 

// lock old forum topics ...
    $msg .= 'Locking old forum topics.<BR>';
    $expires = LOCKOLDTOPIC_DAYS>0 ? LOCKOLDTOPIC_DAYS : 61;
    $result = sqlQueryMsg ('SELECT thread.id, thread.forumid, thread.subject, 
      IF (ADDDATE(MAX(msg.datesent), INTERVAL '. $expires .' DAY) < NOW(), 1, 0 )  as is_expired
      FROM l_forumthread thread, l_forummsg msg
      WHERE msg.threadid = thread.id
        AND thread.status != "locked" AND thread.status!="deleted"
      GROUP BY thread.forumid, thread.id', $msg);
    while ($row = mysql_fetch_object ($result)){
      if ( $row->is_expired ){
        ++$topicsLocked;
        sqlQueryMsg ("UPDATE l_forumthread SET status='locked', status_at=NOW(), status_by=null 
          WHERE id = $row->id  AND forumid = $row->forumid", $msg);
      }
    }
  }


  $msg .= "<p><B>Teams Deleted: $teamsDeleted<BR>Teams marked inactive: $teamsMarkedInactive"
      ."<BR>Players Deleted: $playersDeleted<BR>Topics locked: $topicsLocked</b><p><HR><CENTER><BR><font size=+1>";
  if ($result)
    $msg .= 'Site Maintenance Completed</font>';
  else
    $msg .= 'Site Maintenance FAILED</font>';
  if ($echo)
    echo $msg;
  sqlQuery ('UPDATE bzl_siteconfig set text="'. gmdate ('Y-m-d H:i:s')  .' GMT" where name = "maintenance"');
  sendBzMailToAll (0, ADMIN_PERMISSION, "SITE MAINTENANCE PERFORMED", $msg, true, false);
  return $result;
}


?>
