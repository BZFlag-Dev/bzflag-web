<?php // $Id: ipreport.php,v 1.4 2005/03/24 13:27:14 dennismp Exp $ vim:sts=2:et:sw=2

define (DEFAULT_DAYS, 30);

function section_ipreport (){
  if (!isFuncAllowed ('ipreport'))
    return errorPage ('You are not allowed to do this!');   
  if (!($days=$_GET['days']))
    $days = DEFAULT_DAYS;

  if ($_GET['detail'])
    section_ipreport_showDetail ($_GET['detail'], $days);
  else{
    section_ipreport_showSummary ($days);
  }
}

function section_ipreport_permissions() {
  return array(
    'ipreport' => 'Allow user to view IP report'
  );
}

function section_ipreport_showDetail ($plist, $days){
  $pids = explode (',', $plist);

  $res = sqlQuery ('select ip, pid, l_player.team, l_player.callsign, T.name as teamname from '
      . TBL_VISITS .' , l_player LEFT JOIN l_team T ON  T.id = l_player.team
      WHERE ip IS NOT NULL AND pid=l_player.id  AND pid IN ('. $plist  .') 
      AND ADDDATE(ts, INTERVAL '. $days .' DAY)>NOW() GROUP BY ip,pid ORDER by ip');

  echo '<CENTER>Showing detail for last '. $days .' days.<BR></center>';
  echo '<BR><TABLE align=center cellspacing=0><TR><TD>IP</td><TD width=10></td><TD>Callsign</td>
       <TD></td><TD width=20></td><TD>Team</td></TR>';

  $rowNum=0;
  while ($row = mysql_fetch_object($res)){
    if ($row->ip == $lastRow->ip){
      if (!$dup)
        echo '<TR><TD colspan=6><HR></tr>'. htmlRowClass($rowNum) ."<TD>$row->ip</td><TD></td>" .section_ipreport_showOne($lastRow);
      echo htmlRowClass($rowNum) .'<TD colspan=2></td>' . section_ipreport_showOne ($row);
      $dup = true;
    } else {
      $dup = false;
      $lastRow = $row;
    }
  }
  echo '<table>';
}

function section_ipreport_showOne ($row){
  return '<TD>'. playerLink($row->pid, $row->callsign) ."</td><TD>"
      . htmlURLbutSmall('visits', 'visitlog', "id=$row->pid", ADMBUT)
      . '</td><TD></td><TD>'. teamLink ($row->teamname, $row->team, null) .'</td></tr>';
}



function section_ipreport_showSummary ($days){
  $res = sqlQuery ('select ip, pid, l_player.team, l_player.callsign, T.name as teamname from '
      . TBL_VISITS .' , l_player LEFT JOIN l_team T ON  T.id = l_player.team
      WHERE ip IS NOT NULL AND pid=l_player.id
      AND ADDDATE(ts, INTERVAL '. $days .' DAY)>NOW() GROUP BY ip,pid ORDER by ip');

  $pids = array();
  $groups = array();
  $nextGroup = 0;
  while ($row = mysql_fetch_object($res)){
    if ($row->ip == $lastRow->ip){
      $p1 = $pids["$row->pid"];
      $p2 = $pids["$lastRow->pid"];
      if (!$p1 && !$p2){  // create new group
        section_ipreport_addToGroup ($pids, $groups, $row, $nextGroup);
        section_ipreport_addToGroup ($pids, $groups, $lastRow, $nextGroup++);
      } else if (!$p1)
        section_ipreport_addToGroup ($pids, $groups, $row, $p2);
      else if (!$p2)
        section_ipreport_addToGroup ($pids, $groups, $lastRow, $p1);
      else if ($p1 != $p2){ // concat groups
        $groups[$p1] = array_merge ($groups[$p1], $groups[$p2]);
        unset ($groups[$p2]);
        foreach ($pids as $key=>$p){
          if ($p == $p2)
            $pids[$key] = $p1;
        }
      }
    } 
    $lastRow = $row;
  }

  section_ipreport_doForm ($days);

  echo '<BR><TABLE cellspacing=0 align=center><TR><TD width=100></td><TD>Callsign</td><TD width=10></td>
      <TD>Team</TD></td></TR><TR><TD colspan=4><HR></td></tr>';
  foreach ($groups as $g){
    $arg=null;
    foreach ($g as $p){
      if ($arg)
        $arg .= ',';
      $arg .= $p[0];
    }
    $row=0;
    echo '<TR><TD rowspan='. (count($g)+1) .'>'. htmlURLbutSmall('DETAIL', 'ipreport', "days=$days&detail=$arg", ADMBUT) .'</td>';
    foreach ($g as $p){
      echo htmlRowClass($row) .'<TD>'. playerLink($p[0], $p[1]) .'</td><TD></td><TD>';
      echo  teamLink($p[3], $p[2], null);
      echo "</td></tr>\n";
    }
    echo '<TR><TD colspan=4><HR></td></tr>';
  }
  echo '</table>';
}



function section_ipreport_addToGroup (&$pids, &$groups, $row, $groupID){
  $pids["$row->pid"] = $groupID;
  if (!$groups[$groupID])
    $groups[$groupID] = array();
  $groups[$groupID][] = array ($row->pid, $row->callsign, $row->team, $row->teamname );
}



function section_ipreport_doForm ($numdays){
  echo '<TABLE style="margin-top: 10px" align=center class=insetForm><TR><TD>';
  echo "<TABLE border=0 cellpadding=0 cellspacing=0><TR valign=middle><TD>
  <form action=\"index.php\" name=none>
  <input type=hidden name=link value=ipreport>
  Period:&nbsp;</td><TD>
  <select name=days>";
    htmlOption (3, '3 days', $numdays);
    htmlOption (10, '10 days', $numdays);
    htmlOption (30, '30 days', $numdays);
    htmlOption (90, '90 days', $numdays);
    htmlOption (182, '6 months', $numdays);
  echo '</select><TD width=20></td><TD align=left>'. htmlFormButSmall ('Show me', '')
  .'</td></tr></table></td></tr></table></form>';
}

?>
