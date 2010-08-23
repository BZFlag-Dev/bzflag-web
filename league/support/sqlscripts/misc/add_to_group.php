<?php

define ('GROUPNAME', 'duc.league');


echo "DON'T RUN THIS!\n";
exit;




$namelist[] = 'menotume';

sqlConnect ('league', 'league', 'p@ss2naRt');
$set = mysql_query ("SELECT id,callsign from l_player where team<>0");
while($obj = mysql_fetch_object($set)) {
  $namelist[] = $obj->callsign;
}
mysql_close ();



sqlConnect ('bzbb', 'bzbb', 'pointandshoot');


$group = mysql_query ("SELECT group_id FROM phpbb_groups WHERE group_name='" . GROUPNAME. "'");
if (mysql_num_rows($group) != 1){
  echo 'GROUP '. GROUPNAME ." not found\n";
  exit;
}

$group = mysql_fetch_object ($group);
$group_id = $group->group_id;

echo "GID: $group_id\n";


foreach ($namelist as $name){
  $userset = mysql_query ("SELECT u.user_id, user_active, username, ug.group_id as gid
                         FROM phpbb_users u
                         LEFT JOIN phpbb_user_group ug on u.user_id = ug.user_id AND ug.group_id=$group_id 
                         WHERE username='$name'");
if (!$userset){
 echo mysql_error();
 exit;
}

  printf ("%25.25s: ", $name);
  if (mysql_num_rows($userset) < 1){
    echo "Not a bzbb user\n";
  } else {
    $user = mysql_fetch_object ($userset);
    if ($user->user_active == 0)
      echo "User not active\n";
    else if ($user->gid <> 0)
      echo "already in group!\n";
    else{
      mysql_query ("INSERT INTO phpbb_user_group (group_id, user_id, user_pending) VALUES ($group_id, $user->user_id, 0)");
      echo " ADDED.\n";
    }
  }
}




function sqlConnect ($db, $user, $pass, $host=null){
  $link=mysql_pconnect($host, $user, $pass);
  mysql_select_db($db, $link);
  if($link === false){
    echo "<BR><BR><HR>There seems to be a problem with the database, 
    please try again later.<BR>
    If the problem persists, please contact the site administrator.";
    exit;
  }
}


?>
