<?php // $Id$ vim:sts=2:et:sw=2

function section_players(){

  if (!$sort=$_GET['sort'])
    $sort = 'team';
  if (!$include=$_GET['include'])
    $include = 3;
  
  if ($sort=='callsign')
    $sby = 'P.callsign';
  else if ($sort=='joined')
    $sby = 'P.created';
  else if ($sort=='country')
    $sby = 'P.country, P.callsign';
  else 
    $sby = 'T.name, P.callsign';

  if ($include==1)
    $inc = 'AND P.team <= 0';
  else if ($include==2)
    $inc = 'AND P.team > 0';

  if (SHOW_PLAYER_ACTIVE > 0)
    $activeDays = SHOW_PLAYER_ACTIVE;
  else
    $activeDays = 0;


  $res = sqlQuery("select P.id, P.callsign, unix_timestamp(P.created) as created, T.name, T.leader, T.id teamid, 
      P.logo, P.country, C.flagname, C.name as countryname,
      last_login > subdate(now(), INTERVAL $activeDays DAY) as active
      from l_player P left join l_team T on P.team = T.id
      left join bzl_countries C on P.country = C.numcode
      where P.status != 'deleted' $inc order by $sby");

  section_players_doSelectForm ($sort, $include);

  echo "<BR><table align=center cellspacing=0 cellpadding=1><tr class=tabhead>
      <td>Callsign</td><td>Team</td><TD colspan=3>&nbsp;&nbsp;Joined</td><TD></td></tr>";
  $curteam = "";
  $rownum=0;
  
  while($obj = mysql_fetch_object($res)) {
    if($obj->logo != '')
      $logo = '<img TITLE="Player has a logo" src="'.THEME_DIR.'islogo.gif">';
    else
      $logo = '&nbsp;';

    if($obj->name != "") {
      if($obj->id == $obj->leader)
        $mem = '<img TITLE="Team Leader" src="'.THEME_DIR.'leader.gif">';
      else
        $mem = '&nbsp;';
      $pteam = '<a href="index.php?link=teaminfo&id='.$obj->teamid.'">'.$obj->name.'</a>';
    } else {
      $mem = "";
      $pteam = "<i><font color=#a0a0a0>none</font></i>";
    }


    if($sort=='team' && $obj->name != $curteam) {
      $curteam = $obj->name;
      $rownum=0;
      echo '<tr><td colspan=5><hr></td></tr>';
    } else  if($sort=='country' && $obj->countryname != $curteam) {
      $curteam = $obj->countryname;
      $rownum=0;
      echo '<tr><td colspan=5><hr></td></tr>';
    }


    $c = $rownum++%2?'rowodd':'roweven';
    $flag='';

    if ($obj->flagname)
      $flag = smallFlag ($obj->flagname, $obj->countryname);

    $joined = date ('Y-m-d', $obj->created);

    echo "<tr class=$c><td>". playerLink ($obj->id, $obj->callsign)
        ."</td><td align=left>$pteam</td><TD><nobr>&nbsp;&nbsp;$joined&nbsp;&nbsp;</nobr></td>
         <TD>$flag</td><TD>";
  
    if ($obj->active)
      echo '&nbsp;<img TITLE="Active member" src="'.THEME_DIR.'active.gif">';

    echo "</td><td>&nbsp;$logo$mem</td></tr>\n";


  }
  echo "</table>";
}







function section_players_doSelectForm ($sort, $include){
echo '<TABLE align=center class=insetForm><TR><TD>';
  echo "<TABLE border=0 cellpadding=0 cellspacing=0><TR><TD>
  <form action=\"index.php\" name=none>
  <input type=hidden name=link value=players>
  SORT:&nbsp;</td><TD>
  <select name=sort>";
    htmlOption ('callsign', 'Callsign', $sort);
    htmlOption ('team', 'Team', $sort);
    htmlOption ('country', 'Country', $sort);
    htmlOption ('joined', 'Date Joined', $sort);
  echo "</select></td><TD width=12></td><td>
  Include:&nbsp; 
  </td><TD>
  <select name=include>";
    htmlOption (3, 'ALL Players', $include);
    htmlOption (1, 'TEAMLESS Players', $include);
    htmlOption (2, 'TEAM Members', $include);
  echo '</select></td><td width=12></td><TD>'
  . htmlFormButSmall ('Show Me', '')
  .'</td></tr></table></td></tr></table></form>';
}


?>
