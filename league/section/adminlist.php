<?php // $Id: adminlist.php,v 1.7 2005/04/20 21:42:34 menotume Exp $ vim:sts=2:et:sw=2

function section_adminlist(){
  if (!isFuncAllowed('list_admins'))
    return errorPage ('You are not authorized to list the admins');

  $roles = getRolesWithPermission('adminlist::show');
  $res = sqlQuery ("
      SELECT p.id, p.callsign, r.name as level from l_player p, bzl_roles r
      WHERE r.id = p.role_id AND p.status!='deleted' AND r.id IN (" . join(',',$roles) . " ) ORDER BY level");

  echo '<BR><table align=center border=0 cellspacing=0 cellpadding=2>
      <tr class=tabhead align=center><td>Callsign</td><td width=10></td><td>Level</td></tr>';

  $line=0;
  while($row = mysql_fetch_object($res)) {
    if(++$line %2)
      $cl = "rowOdd";
    else
      $cl = "rowEven";
    echo "<tr class=\"$cl\"><td align=right>".
      htmlLink ($row->callsign, 'playerinfo', "id=$row->id")
      ."</td><td></td><td align=left>$row->level";
  }
  echo '</table>';  
}

function section_adminlist_permissions(){
  return array(
    'list_admins' => 'View administrators',
    'show' => 'Show users with this permission'
  );
}
?>
