<?php

// bzfls.php
//
// Copyright (c) 1993 - 2004 Tim Riker
//
// This package is free software;  you can redistribute it and/or
// modify it under the terms of the license found in the file
// named COPYING that should have accompanied this file.
//
// THIS PACKAGE IS PROVIDED ``AS IS'' AND WITHOUT ANY EXPRESS OR
// IMPLIED WARRANTIES, INCLUDING, WITHOUT LIMITATION, THE IMPLIED
// WARRANTIES OF MERCHANTIBILITY AND FITNESS FOR A PARTICULAR PURPOSE.

define('IN_PHPBB', true);
$phpbb_root_path = '../../forums.bzflag.org/htdocs/';
$phpEx = 'php';

include($phpbb_root_path.'includes/functions.'.$phpEx);
include($phpbb_root_path.'includes/utf/utf_tools.'.$phpEx);
include($phpbb_root_path.'includes/utf/utf_normalizer.'.$phpEx);


// a hack to speed up action_list()
$verifiedGroupID = '6727';


# where to send debug printing (might override below)
$debugLevel= 2;      // set to >2 to see all sql queries (>1 to see GET/POST input args)
$debugFilename	= '/var/log/bzfls/bzfls.log';
#$debugFilename	= '/var/www/dbtest/bzflstest.log';
$debugNoIpCheck = 0;  // for testing ONLY !!!

// define dbhost/dbuname/dbpass/dbname here
// NOTE it's .php so folks can't read the source
include('/etc/bzflag/serversettings.php');
// $dbhost  = 'localhost';
// $dbname  = 'bzflag';
// $bbdbname = 'bzbb';
// $dbuname = 'bzflag';
// $dbpass  = 'bzflag';

include('banfunctions.php');

debug('Connecting to the database', 3);

$link = mysql_connect($dbhost, $dbuname, $dbpass) or die('Could not connect: ' . mysql_error());
if (!mysql_select_db($dbname)) {
  debug("Database $dbname did not exist", 1);
  die('Could not open db: ' . mysql_error());
}

@mysql_query("SET NAMES 'utf8'", $link);

# for banning.  provide key => value pairs where the key is an
# ip address. value is not used at present. these are pulled
# from the serverbans table.
$banlist = array();

$result = sqlQuery ('SELECT type, value, owner, reason FROM serverbans '
	. 'WHERE active = 1');
for ($i = 0; $i < mysql_num_rows ($result); ++$i) {
	$banlist[] = mysql_fetch_assoc($result);
}

// $alternateServers = array('http://my.BZFlag.org/db/','');
$alternateServers = array('');

register_shutdown_function ('allDone');

$debugMessage = null;

function allDone (){

  global $debugMessage, $debugFilename;
  if ($debugMessage != null){
    $fdDebug = @fopen ($debugFilename, 'a');

   if ($fdDebug != null) {
      fwrite($fdDebug, date('D M j G:i:s T Y') . ' ' . str_pad($_SERVER['REMOTE_ADDR'],15)
          . ' ' . str_replace ("\n", "\n  ", $debugMessage));
      if ($debugMessage{strlen($debugMessage)-1} != "\n");
        fputs ($fdDebug, "\n");
      fclose($fdDebug);
    }
    else {
      //print("Unable to write to to log file [$debugFilename]");
    }
  }

}

function debug ($message, $level=1) {
  global $debugMessage, $debugLevel;
  if ($level <= $debugLevel) {
    $debugMessage .= $message;
  }
}

function debugArray ($a){
  $msg = '';
  foreach ($a as $key => $val){
    if (!strlen($msg))
      $msg .= ', ';
    if (strncasecmp ($key, "PASS", 4)==0)
      $val = "**PASSWORD FILTERED**";
    $msg .= "$key=$val";
  }
  return str_replace (array ("\r", "\n"), array ('<\r>', '<\n>'), $msg);
}


// temp debug (menotume 2006-05-22)
//if (strncasecmp ($_REQUEST['callsign'], "dutch", 5) == 0){
//  debug ("\n***** GLOBALS:\n");
//  debug (  print_r ($GLOBALS, true), 1 );
//}


if ($debugLevel > 1){
  if (count ($GLOBALS['_POST']))
    debug ("POST ARGS: " . debugArray ($GLOBALS['_POST'], 0));
  if (count ($GLOBALS['_GET']))
    debug ("GET ARGS: " . debugArray ($GLOBALS['_GET'], 0));
}

