<?php // $Id$ vim:sts=2:et:sw=2

function section_contact(){
  $obj = mysql_fetch_object(mysql_query("select text from bzl_siteconfig where name='contact'"));
  echo nl2br($obj->text);

  echo '<p><TABLE cellspacing=0 align=center><TR><TD colspan=3>
      <HR>Matches can be reported to any of the following:<BR><BR></td></tr>';

  $roles = getRolesWithPermission('show');
  $res = sqlQuery ("SELECT p.id, p.callsign, r.name as level from l_player p, bzl_roles r 
                    WHERE r.id = p.role_id AND r.id IN (" . join(',',$roles) . ") ORDER BY level");

  while($row = mysql_fetch_object($res)) {
    if(++$line %2)
      $cl = "rowEven";
    else
      $cl = "rowOdd";
    echo "<tr class=\"$cl\"><td width=40% align=right>".
      htmlLink ($row->callsign, 'playerinfo', "id=$row->id")
      .'</td><td width=10></td><td align=left>' ;
      if(isAuthenticated()) echo htmlURLbutton ('BZmail', 'sendmessage', "pid=$row->id");
      echo '</td></tr>' ;
  }
  echo '</table>';  
}

function section_contact_permissions() {
  return array(
    'show' => 'Show users as contacts'
  );
}
?>
