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

# where to send debug printing (might override below)
$enableDebug	= 0;
$debugFile	= "bzfls.log";

// define dbhost/dbuname/dbpass/dbname here
// NOTE it's .php so folks can't read the source
include('serversettings.php');
// $dbhost  = "localhost";
// $dbname  = "bzflag";
// $dbuname = "bzflag";
// $dbpass  = "bzflag";

# Common to all
$action   = $_REQUEST['action'];
if (!array_key_exists("action", $_REQUEST)) {
  $action = "";
}

# For bzfs
$nameport = $_REQUEST['nameport'];

# For ADD
$build    = $_REQUEST['build'];
$version  = $_REQUEST['version']; # also on LIST
$gameinfo = $_REQUEST['gameinfo'];
$title    = $_REQUEST['title'];
$local    = $_REQUEST['local'];

# For players
$callsign = $_REQUEST['callsign'];  # urlencoded
$email    = $_REQUEST['email'];     # urlencoded
$password = $_REQUEST['password'];  # urlencoded

# for CHECKTOKEN
$playerid = $_REQUEST['playerid'];
$token = $_REQUEST['token'];

# for banning.  provide key => value pairs where the key is an
# ip address. value is not used at present.
# FIXME this should be in an sql table with a remote admin interface
$banlist = array(
  "68.109.43.46" => "knightmare.kicks-ass.net",
#  "127.0.0.1" => "localhost"
  "255.255.255.255" => "globalbroadcast"
);

// $alternateServers = array("http://db.bzflag.org/db/","");
$alternateServers = array("");

# log function
if ($enableDebug) {
  function debug ($message) {
    global $debugFile;
    $fp = fopen($debugFile, "a");
    if ($fp) {
      # output the message with a BSD-style timestamp
      fwrite($fp, date("D M j G:i:s T Y") . " " . $_SERVER['REMOTE_ADDR'] . " " . $message . "\n");
      fclose($fp);
    } else {
      print("Unable to write to to log file [$filename]");
    }
  }
} else {
  function debug ($message) {
  }
}

