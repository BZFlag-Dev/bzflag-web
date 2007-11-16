<?php // $Id$ vim:sts=2:et:sw=2


// 'global' defines ...
define (DELETED_PLAYER, 1);

define (ADMIN_PERMISSION, 1);
define (GUEST_PERMISSION, 4);
define (NEW_USER_PERMISSION, 3);
    


// Functions to help with permissions

function isGuest() {
  return $_SESSION['role_id'] == GUEST_PERMISSION;
}

function isAuthenticated() {
  return !isGuest();
}

function isAdmin() {
  return $_SESSION['role_id'] == ADMIN_PERMISSION;
}

function isFuncAllowed ($funcID){
  if( $_SESSION['role_id'] == ADMIN_PERMISSION ) return true;	
  $funcID = getAbsPermission($funcID);
  return isset($_SESSION['permissions'][$funcID]);
}

function getRolesWithPermission($name) {
  $roles = array(ADMIN_PERMISSION); // Admin always got access
  $name = getAbsPermission($name);

  $res = mysql_query("SELECT role_id FROM bzl_permissions WHERE name = '$name'") or die(mysql_error());
  while($row = mysql_fetch_array($res)) {
    $roles[] = $row[0];
  }

  return $roles;
}

function getAbsPermission($name) {
	if( strpos($name,'::') === false ) 
		$name = $GLOBALS['MODULE_NAME'] . '::' . $name;
  return $name;
}

function smileys ($input) {
  static $smiley;

  if( $smiley === null ) {
    $smiley = array();
    
    $res = mysql_query("select * from l_smiley") or die(mysql_error());
    while($row = mysql_fetch_object($res)) {
      $smiley[htmlspecialchars($row->code)] = 
        '<img border=0 src="' . THEME_DIR . 'smilies/' .$row->image. '">';
    }
  }

  return strtr($input,$smiley);
}



// exponential moving average of # of games played per day
// if $id is null, an array of all team's activity rankings will be returned.
// TODO: What if team was 'born' less than $nDays ago ?!?!?!?
// Returns:  array ( [teamid] => activity  )
function teamActivity ($id, $nDays){
  if ($id > 0)
    $w = "AND (team1=$id OR team2=$id)";

  $set = sqlQuery ("select team1, team2, UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(tsactual)  as age FROM bzl_match
    WHERE tsactual > DATE_SUB(NOW(), INTERVAL $nDays day) $w
    ORDER BY tsactual desc");

  $k = (2 / (1 + $nDays)) / $nDays;
  $spd = (24*60*60);
  $accum = array();
  while ($row = mysql_fetch_array  ($set)){
    $weight = $k * ($nDays - ($row[2] / $spd));
    $accum[$row[0]] += $weight; 
    $accum[$row[1]] += $weight; 
  }
  if ($id > 0)
    return $accum [$id];
  return $accum;
}


function displayRating ($id) {
  $res = mysql_query('select * from '. TBL_TEAM ." where id='$id'");
  $row = mysql_fetch_array($res);
  $rating = $row[score];
  $badge = $rating;
  if ($badge < 800) $badge = 800;
  if ($badge > 1899) $badge = 1899;
  $imgname = sprintf ("%04d.gif", $badge - $badge%100);
  return "<img width=19 height=17 src=\"" . THEME_DIR . "$imgname\">&nbsp;$rating";;
}


?>
