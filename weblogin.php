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

define('IN_PHPBB', true);
$phpbb_root_path = 'bb/';
$phpEx = 'php';

include($phpbb_root_path.'includes/functions.'.$phpEx);
include($phpbb_root_path.'includes/utf/utf_tools2.'.$phpEx);
include($phpbb_root_path.'includes/utf/utf_normalizer.'.$phpEx);

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

function dumpPageHeader ( $cssURL ) {

	# tell the proxies not to cache
	header('Cache-Control: no-cache');
	header('Pragma: no-cache');
	header('Content-type: text/html');

	echo '
		<HTML>
		<head>
		<title>BZFlag - web login</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" type="text/css" href="http://my.bzflag.org/css/weblogin.css">
		';
		if ($cssURL)
			echo '<link rel="stylesheet" type="text/css" href="' . $cssURL . '">';
		
		echo '
		<link href="http://www.bzflag.org/favicon.ico" rel="shortcut icon">
		</head>
		<BODY>
		<div id="Logo"><img src="http://my.bzflag.org/images/webauth_logo.png"></div>
		<div id="Header"></div>
		<div id="OuterFrame">
			<div id="CentralFrame">
				<div id="CentralHeader">My.BZFlag.org Login Page</div>
		';
}

function dumpPageFooter () {
print('
	  			<div id="CopyrightSection"><span class="CopyrightItem">copyright &copy; <a href="http://my.bzflag.org/w/Tim_Riker">Tim Riker</a> 1993-2010&nbsp;</span></div>
	  			<div id="CentralFooter"></div>
	  		</div>
		</div>
	  	<div id="Footer"></div>
		</BODY>
		</HTML> ');
}

function action_weblogin() {
	if ( array_key_exists("url", $_REQUEST) )
		$URL =  $_REQUEST['url'];
	else
		die ('ERROR, you must pass in a URL value');
		
	$css = FALSE;
    if ( array_key_exists("css", $_REQUEST) )
		$css =  $_REQUEST['css'];
	
	$sessionKey = rand();
	
	$_SESSION['webloginformkey'] = $sessionKey;

	$parsedURL = parse_url($URL);

	dumpPageHeader($css);
	echo '
					<div id="Alert"><img src="http://my.bzflag.org/images/webauth_alert.png"></div>
					<div id="InfoHeader">
							The site <b>' . $parsedURL["host"] . '</b> is requesting a login using your BZFlag global login<br>
							Please enter your username and password in the fields below<br>
							No personal information will be sent to the requesting site (like your password)
					</div>
					
					<div id="LoginFormSection">
							
							<form id="LoginForm" action="'. $_SERVER['SCRIPT_NAME'] . '" method="POST" >
							<div id="Username">Username <INPUT id="UsernameField" type ="text" name="username"></div>
							<div id="Password">Password <INPUT id="PasswordField" type ="password"  name ="password"></div>
							<INPUT type ="hidden" name="url" value="'. htmlentities($URL) .'"><br>
							<INPUT type ="hidden" name="action" value="webvalidate"><br>
							<INPUT type ="hidden" name="key" value="'.$sessionKey.'"><br>
							<div id="LoginButtonSection"><INPUT id="LoginButton" type="submit" value="login"></div>
							</form>
							</div>
						';
	dumpPageFooter();
}

function action_webvalidate() {

	global $bbdbname, $dbname, $link;
	
	$Key = "";
	$formKey = $_SESSION['webloginformkey'];

    if ( array_key_exists("key", $_REQUEST) )
		$Key =  $_REQUEST['key'];
	
	if ( array_key_exists("url", $_REQUEST) )
		$URL =  $_REQUEST['url'];
	else
		die ('ERROR, you must pass in a URL value');

	if ( array_key_exists("url", $_REQUEST) )
		$URL =  $_REQUEST['url'];
	else
		die ('ERROR, you must pass in a URL value');

	if ( array_key_exists("username", $_REQUEST) )
		$username =  utf8_clean_string($_REQUEST['username']);
	else
		die ('ERROR, you must pass in a USERNAME value');

	if ( array_key_exists("password", $_REQUEST) )
		$password =  $_REQUEST['password'];
	else
		die ('ERROR, you must pass in a PASSWORD value');


    if (!mysql_select_db($bbdbname))
	{
      die('Unknown Error');
    }
	
	$validReferer = false;
	
	$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : FALSE;
	
	if ($referer)
		$validReferer = strncmp($_SERVER['SCRIPT_NAME'],$referer,count($_SERVER['SCRIPT_NAME']));
	
	if ($Key != $formKey || !$validReferer)
	{
		dumpPageHeader(FALSE);
		echo'
			<div id="Error">
				
					The website you are loging in from is attempting to circumvent a part of the BZFlag weblogin system<br>
					Please contact the site owner to have them rectify the problem. If the site in question had asked you for password, it is possible that the site may have stored your information. It is highly recommended that users who see this message change their password immediately.
				</div>
		';
		
		dumpPageFooter();
	}
	else
	{
		$result = mysql_query(	"SELECT user_id, user_password FROM bzbb3_users "
								. "WHERE username_clean='$username' "
								. "AND user_inactive_reason=0", $link)
								  or die ("Invalid query: " . mysql_error());
	
		$row = mysql_fetch_row($result);
		$playerid = $row[0];
	
		if (!$playerid || !phpbb_check_hash($password, $row[1]))
		{
			dumpPageHeader(FALSE);
			echo'<div id="Error"><b>The username or password you entered was invalid.</b></div>';
			dumpPageFooter();
		}
		else
		{
		  srand(microtime() * 100000000);
		  $token = rand(0,2147483647);
	
		  $result = mysql_query("UPDATE bzbb3_users SET "
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
		  die('Could not open db: ');
		}
	}
}

// start of real script

session_start();
# Connect to the server database persistently.
$link = mysql_pconnect($dbhost, $dbuname, $dbpass)
     or die('Could not connect: ');
if (!mysql_select_db($dbname))
  die('Could not open db: ');

@mysql_query("SET NAMES 'utf8'", $link);

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