// Query helper. This should be used instead of mysql_query()
function sqlQuery ($qry){
  if ( ! ($set = mysql_query ($qry))){
    debug ("*** QUERY FAILED:\n$qry\n*** ERROR: ". mysql_error(), 1);
    die ("Invalid query: " . mysql_error());
  } else
    debug ("SUCCESS: $qry", 3);
  return $set;
}

function validate_string($string, $valid_chars, $return_invalid_chars) {
  # thanx http://scripts.franciscocharrua.com/validate-string.php =)
  $invalid_chars = '';

  if ($string == null || $string == '')
    return(true);

  # for every char
  for ($index = 0; $index < strlen($string); $index++) {
    $char = substr($string, $index, 1);
    # valid char?
    if (strpos($valid_chars, $char) === false) {
      # if not, is it on the list of invalid characters?
      if (strpos($invalid_chars, $char) === false) {
        # if not, add it.
        $invalid_chars .= $char;
      }
    }
  }

  # if the string does not contain invalid characters, the function will return true.
  # if it does, it will either return false or a list of the invalid characters used
  # in the string, depending on the value of the second parameter.
  if($return_invalid_chars == true && $invalid_chars != '')
    return($invalid_chars);
  else
    return($invalid_chars == '');
}

# validate string or error
function validate_string_or_error($string, $valid_chars) {
  $invalid_chars = validate_string($string, $valid_chars, true);
  if ($invalid_chars == true) {
    return($string);
  }
  header('Content-type: text/html');
  print("ERROR: Invalid chars in \"$string\": \"$invalid_chars\"");
  return('');
}

# validate string or die
function validate_string_or_die($string, $valid_chars) {
  if ($string == '') {
    return $string;
  }
  $string = validate_string_or_error($string, $valid_chars);
  if ($string === '') {
    die('');
  }
  return($string);
}

# validate callsign or error (restrictive, used for more than callsign)
function vcsoe($string) {
  # against better judgement " " is valid here =(
  $valid_chars = ' -_.1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  return(validate_string_or_error($string, $valid_chars));
}

# validate hex or die
function vhod($string) {
  $valid_chars = '1234567890abcdef';
  return(validate_string_or_die($string, $valid_chars));
}

# validate nameport or die
function vnpod($string) {
  $valid_chars = '-.:1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  return(validate_string_or_die($string, $valid_chars));
}

# validate checktoken or die
function vctod($string) {
  $valid_chars = '\r\n=-_.1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  return(validate_string_or_die($string, $valid_chars));
}

# validate email or die
function veod($string) {
  $valid_chars = '-.@1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  return(validate_string_or_die($string, $valid_chars));
}

# maybeaddslashes
function maybe_add_slashes($string) {
  return get_magic_quotes_gpc() ? $string : addslashes($string);
}

# Common to all
if (array_key_exists('action', $_REQUEST)) {
  $action = vcsoe($_REQUEST['action']);
} else {
  $action = '';
}
# For ADD REMOVE
$nameport = vnpod(@$_REQUEST['nameport']);
$serverKey = maybe_add_slashes(@$_REQUEST['key']);
# For ADD
$build    = vcsoe(@$_REQUEST['build']);
$version  = vcsoe(@$_REQUEST['version']); # also on LIST
$gameinfo = vhod(@$_REQUEST['gameinfo']);
$slashtitle = maybe_add_slashes(@$_REQUEST['title']); # escape for SQL calls
# for ADD CHECKTOKENS
$checktokens = vctod(@$_REQUEST['checktokens']); # callsign0=token\ncallsign1=token\n...
$groups   = vctod(@$_REQUEST['groups']); # groups server is interested in

# For players
$callsign = vcsoe(@$_REQUEST['callsign']);  # urlencoded
$email    = veod(@$_REQUEST['email']);     # urlencoded
$password = vcsoe(@$_REQUEST['password']);  # urlencoded

# for LIST
$local      = vcsoe(@$_REQUEST['local']);
$listformat = vcsoe(@$_REQUEST['listformat']);


