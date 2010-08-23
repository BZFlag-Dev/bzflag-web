<?php


require_once ('phplib.php');


function addPlayer ($callsign, $pass, $teamid, $level, $status='registered' ){
	$md5 = md5($pass);
	sqlQuery ("insert into l_player (callsign, team, status, role_id, password, md5password, created, last_login) 
	values('$callsign', $teamid, '$status', $level, encrypt('$pass'), '$md5', now(), now())");
	return mysql_insert_id();
}



addPlayer ('Deleted player', 'xyz@3ed', 0, NEW_USER_PERMISSION, 'deleted');
addPlayer ('admin', 'admin', 0, ADMIN_PERMISSION);

?>
