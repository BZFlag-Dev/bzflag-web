<?php

require_once ('phplib.php');

define ('PASSWORD', 'xxx');

//define (ADMIN_PERMISSION, 1);
//define (GUEST_PERMISSION, 4);
//define (NEW_USER_PERMISSION, 3);
define (REFEREE_PERMISSION, 2);
define (MODERATOR_PERMISSION, 5);
define (BZADMIN_PERMISSION, 7);
define (NONPOSTER_PERMISSION, 6);


// add one of each role ..

addPlayer ('test_ref',     PASSWORD, 0, REFEREE_PERMISSION);
addPlayer ('test_mod',     PASSWORD, 0, MODERATOR_PERMISSION);
addPlayer ('test_bzadmin', PASSWORD, 0, BZADMIN_PERMISSION);
addPlayer ('test_nopost',  PASSWORD, 0, NONPOSTER_PERMISSION);



// add normal players and teams ...

$teamA = addTeam ('The Three Stooges', 'closed', PASSWORD);


leaderTeam ($teamA, addPlayer ('Moe', PASSWORD, $teamA, NEW_USER_PERMISSION));
addPlayer ('Larry', PASSWORD, $teamA, NEW_USER_PERMISSION);
addPlayer ('Curly', PASSWORD, $teamA, NEW_USER_PERMISSION);

addPlayer ('test_teamless', PASSWORD, 0, NEW_USER_PERMISSION);




function rowExists ($query){
	return mysql_num_rows (sqlQuery ($query));
}

function delPlayer ($callsign){
	sqlQuery ("delete from l_player where callsign = '$callsign'");
}

function delTeam ($name){
	sqlQuery ("delete from l_team where name = '$name'");
}


function addPlayer ($callsign, $pass, $teamid, $roleid ){
  if (rowExists("SELECT callsign FROM l_player where callsign='$callsign'"))
    return;

  $md5 = md5($pass);
	sqlQuery ("insert into l_player (callsign, team, status, role_id, password, md5password, created, last_login) 
	values('$callsign', $teamid, 'registered', $roleid, 
		encrypt('$pass'), '$md5', now(), now())");
	return mysql_insert_id();
}


function addTeam ($name, $status, $password){
	$md5 = md5($pass);
	sqlQuery ("insert into l_team (name, status, status_changed, score, password, active) 
			values('$name', '$status', now(), 1200, encrypt('$pass'), 'yes')");

	return mysql_insert_id();
}


function leaderTeam ($teamID, $leaderID){
	sqlQuery ("update l_team set leader = $leaderID where id = $teamID");
}


?>
