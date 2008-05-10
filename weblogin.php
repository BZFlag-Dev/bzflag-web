<?php
// bzlogin.php
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
$debugFile	= 'bzfls.log';

// define dbhost/dbuname/dbpass/dbname here
// NOTE it's .php so folks can't read the source
include('/etc/bzflag/serversettings.php');

# for banning.  provide key => value pairs where the key is an
# ip address. value is not used at present.
# FIXME this should be in an sql table with a remote admin interface
$banlist = array(
  '68.109.43.46' => 'knightmare.kicks-ass.net',
#  '127.0.0.1' => 'localhost'
  '66.189.4.29' => 'axl rose',
  '134.241.194.13' => 'axl rose',
  '255.255.255.255' => 'globalbroadcast'
);

$thisURL = 'http://my.bzflag.org/weblogin.php';
$listServerURL = 'http://my.bzflag.org/db/';

function dumpPageHeader () {

	# tell the proxies not to cache
	header('Cache-Control: no-cache');
	header('Pragma: no-cache');
	header('Content-type: text/html');

	print ('
		<HTML>
		<head>
		<title>BZFlag - web login</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" type="text/css" href="http://www.bzflag.org/general.css">
		<link href="http://www.bzflag.or/favicon.ico" rel="shortcut icon">
		</head>
		<BODY>
		<table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#DDDDDD">
		<tr height="50" valign="top">
			<td colspan="2">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td bgcolor="#013571" align="right"><img src="http://www.bzflag.org/images/logo2-1.jpg" alt="logo"></td>
						<td bgcolor="#818181" align="left"><img src="http://www.bzflag.org/images/logo2-2.jpg" alt=""></td>
					</tr>
				 </table>
			</td>
		</tr>
		<tr valign="top">
			<td>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
					<tr>
						<td valign="top"><center><b>my.bzflag.org login page</b></center></td>
					</tr> ');
}

function dumpPageFooter () {
print('</table>
			</td>
		</tr>
		<tr valign="bottom">
		<td bgcolor="#000000" cellpadding="2">
			<table width="100%" border="0" cellpadding="2" bgcolor="#FFFFFF">
				<tr>
					<td align="right">
						<span class="copyright">copyright &copy; <a href="http://www.bzflag.org/wiki/CurrentMaintainer">CurrentMaintainer</a> 1993-2005&nbsp;</span>
					</td>
				</tr>
				<tr>
					<td>
						<table width="100%" border="0" cellpadding="2" bgcolor="#FFFFFF"><tr><td align="center">
						<a href="http://www.opengl.org/"><img src="http://www.bzflag.org//images/opengl.gif" alt="opengl" width="88" height="31" border="0"></a>
						<a href="http://sourceforge.net/project/?group_id=3248"><img src="http://sourceforge.net/sflogo.php?group_id=3248&amp;type=1" width="88" height="31" alt="sourceforge" border="0"></a>
						<a href="http://sourceforge.net/donate/index.php?group_id=3248"><img src="http://images.sourceforge.net/images/project-support.jpg" width="88" height="32" border="0" alt="Support This Project" /> </a>
						<a href="http://www.linuxgames.com/"><img src="http://www.bzflag.org/images/linuxgames.gif" width="88" height="31" alt="linuxgames" border="0"></a>
						<a href="http://www.telefragged.com/"><img src="http://www.bzflag.org//images/telefragged.gif" width="88" height="31" alt="telefragged" border="0"></a>
					</td>
				</tr>
			</table>
			</td></tr></table>
		</td>
		</tr>
		</table>

		</BODY>
		</HTML> ');
}

function action_weblogin() {
	if ( array_key_exists("url", $_REQUEST) )
		$URL =  $_REQUEST['url'];
	else
		die ('ERROR, you must pass in a URL value');

	$parsedURL = parse_url($URL);

	dumpPageHeader();
	// TODO, make this all prety
	print ('
					<tr>
						<td valign="top">
							The site <b>' . $parsedURL["host"] . '</b> is requesting a login using your bzflag global login<br>
							Please enter your username and password in the fields below<br>
							No personal information will be sent to the requesting site (like your password)
						</td>
					</tr>
					<tr>
						<td valign="top">
							<table width="40%" border="0" cellpadding="0" cellspacing="0">
							<form action="http://my.bzflag.org/weblogin.php" method="POST" >
							<tr>
								<td>Username <INPUT type ="text" name="username"></td>
							</tr>
							<tr>
								<td>Password <INPUT type ="password"  name ="password"></td>
							</tr>
							<INPUT type ="hidden" name="url" value="'. htmlentities($URL) .'"><br>
							<INPUT type ="hidden" name="action" value="webvalidate"><br>
							<tr>
								<td><INPUT type="submit" value="login"></td>
							</tr>
							</form>
							</table>
						</td>
					</tr> ');
	dumpPageFooter();
}

function action_webvalidate() {

	global $bbdbname, $dbname, $link;

	if ( array_key_exists("url", $_REQUEST) )
		$URL =  $_REQUEST['url'];
	else
		die ('ERROR, you must pass in a URL value');

	if ( array_key_exists("username", $_REQUEST) )
		$username =  $_REQUEST['username'];
	else
		die ('ERROR, you must pass in a USERNAME value');

	if ( array_key_exists("password", $_REQUEST) )
		$password =  $_REQUEST['password'];
	else
		die ('ERROR, you must pass in a PASSWORD value');


    if (!mysql_select_db($bbdbname))
	{
      die('Could not open db:' . $bbdbname .' ' . mysql_error());
    }

    $result = mysql_query(	"SELECT user_id FROM phpbb_users "
							. "WHERE username='$username' "
							. "AND user_password=MD5('$password')"
							. "AND user_active=1", $link)
							  or die ("Invalid query: " . mysql_error());

    $row = mysql_fetch_row($result);
    $playerid = $row[0];

    if (!$playerid)
	{
		dumpPageHeader();
		print('
			<tr>
				<td valign="top">
					<b>The username or password you entered was invalid.</b>
				</td>
			</tr>
		');
		dumpPageFooter();
	}
    else
	{
      srand(microtime() * 100000000);
      $token = rand(0,2147483647);

      $result = mysql_query("UPDATE phpbb_users SET "
							  . "user_token='$token', "
							  . "user_tokendate='" . time() . "', "
							  . "user_tokenip='" . $_SERVER['REMOTE_ADDR'] . "' "
							  . "WHERE user_id='$playerid'", $link)
		or die ("Invalid query: ". mysql_error());

//	$redirURL = $URL . '?username=' . $username . '&token=' . $token;

// let them specify the paramaters, we'll just replace them with real info
	$redirURL = str_replace(Array('%TOKEN%', '%USERNAME%'), Array($token, urlencode($username)), $URL);

	header('location: ' . $redirURL);
    }
    if (!mysql_select_db($dbname))
	{
      die('Could not open db: ' . mysql_error());
    }
}

// start of real script


# Connect to the server database persistently.
$link = mysql_pconnect($dbhost, $dbuname, $dbpass)
     or die('Could not connect: ' . mysql_error());
if (!mysql_select_db($dbname))
  die('Could not open db: ' . mysql_error());

// start of script
// figure out what we are doing
if ( array_key_exists('action', $_REQUEST) )
	$action =  $_REQUEST['action'];
else
	$action = 'weblogin';

switch ($action) {
case 'weblogin':
	action_weblogin();
	break;

case 'webvalidate':
	action_webvalidate();
break;

default:
	 echo 'ERROR = 404, WTF? Command ' . $action ; ' not known';
	 break;
}

# make sure the connection to mysql is severed
if ($link) {
  # for a transaction commit just in case
  mysql_query('COMMIT', $link);

  # say bye bye (shouldn't need to ever really, especially for persistent..)
  #mysql_close($link);
  }
?>
