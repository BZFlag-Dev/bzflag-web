<?php // $Id: badpass.php,v 1.5 2005/04/03 08:19:22 menotume Exp $ vim:sts=2:et:sw=2
define (DEFAULT_DAYS, 7);
define (DEFAULT_THRESHOLD, 5);
define (TIMESPAN, 60*5);

function section_badpass (){
  $days = $_GET['days'];
  $thresh = $_GET['thresh'];
  if (!$days)
    $days = DEFAULT_DAYS;
  if (!$thresh)
    $thresh = DEFAULT_THRESHOLD;
  if ($thresh < 2)
    $thresh=2;
  if ($thresh > 100)
    $thresh=100;
  
  if (!isFuncAllowed('badpass'))
    return errorPage ('You are not authorized to view this log');

  echo '<BR><CENTER>';
  section_badpass_selector ($days, $thresh);

  $res = sqlQuery ("SELECT ip, name, unix_timestamp(gmtime) AS utime, l_player.id 
       FROM l_badpass LEFT JOIN l_player on l_badpass.name = l_player.callsign 
       WHERE ADDDATE(gmtime, INTERVAL $days DAY)>NOW() ORDER BY ip, gmtime");

  if (mysql_num_rows($res) <= $thresh){
    echo '<BR><CENTER>No data. Nothing, zip, nadda, zilch, null.<BR>';
    return;
  } 

  $stack = array();
  for ($x=0; $x<$thresh-1; $x++)
    $stack[] = $row;

  echo $thresh . ' or more bad login attempts from same IP within 5 minutes<BR>(Displaying all sets for the last '. $days .' days)';

  echo '<TABLE cellspacing=0><tr align=center class=tabhead><td align=left>Callsign entered</td><td width=10></td>
       <td>Time (UTC)</td><TD width=12></td><TD>IP</td></tr>';

  $rn = 0;
  while ( $row = mysql_fetch_object ($res) ){
    if ($row->ip==$stack[0]->ip && ($row->utime-$stack[0]->utime)<=TIMESPAN){
      if (!$set){
        echo '<TR><TD colspan=5><HR></td></tr>';
        for ($x=0; $x<$thresh-1; $x++)
          echo htmlRowClass($rn) .'<td>'. playerLink($stack[$x]->id, $stack[$x]->name) .'</td><td></td><TD>'. date ('Y-m-d  H:i:s', $stack[$x]->utime) 
               ."</td><TD></td><TD><A HREF='index.php?link=visitlog&ip={$stack[$x]->ip}'>{$stack[$x]->ip}</a></td></tr>"; 
      
      }
      echo htmlRowClass($rn) .'<td>' .playerLink($row->id, $row->name). '</td><td></td><TD>'. date ('Y-m-d  H:i:s', $row->utime) 
           ."</td><TD></td><TD><A HREF='index.php?link=visitlog&ip=$row->ip'>$row->ip</a></td></tr>"; 
      $set = true;
    } else
      $set = false;
    $stack[] = $row;
    array_shift ($stack);
  } 
}


function section_badpass_permissions (){
  return array(
    'badpass' => 'View authentication failures'
  );
}

function section_badpass_selector ($days, $thresh){
  echo '<TABLE align=center class=insetForm><TR><TD>';
  echo "<TABLE border=0 cellpadding=0 cellspacing=0><TR valign=middle><TD>
  <form action=\"index.php\" name=none>
  <input type=hidden name=link value=badpass>
  # Days:&nbsp;</td><TD>
  <select name=days>";
    htmlOption (3,  '3', $days);
    htmlOption (7, '7', $days);
    htmlOption (15, '15', $days);
    htmlOption (30, '30', $days);
    htmlOption (60, '60', $days);
    htmlOption (120, '120', $days);
  echo "</select><BR></td><TD width=20></td><TD>Threshold: &nbsp;
       <input title=\"Show anyone (same IP) who enters a bad callsign/password more than this many times in 5 minutes\" 
       type=text name=thresh size=2 maxlength=2 value='$thresh'><TD width=20></td><TD align=left>"
       . htmlFormButSmall ('Fetch!', '') .'</td></tr></table></td></tr></table></form>';
}
?>
