<?php // $Id: bulkmail.php,v 1.3 2005/03/24 13:27:14 dennismp Exp $ vim:sts=2:et:sw=2

function section_bulkmail (){

  if (!isFuncAllowed('bulk_mail'))
    return errorPage ('You are not authorized to send bulk mail.');

  echo '<BR>';
  
  echo "UNDER CONSTRUCTION.<BR>This function will allow privileged users to
  Send a bzmail to multiple recipients, such as 'all admins', 'all active
  teams', all users', etc.<BR>";

}

function section_bulkmail_permissions() {
  return array(
    'bulk_mail' => 'Allow to send bulkmail' 
  );
}




/***

All Players

All Admins
All Referees

All teamless players


All teams
All active teams
All inactive teams
All matchless teams





echo '<div class=checkbox>';



echo '<input class=checkbox type=checkbox name=del'.$checknum.' value='.$msg->msgid.'>&nbsp;';


***/





?>