function testform ($message) {
  global $action;
  header("Content-type: text/html");
  print('<html>
<head>
<title>BZFlag db server</title>
</head>
<body>
  <h1>BZFlag db server</h1>
  ' . $message . '
  <p>This is the development interface to the <a href="http://BZFlag.org/">BZFlag</a> list server.</p>
  <form action="" method="GET">
    action:<select name="action">
	<option value="LIST" selected>LIST - list servers</option>
	<option value="DEBUG">DEBUG - developer interface</option>
	<option value="ADD">ADD - add a server</option>
	<option value="REMOVE">REMOVE - remove a server</option>
	<option value="REGISTER">REGISTER - new player</option>
	<option value="CONFIRM">CONFIRM - confirm registration</option>
	<option value="CHECKTOKEN">CHECKTOKEN - verify player token from game server</option>
	<option value="UNKNOWN">UNKNOWN - test invalid request</option>
    </select><br>
    actions: LIST<br>
    version:<input type="text" name="version" size="80"><br>
    local:<input type="text" name="local" size="5" value="1"><br>
    callsign:<input type="text" name="callsign" size="80"><br>
    password:<input type="text" name="password" size="80"><br>
    actions: ADD REMOVE<br>
    nameport:<input type="text" name="nameport" size="80"><br>
    build:<input type="text" name="build" size="80"><br>
    gameinfo:<input type="text" name="gameinfo" size="80"><br>
    title:<input type="text" name="title" size="80"><br>
    actions: REGISTER CONFIRM<br>
    email:<input type="text" name="email" size="80"><br>
    actions: CHECKTOKEN<br>
    playerid:<input type="text" name="playerid" size="80"><br>
    token:<input type="text" name="token" size="80"><br>
    <input type="submit" value="Post entry">
    <input type="reset" value="Clear form">
  </form>
</body>');
  if ($action == "DEBUG") {
    print('<PRE>\n');
    var_dump($GLOBALS);
    print('</PRE>\n');
    phpinfo();
  }
  print('</body>
</html>');
}

function action_list () {
  #  -- LIST --
  # Same as LIST in the old bzfls
  global $link, $callsign, $password, $version, $local, $alternateServers;
  header("Content-type: text/plain");
  debug("Fetching LIST");

  # remove all inactive servers from the table
  debug("Deleting inactive servers from list");
  $timeout = 1800;    # timeout in seconds
  $staletime = time() - $timeout;
  mysql_query("DELETE FROM servers WHERE lastmod < $staletime", $link)
    or die("Could not drop old servers" . mysql_error());

  if ($callsign && $password) {
    # TODO we have user credentials, verify, create a token
    $result = mysql_query("SELECT playerid FROM players "
	. "WHERE callsign='$callsign' "
	. "AND password = '$password'"
	. "AND randtext IS NULL", $link)
      or die ("Invalid query: " . mysql_error());
    $row = mysql_fetch_row($result);
    $playerid = $row[0];
    if (!$playerid) {
      print("TOKEN:ERROR: invalid callsign or password ($callsign:$password)\n");
    } else {
      srand(microtime() * 100000000);
      $token = rand(0,2147483647);
      $result = mysql_query("UPDATE players SET "
	  . "token = '$token', "
	  . "tokendate = '" . time() . "'"
	  . "WHERE playerid = '$playerid'", $link)
	or die ("Invalid query: ". mysql_error());
      print("TOKEN:$playerid:$token\n");
    }
  }

  if ($version)
    $result = mysql_query("SELECT nameport,version,gameinfo,ipaddr,title "
	. " FROM servers WHERE version = '$version'"
	. " ORDER BY `nameport` ASC", $link)
      or die ("Invalid query: ". mysql_error());
  else
    $result = mysql_query("SELECT nameport,version,gameinfo,ipaddr,title "
	. " FROM servers ORDER BY `nameport` ASC", $link)
      or die ("Invalid query: ". mysql_error());
  while (TRUE) {
    $row = mysql_fetch_row($result);
    if (!$row)
      break;
    $line = implode(" ", $row);
    print "$line\n";
  }

  if ($local != 1) {
    // check the old list server and append
    foreach($alternateServers as $thisSever ){
      if ($thisSever != "")
	readfile($thisSever.'?action=LIST&local=1');
    }
  }
}

function action_add () {
  #  -- ADD --
  # Server either requests to be added to DB, or to issue a keep-alive so that it
  # does not get dropped due to a timeout...
  global $link, $nameport, $version, $gameinfo, $title;
  header("Content-type: text/plain");
  debug("Attempting to ADD $nameport $version $gameinfo $title");

  # Filter out badly formatted or buggy versions
  print "trying ADD $nameport $version $gameinfo $title\n";
  $pos = strpos($version, "BZFS");
  if ($pos === false || $pos > 0)
    return;
  $split = explode(":", $nameport);
  $servname = $split[0];
  if (array_key_exists(1, $split))
    $servport = $split[1];
  else
    $servport = 5154;
  $servip = gethostbyname($servname);

  if ($servip == "0.0.0.0") {
    debug("Changed " . $servip . " to requesting address: "
	. $_SERVER['REMOTE_ADDR'] );
    $servip =  $_SERVER['REMOTE_ADDR'];
    $servname = $servip;
    $nameport = $servip . ":" . $servport;
  }elseif ($_SERVER['REMOTE_ADDR'] != $servip) {
    debug("Requesting address is " . $_SERVER['REMOTE_ADDR']
	. " while server is at " . $servip );
    print("Requesting address is " . $_SERVER['REMOTE_ADDR']
	. " while server is at " . $servip );
    die();
  }

  # Test to see whether nameport is valid by attempting to establish a
  # connection to it
  # FIXME - should callback and update all stats instead of bzupdate.pl

  $fp = fsockopen ($servname, $servport, $errno, $errstring, 30);
  if (!$fp) {
    print "failed to connect\n";
    return;
  }
  # TODO should check additional info for stats here.
  fclose ($fp);

  $curtime = time();

  $result = mysql_query("SELECT * FROM servers "
      . "WHERE nameport = '$nameport'", $link)
    or die ("Invalid query: ". mysql_error());
  $count = mysql_num_rows($result);
  if (!$count) {
    debug("Server does not already exist in database -- adding");

    # Server does not already exist in DB so insert into DB
    $result = mysql_query("INSERT INTO servers "
	. "(nameport, build, version, gameinfo, ipaddr,"
	. " title, lastmod) VALUES "
	. "('$nameport', '$build', '$version',"
	. " '$gameinfo', '$servip', '$title', $curtime)", $link)
      or die ("Invalid query: ". mysql_error());
  } else {

    debug("Server already exists in database -- updating");

    # Server exists already, so update the table entry
    # ASSUMPTION: only the 'lastmod' column of table needs updating since all
    # else should remain the same as before
    $result = mysql_query("UPDATE servers SET "
	. "build = '$build', "
	. "version = '$version', "
	. "gameinfo = '$gameinfo', "
	. "title = '$title', "
	. "lastmod = $curtime "
	. "WHERE nameport = '$nameport'", $link)
      or die ("Invalid query: ". mysql_error());
  }

  debug("ADD complete");

  print "ADD complete\n";
}

function action_remove () {
  #  -- REMOVE --
  # Server requests to be removed from the DB.
  global $link, $nameport;
  header("Content-type: text/plain");
  debug("REMOVE request from $nameport");

  $split = explode(":", $nameport);
  $servname = $split[0];
  if (array_key_exists(1, $split))
    $servport = $split[1];
  else
    $servport = 5154;
  $servip = gethostbyname($servname);
  if ($servip == "0.0.0.0") {
    debug("Changed " . $servip . " to requesting address: "
	. $_SERVER['REMOTE_ADDR'] );
    $servip =  $_SERVER['REMOTE_ADDR'];
    $servname = $servip;
    $nameport = $servip . ":" . $servport;
  } elseif ($_SERVER['REMOTE_ADDR'] != $servip) {
    debug("Requesting address is " . $_SERVER['REMOTE_ADDR']
	. " while server is at " . $servip );
    print("Requesting address is " . $_SERVER['REMOTE_ADDR']
	. " while server is at " . $servip );
    die();
  }

  $result = mysql_query("DELETE FROM servers WHERE nameport = '$nameport'", $link)
    or die ("Invalid query: ". mysql_error());
}

function action_register () {
  #  -- REGISTER --
  # Registers a player onto the players database.
  global $link, $callsign, $email, $password;
  # see if there is an existing entry
  header("Content-type: text/plain");
  $result = mysql_query("SELECT * FROM players WHERE email = '$email'", $link)
    or die ("Invalid query: ". mysql_error());
  if ( mysql_num_rows($result) > 0 ) {
    print("Registration FAILED: ");
    print("A player has already registered with the email address: $email");
    exit;
  }
  $result = mysql_query("SELECT * FROM players WHERE callsign = '$callsign'", $link)
    or die ("Invalid query: ". mysql_error());
  if ( mysql_num_rows($result) > 0 ) {
    print("Registration FAILED: ");
    print("A player has already registered with the callsign: $callsign");
    exit;
  }

  # no existing entry found - proceed to complete the registration
  $alphanum = "abcdefghijklmnopqrstuvwxyz0123456789";
  $randtext = "";
  srand(microtime());
  for ( $i = 0; $i < 8; $i++ )
    $randtext .= $alphanum{rand(0,35)};
  # FIXME remove `` etc from email
  $to = urldecode($email);
  mail($to, "BZFlag player registration",
       "You have just registered a BZFlag player account with\n" .
       "    callsign: $callsign\n" .
       "    password: $password\n" .
       "To activate this account, please go to the following URL:\n\n" .
       "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'] . "?action=CONFIRM&email=$email&password=$randtext\n")
    or die ("Could not send mail");
  $curtime = time();
  $result = mysql_query("INSERT INTO players "
      . "(email, callsign, password, created, randtext, lastmod) VALUES "
      . "('$email', '$callsign', '$password', '$curtime', "
      . "'$randtext', '$curtime')", $link)
  or die ("Invalid query: ". mysql_error());
  print("Registration SUCCESSFUL: ");
  print("You will receive an email informing you on how to complete your account registration\n");
  print("While we are debugging, the link is posted here as well.:\n" .
       "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'] . "?action=CONFIRM&email=$email&password=$randtext\n");
}

function action_confirm () {
  #  -- CONFIRM --
  # Confirms a registration
  global $link, $email, $password;
  header("Content-type: text/html");
  print("<html><head><title>Confirm registration</title></head><body>\n");
  $result = mysql_query("SELECT randtext FROM players WHERE email='$email'", $link)
    or die ("Invalid query: " . mysql_error());
  $row = mysql_fetch_row($result);
  $randtext = $row[0];
  if ( $randtext == NULL ) {
    print("The account $email has already been confirmed.<br>\n");
  } else {
    if ( $password != $randtext ) {
      print("Failed to confirm registration for $email since generated key did not match<br>\n");
    } else {
      $result = mysql_query("UPDATE players SET "
	  . "randtext = NULL, "
	  . "lastmod = '" . time() . "' "
	  . "WHERE email='$email'", $link)
	or die ("Invalid query: " . mysql_error());
      print("The account for $email has been successfully activated.<br>\n");
    }
  }
  print('See <a href="http://BZFlag.org">http://BZFlag.org</a></body></html>');
}

function action_checktoken () {
  #  -- CHECKTOKEN --
  # validate player token for connecting player on a game server
  global $link, $playerid, $token;
  # TODO add grouplist support
  header("Content-type: text/plain");
  $timeout = 600; # 10 minutes while testing
  $staletime = time() - $timeout;
  $result = mysql_query("SELECT callsign FROM players "
      . "WHERE playerid = '$playerid' "
      . "AND token = '$token' "
      . "AND tokendate > $staletime", $link)
    or die ("Invalid query: " . mysql_error());
  $row = mysql_fetch_row($result);
  $callsign = $row[0];
  if ($callsign) {
    # clear tokendate so nasty game server admins can't login someplace else
    $result = mysql_query("UPDATE players SET "
	. "lastmod = '" . time() . "', "
	. "tokendate = '0' "
	. "WHERE playerid='$playerid'", $link)
      or die ("Invalid query: " . mysql_error());
    print ("$callsign:\n");
  } else
    print ("FAIL");
}

# ignore banned servers outright
if ($banlist[$_SERVER['REMOTE_ADDR']] != "") {
  # reject the connection attempt
  header("Content-type: text/plain");
  $remote_addr = $_SERVER['REMOTE_ADDR'];
  debug("Connection rejected from $remote_addr");
  die("Connection attempt rejected.  See #bzflag on irc.freenode.net");
}

debug("Connecting to the database");

# Connect to the server database persistently.
$link = mysql_pconnect($dbhost, $dbuname, $dbpass)
     or die("Could not connect: " . mysql_error());
if (!mysql_select_db($dbname)) {
  debug("Database did not exist");

  die("Could not open db: " . mysql_error());
}

# remove all inactive registered players from the table
# FIXME this should not happen on every request
$timeout = 31536000; # timeout in seconds, 365 days
$staletime = time() - $timeout;
mysql_query("DELETE FROM players WHERE lastmod < $staletime", $link)
  or die ("Could not remove inactive players" . mysql_error());

# remove all players who have not confirmed registration
# FIXME this should not happen on every request
$timeout = 259200;  # timeout in seconds, 72h
$staletime = time() - $timeout;
mysql_query("DELETE FROM players WHERE lastmod < $staletime AND randtext != NULL", $link)
  or die ("Could not remove inactive players" . mysql_error());

# Do stuff based on what the 'action' is...
switch ($action) {
case "LIST":
  action_list();
  break;
case "ADD":
  action_add();
  break;
case "REMOVE":
  action_remove();
  break;
case "REGISTER":
  action_register();
  break;
case "CONFIRM";
  action_confirm();
  break;
case "CHECKTOKEN":
  action_checktoken();
  break;
case "DEBUG":
  testform('');
  break;
default:
  # TODO dump the default form here but still close the database connection
  testform('Unknown command: \'' . $action . '\'');
}

# make sure the connection to mysql is severed
if ($link) {
  # for a transaction commit just in case
  debug("Commiting any pending transactions");
  mysql_query("COMMIT", $link);

  # debug("Closing link to database");

  # say bye bye (shouldn't need to ever really, especially for persistent..)
  #mysql_close($link);
}

debug("End session");

# Local Variables: ***
# mode:php ***
# tab-width: 8 ***
# c-basic-offset: 2 ***
# indent-tabs-mode: t ***
# End: ***
# ex: shiftwidth=2 tabstop=8

?>
