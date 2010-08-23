<?php // $Id: visitlog.php,v 1.4 2005/03/24 13:27:14 dennismp Exp $ vim:sts=2:et:sw=2

define (DEFAULT_LIMIT, 50);
// max, ip, id

function section_visitlog (){
  $id = $_GET['id'];
  $max = $_GET['max'];
  $ip = $_GET['ip'];
  if (!$max)
    $max = DEFAULT_LIMIT;
  if ($ip)
    $id=null;
  if ($ip{0}=='*')
    $ip=null;
  
  if (!isFuncAllowed('visit_log'))
    return errorPage ('You are not authorized to view the log');

  echo '<BR><CENTER>';
  section_visitlog_selector ($ip, $max, $id);

  echo'<div class=contTitle>Showing Visit Log for';
  if ($ip){
    echo " IP: $ip";
    $res = sqlQuery ('select pid, ts, ip, P.callsign as callsign 
      FROM '. TBL_VISITS .','. TBL_PLAYER ." as P where pid=P.id AND ip LIKE '$ip%' order by ts desc limit $max");
  } else if ($id){
    $player = section_visitlog_sqlGetPlayer ($id);  
    echo ': '. playerLink ($id, $player->callsign);     
    $res = sqlQuery ('select ts, ip FROM '. TBL_VISITS ." where pid=$id order by ts desc limit $max");
  } else {
    echo ': ALL'; 
    $res = sqlQuery ('select pid, ts, ip, P.callsign as callsign 
      FROM '. TBL_VISITS .','. TBL_PLAYER ." as P where pid=P.id order by ts desc limit $max");
  }

  echo '<TABLE cellspacing=0><tr align=center class=tabhead>';
  if (!$id)
    echo '<td>Callsign</td><td width=8></td>';
  echo '<td>Login time</td><TD width=15></td><TD>IP</td></tr>';

  $rn = 0;
  while ( $row = mysql_fetch_object ($res) ){
    $c = $rn++%2?'rowodd':'roweven';
    echo "<TR class=$c>";
    if (!$id)
      echo '<td>'. playerLink ($row->pid, $row->callsign) .'</td><td></td>';
    echo "<TD>$row->ts</td><TD></td><TD>$row->ip</td></tr>";  
  } 
}

function section_visitlog_permissions() {
  return array(
    'visit_log' => 'View a players visit log'
  );
}

function section_visitlog_sqlGetPlayer ($id){
  return mysql_fetch_object (sqlQuery ("SELECT * from ". TBL_PLAYER ." WHERE id='$id'"));
}



function section_visitlog_selector ($ip, $max, $id){
  echo '<TABLE align=center class=insetForm><TR><TD>';
  echo "<TABLE border=0 cellpadding=0 cellspacing=0><TR valign=middle><TD>
  <form action=\"index.php\" name=none>
  <input type=hidden name=link value=visitlog>
  <input type=hidden name=id value=$id>
  # Visits:&nbsp;</td><TD>
  <select name=max>";
    htmlOption (50,  '50', $max);
    htmlOption (100, '100', $max);
    htmlOption (200, '200', $max);
    htmlOption (500, '500', $max);
    htmlOption (1000, '1000', $max);
  echo "</select><BR></td><TD width=20></td><TD>IP: &nbsp;
       <input title=\"Enter any number of IP digits, or * for ALL\" type=text name=ip size=15 maxlength=15 value='$ip'>
       <TD width=20></td><TD align=left>". htmlFormButSmall ('Fetch!', '')
       .'</td></tr></table></td></tr></table></form>';
}



?>