function testform($message) {
  header('Content-type: text/html');
  print('<html>
<head>
<title>BZFlag db server</title>
</head>
<body>
  <h1>BZFlag db server</h1>
  ' . $message . '
  <p>This is the development interface to the <a href="http://BZFlag.org/">BZFlag</a> list server AT BZ.</p>
  <form action="" method="POST">
    action:<select name="action">
	<option value="LIST" selected>LIST - list servers</option>
	<option value="ADD">ADD - add a server</option>
	<option value="REMOVE">REMOVE - remove a server</option>
	<option value="CHECKTOKENS">CHECKTOKENS - verify player token from game server</option>
	<option value="GETTOKEN">GETTOKEN - get player token</option>
	<option value="UNKNOWN">UNKNOWN - test invalid request</option>
    </select><br>
    list_format:<select name="listformat">
	<option value="plain" selected>plain</option>
	<option value="lua">lua</option>
	<option value="json">json</option>
    </select><br>
    actions: LIST<br>
    version:<input type="text" name="version" size="80"><br>
    local:<input type="text" name="local" size="5" value="1"><br>
    callsign:<input type="text" name="callsign" size="80"><br>
    password:<input type="text" name="password" size="80"><br>
    actions: REMOVE<br>
    nameport:<input type="text" name="nameport" size="80"><br>
    actions: ADD REMOVE<br>
    build:<input type="text" name="build" size="80"><br>
    gameinfo:<input type="text" name="gameinfo" size="80"><br>
    title:<input type="text" name="title" size="80"><br>
    advertgroups:<input type="text" name="advertgroups" size="40" maxsize=1000><br>
    actions: ADD CHECKTOKENS<br>
    checktokens:<textarea name="checktokens" rows="3" style="width:100%">
CallSign0@127.0.0.1=01234567
CallSign1=89abcdef</textarea>
    groups:<textarea name="groups" rows="3" style="width:100%">
Group0
Group1</textarea>
    actions: REGISTER CONFIRM<br>
    email:<input type="text" name="email" size="80"><br>
    <input type="submit" value="Post entry">
    <input type="reset" value="Clear form">
  </form>
</body>
</html>');
}

function advertlistCleanup (){
  $result = sqlQuery ('
      SELECT SAV.server_id from server_advert_groups as SAV
      LEFT JOIN servers  S ON S.server_id=SAV.server_id WHERE S.server_id is null');
  if ( ($row = mysql_fetch_row($result)) )
    $list = $row[0];
  while ( ($row = mysql_fetch_row($result)) )
    $list .= ',' . $row[0];
  sqlQuery ("DELETE FROM server_advert_groups WHERE server_id IN ($list)");
}


function lua_quote($str)
{
  return '"' . addslashes($str) . '"';
}


function json_quote($str)
{
  return '"' . addslashes($str) . '"';
}


function print_plain_list(&$listing)
{
  if (isset($listing['token'])) {
    if ($listing['token']) {
      print("TOKEN: " . $listing['token'] . "\n");
    } else {
      print("NOTOK: invalid callsign or password\n");
    }
  }
  foreach ($listing['servers'] as $server) {
    print(implode(" ", $server) . "\n");
  }
}


function print_lua_list(&$listing)
{
  print "return {\n";
  if (isset($listing['token'])) {
    print "token = " . lua_quote($listing['token']) . ",\n";
  }
  print "fields = { 'version', 'hexcode', 'addr', 'ipaddr', 'title', 'owner' },\n";
  //print "fields = { 'version', 'hexcode', 'addr', 'ipaddr', 'title', 'owner', 'ownername' },\n";
  print "servers = {\n";
  foreach ($listing['servers'] as $server) {
    print "{"
    . lua_quote($server[1]) . ","     // version
    . lua_quote($server[2]) . ","     // hexcode
    . lua_quote($server[0]) . ","     // addr
    . lua_quote($server[3]) . ","     // ipaddr
    . lua_quote($server[4]) . ","     // title
    //. lua_quote($server[5]) . ","    // owner
    . lua_quote($server[6]) . "},\n"; // ownername
  }
  print "}\n"; // end the "servers" table
  print "}\n";
}


function print_json_list(&$listing)
{
  print "{\n";
  if (isset($listing['token'])) {
    print "token: " . json_quote($listing['token']) . ",\n";
  }
  print '"fields": ["version","hexcode","addr","ipaddr","title","owner"],' . "\n";
  //print '"fields": ["version","hexcode","addr","ipaddr","title","owner","ownername"],' . "\n";
  print '"servers": [';
  $first = true;
  foreach ($listing['servers'] as $server) {
    if ($first) {
      $first = false;
    } else {
      print ",";
    }
    print "\n["
    . json_quote($server[1]) . ","  // version
    . json_quote($server[2]) . ","  // hexcode
    . json_quote($server[0]) . ","  // addr
    . json_quote($server[3]) . ","  // ipaddr
    . json_quote($server[4]) . ","  // title
    //. json_quote($server[5]) . "," // owner
    . json_quote($server[6]) . "]"; // ownername
  }
  print "\n]\n";
  print "}\n";
}


function action_list() {
  #  -- LIST --
  # Same as LIST in the old bzfls
  global $bbdbname, $dbname, $link, $callsign, $password, $version;
  global $local, $listformat, $alternateServers;
  header('Content-type: text/plain');
  debug ("  :::::  ", 2);

  $listing = Array();

  # remove all inactive servers from the table
  debug('Deleting inactive servers from list', 3);
  $timeout = 1830;    # timeout in seconds
  $staletime = time() - $timeout;
  mysql_query("DELETE FROM servers WHERE lastmod < $staletime", $link)
    or die('Could not drop old servers' . mysql_error());

  if (mysql_affected_rows($link) > 0)
    advertlistCleanup();

  if ($callsign && $password) {
    if (!mysql_select_db($bbdbname)) {
      debug("Database $bbdbname did not exist", 1);
      die('Could not open db: ' . mysql_error());
    }
    $clean_callsign = utf8_clean_string($callsign);

    $result = mysql_query("SELECT user_id, user_password FROM bzbb3_users "
	. "WHERE username_clean='".mysql_real_escape_string($clean_callsign)."' "
	. "AND user_inactive_reason=0", $link)
      or die ("Invalid query: " . mysql_error());
    $row = mysql_fetch_row($result);
    $playerid = $row[0];
    if (!$playerid || !phpbb_check_hash($password, $row[1])) {
      $listing['token'] = ""; // empty token is a bad token
      $playerid = 0;
      debug ("NOTOK", 2);
    } else {
      srand(microtime() * 100000000);
      $token = rand(0,2147483647);
      debug ("OK   token=$token", 2);
      $result = mysql_query("UPDATE bzbb3_users SET "
	  . "user_token='$token', "
	  . "user_tokendate='" . time() . "', "
	  . "user_tokenip='" . $_SERVER['REMOTE_ADDR'] . "' "
	  . "WHERE user_id='$playerid'", $link)
	or die ("Invalid query: ". mysql_error());
      $listing['token'] = $token;
      # check for private messages and send a notice if there is one
      $result = mysql_query("SELECT user_new_privmsg FROM bzbb3_users "
	  . "WHERE username_clean='".mysql_real_escape_string($clean_callsign)."'", $link)
	or die ("Invalid query: " . mysql_error());
      $pms = mysql_result($result, 0);
      if ($pms) {
	print("NOTICE: You have $pms private messages waiting for you, $callsign.  Log in at http://my.bzflag.org/bb/ to read them.\n");
      }
    }
    if (!mysql_select_db($dbname)) {
      debug("Database $dbname did not exist", 1);
      die('Could not open db: ' . mysql_error());
    }
  }

  $advertList = "0";    // marker for phantom group 'EVERYONE'
  if (isset($playerid) && $playerid){
    global $verifiedGroupID;

    sqlQuery ("USE $bbdbname");
    // get list of groups player belongs to ...
    debug ("FETCHING GROUPS", 3);

//    $result = sqlQuery ("
//      SELECT g.group_id FROM bzbb3_user_group ug, bzbb3_groups g
//      WHERE g.group_id=ug.group_id AND (ug.user_id = $playerid AND g.group_name<>'') OR g.group_name='VERIFIED'");

// menotume hack 2006/14/18 - speed fix ??
    $result = sqlQuery ("
      SELECT g.group_id FROM bzbb3_user_group ug, bzbb3_groups g
      WHERE g.group_id=ug.group_id AND ug.user_pending=0 AND ug.user_id = $playerid");

    // Verified group (speed up the above query, since group_name is
    // not indexed, this this prevents the need to look it up by name)
    $advertList .= "," . $verifiedGroupID;

    while ($row = mysql_fetch_row($result))
      $advertList .= ",$row[0]";
    sqlQuery ("USE $dbname");
  }
  //print "NOTICE: $advertList\n";

  if ($version)
    $qryv = "AND version='$version'";
  else
    $qryv = '';

  $fields = "nameport,version,gameinfo,ipaddr,title";
  $listing['fields'] = Array("addr", "version", "hexcode", "ipaddr", "title");

  if ($listformat == "lua" || $listformat == "json") {
    $fields .= ",owner,ownername";
    $listing['fields'][] = "owner"; // append the owner field
    $listing['fields'][] = "ownername"; // append the owner field
  }

  $result = sqlQuery("
    SELECT $fields FROM servers, server_advert_groups as sav
    WHERE servers.server_id=sav.server_id
    AND  sav.group_id IN ($advertList) $qryv
    ORDER BY `nameport` ASC");

  $listing['servers'] = Array();

  while (($row = mysql_fetch_row($result)) !== FALSE) {
    $listing['servers'][] = $row;
  }

  switch ($listformat) {
    case "lua":  { print_lua_list($listing);   break; }
    case "json": { print_json_list($listing);  break; }
    default:     { print_plain_list($listing); break; }
  }

  if ($local != 1) {
    // check the old list server and append
    foreach($alternateServers as $thisSever ){
      if ($thisSever != '') {
	readfile($thisSever.'?action=LIST&local=1');
      }
    }
  }
}


function action_gettoken (){
  global $bbdbname, $dbname, $link, $callsign, $password, $version, $local, $alternateServers;
  header('Content-type: text/plain');
  debug('Fetching TOKEN', 2);

  if ($callsign && $password) {
    if (!mysql_select_db($bbdbname)) {
      debug("Database $bbdbname did not exist", 1);
      die('Could not open db: ' . mysql_error());
    }
    $clean_callsign = utf8_clean_string($callsign);

    $result = mysql_query("SELECT user_id, user_password FROM bzbb3_users "
	. "WHERE username_clean='".mysql_real_escape_string($clean_callsign)."' "
	. "AND user_inactive_reason=0", $link)
      or die ("Invalid query: " . mysql_error());
    $row = mysql_fetch_row($result);
    $playerid = $row[0];
    if (!$playerid || !phpbb_check_hash($password, $row[1])) {
      print("NOTOK: invalid callsign or password\n");
    } else {
      srand(microtime() * 100000000);
      $token = rand(0,2147483647);
      $result = mysql_query("UPDATE bzbb3_users SET "
	  . "user_token='$token', "
	  . "user_tokendate='" . time() . "', "
	  . "user_tokenip='" . $_SERVER['REMOTE_ADDR'] . "' "
	  . "WHERE user_id='$playerid'", $link)
	or die ("Invalid query: ". mysql_error());
      print("TOKEN: $token\n");
    }
  }
}

function checktoken($callsign, $ip, $token, $garray) {
  # validate player token for connecting player on a game server
  global $bbdbname, $dbname, $link;
  # TODO add grouplist support
  print("MSG: checktoken callsign=$callsign, ip=$ip, token=$token ");
  foreach($garray as $group) {
    print(" group=$group");
  }
  print("\n");
  $timeout = 3600; # 60 minutes while testing
  $staletime = time() - $timeout;
  if (!mysql_select_db($bbdbname)) {
    debug("Database $bbdbname did not exist", 1);
    die('Could not open db: ' . mysql_error());
  }

  $clean_callsign = utf8_clean_string($callsign);

  # Check if player exists. if not, just return UNK
  $result = mysql_query("SELECT user_id FROM bzbb3_users "
			. "WHERE username_clean='".mysql_real_escape_string($clean_callsign)."' ", $link)
    or die ('Invalid query: ' . mysql_error());
  $rows = mysql_num_rows($result);
  if (!mysql_num_rows($result)) {
    print ("UNK: $callsign\n");
    debug ("UNK:$callsign ", 2);
    return;
  }

  # include server-reported IP so other server admins can't steal tokens
  # include token if specified
  $testtoken = "AND user_token='$token' AND user_tokendate > $staletime ";
  if ($ip) {
    $testtoken .= "AND user_tokenip='$ip'";
  }

//debug ( "SELECT user_id FROM bzbb3_users ". "WHERE username='$callsign' " . $testtoken, 2);

  $result = mysql_query("SELECT user_id FROM bzbb3_users "
      . "WHERE username_clean='".mysql_real_escape_string($clean_callsign)."' "
      . $testtoken, $link)
    or die ('Invalid query: ' . mysql_error());
  $row = mysql_fetch_row($result);
  $playerid = $row[0];
  if ($playerid) {
    # clear tokendate so nasty game server admins can't login someplace else
    $result = mysql_query("UPDATE bzbb3_users SET "
			  . "user_lastvisit='" . time() . "', "
    #. "user_tokendate='" . time() . "'"
			  . "user_tokendate='0'"
			  . "WHERE user_id='$playerid'", $link)
      or die ('Invalid query: ' . mysql_error());
    print ("TOKGOOD: $callsign");
    if (count($garray)) {
      $query = "SELECT bzbb3_groups.group_name FROM bzbb3_groups, bzbb3_user_group "
	. "WHERE bzbb3_user_group.user_id='$playerid' "
	. "AND bzbb3_user_group.group_id=bzbb3_groups.group_id "
	. "AND bzbb3_user_group.user_pending=0 "
	. "and (bzbb3_groups.group_name='"
	. implode("' or bzbb3_groups.group_name='", $garray) . "' )";
      $result = mysql_query($query)
	or die ('Invalid query: ' . mysql_error());
      while ($row = mysql_fetch_row($result)) {
	print(':' . $row[0]);
      }
    }
    print ("\n");

    # Send the BZID
    # - the BZID can be any uniquely identifying invariant string
    # - bzfs is setup to accept spaces if the strings is "quoted"
    print ("BZID: $playerid $callsign\n");
    debug ("TOKGOOD: $callsign ", 2);
  } else {
    print ("TOKBAD: $callsign\n");
    debug ("TOKBAD:$callsign ", 2);
  }
}

function action_checktokens() {
  #  -- CHECKTOKENS --
  # validate callsigns and tokens (clears tokens)
  global $link, $checktokens, $groups;
  debug ("  :::::  ", 2);
  if ($checktokens != '') {
    function remove_empty ($value) { return empty($value) ? false : true; }
    $garray = array_filter(explode("\r\n", $groups), 'remove_empty');
    foreach(array_filter(explode("\r\n", $checktokens), 'remove_empty') as $checktoken) {
      list($callsign, $rest) = explode('@', $checktoken);
      if ($rest) {
	list($ip, $token) = explode('=', $rest);
      } else {
	$ip = '';
	list($callsign, $token) = explode('=', $checktoken);
      }
      if ($token) checktoken($callsign, $ip, $token, $garray);
    }
  }
}

function add_advertList ($serverID){
  global $bbdbname;

  $adverts = $_REQUEST['advertgroups'];

  if ( ! isset($adverts) ){
    sqlQuery ("INSERT INTO server_advert_groups (server_id, group_id) VALUES ($serverID, 0)");
  } else {
  // bzfs makes sure that no quotes are in group names, so if present - is an invalid request
    if (strcspn($adverts ,"'\"")!=strlen($adverts) || strlen($adverts)>2048)
      die ('invalid request (advertgroups)');
    $advertList = explode (',', $adverts);
    if (in_array('EVERYONE', $advertList) || count($advertList)==0 ){
      sqlQuery ("INSERT INTO server_advert_groups (server_id, group_id) VALUES ($serverID, 0)");
    } else {
      sqlQuery ("
        INSERT INTO server_advert_groups (server_id, group_id)
        SELECT $serverID, group_id FROM $bbdbname.bzbb3_groups
        WHERE group_name IN ('". implode ("','", $advertList) ."')");
    }
  }
}

function action_add() {
  #  -- ADD --
  # Server either requests to be added to DB, or to issue a keep-alive so that it
  # does not get dropped due to a timeout...
  global $bbdbname, $dbname, $link, $nameport, $version, $build, $gameinfo, $slashtitle, $checktokens, $groups, $debugNoIpCheck, $serverKey;
  header('Content-type: text/plain');
  debug("Attempting to ADD $nameport $version $gameinfo " . stripslashes($slashtitle), 3);
  
  $owner = "";
  $ownerID = "";
  
  // check the server key (from the bzfs -publickey option)
  if ( ($version != 'BZFS0026' && $version != 'BZFS1910') || $serverKey)
  {
	$result = mysql_query("SELECT host, owner FROM authkeys WHERE key_string='" . mysql_real_escape_string($serverKey) . "'");
	$count = mysql_num_rows($result);
	if (!$count)
	{
		print("ERROR: Missing or invalid server authentication key\n");
		return;
	}
	$row=mysql_fetch_row($result);
	
	$host = $row[0];
	$ownerID = $row[1];
	$ip = gethostbyname($host);
	if ($ip != $_SERVER['REMOTE_ADDR'])
	{
		echo "ERROR: Host mismatch for server authentication key\n";
		return;	
	}
	
	// ok so the key is good, now to check the owner
	mysql_select_db($bbdbname);
	$result = mysql_query("SELECT username_clean FROM bzbb3_users WHERE user_id='{$ownerID}'");
	if (($row = mysql_fetch_row($result)) !== FALSE)
	{
	  $owner = $row[0];
	}
	else {
		print("ERROR: Owner lookup failure\n");
		return;	
	}
	mysql_select_db($dbname);
  }

  # Filter out badly formatted or buggy versions
  print "MSG: ADD $nameport $version $gameinfo " . stripslashes($slashtitle) . "\n";
  $pos = strpos($version, 'BZFS');
  if ($pos === false || $pos > 0)
  {
	 // see if it's fork or port ( just see if it's 8 characters and has letter and numbers in the right spot )
	 if (strlen($version) != 8 || is_numeric($version[0]) || is_numeric($version[3]) ||!is_numeric($version[4]))
		return;
  }
  $split = explode(':', $nameport);
  $servname = $split[0];
  if (array_key_exists(1, $split))
    $servport = $split[1];
  else
    $servport = 5154;
  $servip = gethostbyname($servname);

  if ($servip == '0.0.0.0') {
    debug("Changed $servname to requesting address: "
	. $_SERVER['REMOTE_ADDR'], 1 );
    $servip =  $_SERVER['REMOTE_ADDR'];
    $servname = $servip;
    $nameport = $servip . ':' . $servport;
  }elseif ($_SERVER['REMOTE_ADDR'] != $servip && !$debugNoIpCheck) {
    debug('Requesting address is ' . $_SERVER['REMOTE_ADDR']
	. ' while server is at ' . $servip, 1 );
    print('ERROR: Requesting address is ' . $_SERVER['REMOTE_ADDR']
	. ' while server is at ' . $servip );
    die();
  }

  # Test to see whether nameport is valid by attempting to establish a
  # connection to it
  $fp = @fsockopen ($servname, $servport, $errno, $errstring, 5);
  if (!$fp) {
    //debug('Unable to connect back to '.$servname.':'.$servport, 1);
    print("ERROR: Unable to reach your server. Check your router/firewall. ($errstring)\n");
    return;
  }
  # FIXME - should callback and update all stats instead of bzupdate.pl
  fclose ($fp);
  
  $curtime = time();
  
  $ownerEsc = mysql_real_escape_string($owner);

  $result = mysql_query("SELECT * FROM servers "
      . "WHERE nameport = '".mysql_real_escape_string($nameport)."'", $link)
    or die ('Invalid query: ' . mysql_error());
  $count = mysql_num_rows($result);
  if (!$count) {
    debug('Server does not already exist in database -- adding', 3);
    print("MSG: adding $nameport\n");

    # Server does not already exist in DB so insert into DB
    # FIXME escape title!
    $result = mysql_query("INSERT INTO servers "
        . "(nameport, build, version, owner, ownername, gameinfo, ipaddr,"
        . " title, lastmod) VALUES "                           
        . "('$nameport', '$build', '$version', '$ownerID', '{$ownerEsc}', "
	. " '$gameinfo', '$servip', '$slashtitle', $curtime)", $link)
      or die ("Invalid query: ". mysql_error());

    add_advertList(mysql_insert_id());
  } else {

    debug("Server already exists in database -- updating", 3);
    print("MSG: updating $nameport\n");

    # Server exists already, so update the table entry
    # ASSUMPTION: only the 'lastmod' column of table needs updating since all
    # else should remain the same as before
    # FIXME escape title!
    $result = mysql_query("UPDATE servers SET "
	. "build = '$build', "
	. "version = '$version', "
	. "gameinfo = '$gameinfo', "
	. "title = '$slashtitle', "
	. "lastmod = $curtime, "
	. "owner = '$ownerID', "
	. "ownername = '{$ownerEsc}' "
	. "WHERE nameport = '$nameport'", $link)
      or die ("Invalid query: ". mysql_error());
  }
  
  if ($owner)
	print "OWNER: $owner\n";

  action_checktokens();
  debug("ADD $nameport", 3);
  print "ADD $nameport\n";
}

function action_remove() {
  #  -- REMOVE --
  # Server requests to be removed from the DB.
  global $link, $nameport, $debugNoIpCheck;
  header('Content-type: text/plain');
  print("MSG: REMOVE request from $nameport\n");
  debug("REMOVE request from $nameport", 1);

  $split = explode(':', $nameport);
  $servname = $split[0];
  if (array_key_exists(1, $split))
    $servport = $split[1];
  else
    $servport = 5154;
  $servip = gethostbyname($servname);
  if ($servip == '0.0.0.0') {
    debug('Changed ' . $servip . ' to requesting address: '
	. $_SERVER['REMOTE_ADDR'], 1 );
    $servip =  $_SERVER['REMOTE_ADDR'];
    $servname = $servip;
    $nameport = $servip . ":" . $servport;
  } elseif ($_SERVER['REMOTE_ADDR'] != $servip && !$debugNoIpCheck) {
    debug('Requesting address is ' . $_SERVER['REMOTE_ADDR']
	. ' while server is at ' . $servip, 1 );
    print('ERROR: Requesting address is ' . $_SERVER['REMOTE_ADDR']
	. ' while server is at ' . $servip );
    die();
  }

  $result = sqlQuery("SELECT server_id FROM servers WHERE nameport = '$nameport'");
  if ( ($row=mysql_fetch_row($result)) ){
    sqlQuery ("DELETE FROM servers WHERE server_id = $row[0]");
    sqlQuery ("DELETE FROM server_advert_groups WHERE server_id = $row[0]");
  }
  print("REMOVE: $nameport\n");
}

# set up a list of addresses to check
$values = Array();
$values['ipaddress'][0] = $_SERVER['REMOTE_ADDR'];
$values['hostname'][0] = gethostbyaddr($_SERVER['REMOTE_ADDR']);

# If the hostname value came back as an IP, there wasn't a reverse DNS name,
# so ditch it
if ($values['hostname'][0] == $values['ipaddress'][0])
  unset($values['hostname'][0]);

# TODO: Add a check for the $nameport variable here and add that to $values

# ignore banned servers outright
if (IsBanned($values, $banlist)) {
  # reject the connection attempt
  header('Content-type: text/plain');
  $remote_addr = $_SERVER['REMOTE_ADDR'];
  debug("Connection rejected from $remote_addr", 1);
  die("ERROR: Connection attempt rejected.  See #bzflag on irc.freenode.net\n");
}

if (rand(1, 100) == 50){     // menotume 2006-04-30 : reduce queries
# remove all inactive registered players from the table
$timeout = 31536000; # timeout in seconds, 365 days
$staletime = time() - $timeout;
mysql_query("DELETE FROM players WHERE lastmod < $staletime", $link)
  or die ('Could not remove inactive players' . mysql_error());

# remove all players who have not confirmed registration
# FIXME this should not happen on every request
$staletime = time() - $timeout;
mysql_query("DELETE FROM players WHERE lastmod < $staletime AND randtext != NULL", $link)
  or die ('Could not remove inactive players' . mysql_error());
}


# tell the proxies not to cache
header('Cache-Control: no-cache');
header('Pragma: no-cache');
header("Connection: close");

# Do stuff based on what the 'action' is...
switch ($action) {
  case 'LIST':     { action_list();       break; }
  case 'GETTOKEN': { action_gettoken();   break; }
  case 'ADD':      { action_add();        break; }
  case 'REMOVE':   { action_remove();     break; }
  case 'REGISTER': { action_register();   break; }
  case 'CONFIRM':  { action_confirm();    break; }
  case 'CHECKTOKENS': {
    header('Content-type: text/plain');
    action_checktokens();
    break;
  }
  default: {
    # TODO dump the default form here but still close the database connection
    testform('Unknown command: \'' . $action . '\'');
  }
}

# make sure the connection to mysql is severed
if ($link) {
  # for a transaction commit just in case
  debug('Commiting any pending (SQL) transactions', 3);
  mysql_query('COMMIT', $link);

  # debug('Closing link to database');

  # say bye bye (shouldn't need to ever really, especially for persistent..)
  #mysql_close($link);
}

debug('End session', 4);

# Local Variables: ***
# mode:php ***
# tab-width: 8 ***
# c-basic-offset: 2 ***
# indent-tabs-mode: t ***
# End: ***
# ex: shiftwidth=2 tabstop=8

?>
