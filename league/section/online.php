<?php // $Id$ vim:sts=2:et:sw=2

function section_online_timestr ($secs){
  if ($secs > 3600)
    return sprintf ("%2d:%02d:%02d", $secs/3600, (secs%3600)/60, $secs%60);
  else
    return sprintf ("%02d:%02d", $secs/60, $secs%60);
}
  
function section_online_matchCIDR($addr, $cidr) {
  list($ip, $mask) = explode('/', $cidr);
  return (ip2long($addr) >> (32 - $mask) == ip2long($ip) >> (32 - $mask));
  }

function section_online_permissions() {
  return array(
    'show_guests' => 'Show guests',
    'show_ip'     => 'Show IPs'
  );
}

function section_online(){
  echo '<BR>';

  $showIP = isFuncAllowed('show_ip' );

  $logged = sqlQuery ('SELECT callsign, MIN('. SESSION_LIFETIME .'-(UNIX_TIMESTAMP(expire)-UNIX_TIMESTAMP())) AS idle, 
      ip, playerid FROM '. TBL_SESSION   .' WHERE callsign IS NOT NULL AND expire > NOW() GROUP BY callsign ');

  if (!$logged){
    echo 'Data problems - try back later<BR>';
    return;
  }

  $guests = sqlQuery ('SELECT MIN('. SESSION_LIFETIME .'-(UNIX_TIMESTAMP(expire)-UNIX_TIMESTAMP())) AS idle, ip
      FROM '. TBL_SESSION   .' WHERE callsign IS NULL AND expire > NOW() GROUP BY ip');

  if(mysql_num_rows($logged)==0 && mysql_num_rows($guests)==0) {
    echo "<center>Nobody's online</center>";
    return;
  }


  echo '<center><table>';

  while($row = mysql_fetch_object($logged)) {
    echo '<TR><TD>'.$ipLong . playerLink ($row->playerid,$row->callsign)
        .'&nbsp;</td><TD>&nbsp; (idle: ' .section_online_timestr($row->idle). ')</td>';
    if ($showIP)
      echo "<TD width=10></td><TD>$row->ip</td>";
    echo '</tr>';
  }

  if( isFuncAllowed('show_guests' ) ) {
    echo '<TR><TD colspan=5><BR></td></tr>';
    while($row = mysql_fetch_object($guests)) {
      // Ignore googlebot and msnbot/microsoft surfers. Narrow this down if possible
      if( !section_online_matchCIDR($row->ip, "66.249.64.0/19" ) && !section_online_matchCIDR($row->ip, "207.46.0.0/16") ) {
        echo '<TR><TD>guest&nbsp;</td><TD>&nbsp; (idle: ' .section_online_timestr($row->idle). ')</td>';
        if ($showIP)
          echo "<TD width=10></td><TD>$row->ip</td>";
        echo '</tr>';
      }
    }
  }

  echo '</table></center>';
}


?>
