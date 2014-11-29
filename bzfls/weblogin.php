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

// Weblogin settings
$config = Array(
  // Used for checking for cross-site form submission. Should contain the
  // full domain name of the site hosting the weblogin.php script.
  'ourdomain' => 'my.bzflag.org',
);

define('IN_PHPBB', true);
$phpbb_root_path = '../../forums.bzflag.org/htdocs/';
$phpEx = 'php';

include($phpbb_root_path.'includes/functions.'.$phpEx);
include($phpbb_root_path.'includes/utf/utf_tools.'.$phpEx);
include($phpbb_root_path.'includes/utf/utf_normalizer.'.$phpEx);

// define dbhost/dbuname/dbpass/dbname here
// NOTE it's .php so folks can't read the source
include('/etc/bzflag/serversettings.php');

function dumpPageHeader () {

	# tell the proxies not to cache
	header('Cache-Control: no-cache');
	header('Pragma: no-cache');
	header('Content-type: text/html');

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <title>My.BZFlag.org Login Page</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <link rel="stylesheet" href="css/weblogin.css">
  <link href="http://www.bzflag.org/favicon.ico" rel="shortcut icon">
</head>
<body>
  <div id="container">
    <div id="header">
      <img src="http://my.bzflag.org/images/webauth_logo.png" width="184" height="130" alt="">
      <h1>My.BZFlag.org Login</h1>
    </div>
    <div id="main">
<?php
}

function dumpPageFooter () {
?>
    </div>
    <div id="footer">copyright &copy; <a href="http://my.bzflag.org/w/Tim_Riker">Tim Riker</a> 1993-2010</div>
  </div>
</body>
</html>
<?php
}

function action_weblogin() {
	
	  global $bbdbname, $dbname, $link;

  if ( array_key_exists("url", $_REQUEST) )
    $URL =  $_REQUEST['url'];
  else
    die ('ERROR, you must pass in a URL value');
		
  $sessionKey = rand();
	
  $_SESSION['webloginformkey'] = $sessionKey;

  $parsedURL = parse_url($URL);
	
  if (!isset($parsedURL["host"]))
    die ('ERROR, you must pass in a URL value');

	$hostkey = md5($parsedURL["host"]);
	
	$wlu = $hostkey.'wlu';
	$wlk = $hostkey.'wlk';
	
	if (isset($_COOKIE[$wlu]) && isset($_COOKIE[$wlk]))
	{
		// try autologin
		if (mysql_select_db($bbdbname))
    {
			$uid = $_COOKIE[$wlu];
			
       $result = mysql_query("SELECT user_id, user_password, username_clean FROM bzbb3_users "
				. "WHERE user_id='".mysql_real_escape_string($uid)."' "
				. "AND user_inactive_reason=0", $link);
			 
			 if ($result)
			 {
					$row = mysql_fetch_row($result);
					$playerid = $row[0];
					
					$keyhash = md5($parsedURL["host"] . $row[1]); 
					if ($keyhash ==$_COOKIE[$wlk])
					{
						 srand(microtime() * 100000000);
							$token = rand(0,2147483647);
						
							$result = mysql_query("UPDATE bzbb3_users SET "
									. "user_token='$token', "
									. "user_tokendate='" . time() . "', "
									. "user_tokenip='" . $_SERVER['REMOTE_ADDR'] . "' "
									. "WHERE user_id='$playerid'", $link);
							if ($result)
							{
								 $redirURL = str_replace(Array('%TOKEN%', '%USERNAME%'), Array($token, urlencode($row[2])), $URL);
									header('location: ' . $redirURL);
									return;
							}
					}
			 }
    }
	}
	
	dumpPageHeader();
?>
      <div id="information">
        The website <b><?php echo htmlentities($parsedURL['host']); ?></b> is requesting a login using your BZFlag global login.<br> 
        Please provide your username and password on this form.<br>
        Your password will <b>NOT</b> be sent to the requesting site.
      </div>
      
      <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="POST">
        <div id="form"> 
          <input type="hidden" name="url" value="<?php echo htmlentities($URL); ?>"> 
          <input type="hidden" name="action" value="webvalidate">
          <input type="hidden" name="key" value="<?php echo $sessionKey; ?>">
          <label id="usernamelabel">Username: <input name="username" id="username"></label> 
          <label id="passwordlabel">Password: <input type="password" name="password" id="password"></label>
          <label id="rememberlabel"><input type="checkbox" name="remember" id="remember"> Automatically login when going to <b><?php echo htmlentities($parsedURL['host']); ?></b></label> 
          <label id="loginlabel"><input type="submit" id="login" value="login"></label>
      </div>
      </form>
<?php
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
		
	$parsedURL = parse_url($URL);
	
  if (!isset($parsedURL["host"]))
    die ('ERROR, you must pass in a URL value');

  if ( array_key_exists("username", $_REQUEST) )
    $username =  utf8_clean_string($_REQUEST['username']);
  else
    die ('ERROR, you must pass in a USERNAME value');

  if ( array_key_exists("password", $_REQUEST) )
    $password =  $_REQUEST['password'];
  else
    die ('ERROR, you must pass in a PASSWORD value');
		
	$remember = FALSE;
	if ( array_key_exists("remember", $_REQUEST) )
    $remember =  $_REQUEST['remember'];

  if (!mysql_select_db($bbdbname))
    {
      die('Unknown Error');
    }
	
	$refererParts = parse_url($_SERVER['HTTP_REFERER']);
	$validReferer = (empty($_SERVER['HTTP_REFERER']) || empty($refererParts['host']) || $refererParts['host'] == $GLOBALS['config']['ourdomain']);
	
  if ($Key != $formKey || !$validReferer)
    {
      dumpPageHeader();
?>
      <div id="information">
        The website <b><?php echo htmlentities($parsedURL['host']); ?></b> is attempting to circumvent a part of the BZFlag weblogin system<br> 
        Please contact the site owner to have them rectify the problem.<br>
        If the website in question had asked you for password, it is possible that the site may have stored your information. It is highly recommended you change your password immediately.
      </div>
<?php
      dumpPageFooter();
    }
  else
    {
      $result = mysql_query("SELECT user_id, user_password, username FROM bzbb3_users "
				. "WHERE username_clean='".mysql_real_escape_string($username)."' "
				. "AND user_inactive_reason=0", $link)
	or die ("Invalid query: " . mysql_error());
	
      $row = mysql_fetch_row($result);
      $playerid = $row[0];
      $username_real = $row[2];
	
		if (!$playerid || !phpbb_check_hash($password, $row[1]))
		{
	  	dumpPageHeader();
?>
      <div id="information">
        The username or password you entered was invalid.
      </div>
<?php
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
	$hostkey = md5($parsedURL["host"]);
	
	if ($remember)
	{
		$wlu = $hostkey.'wlu';
		$wlk = $hostkey.'wlk';
		setcookie($wlu, $playerid , time()+1209600); 
		$key = md5($parsedURL["host"] . $row[1]);
    setcookie($wlk, $key , time()+1209600); 
  }
	else
	{
		setcookie($hostkey.'webloginuser'.'webloginuser', "" , time()-3600); 
		setcookie($hostkey.'webloginkey'.'webloginkey', "" , time()-3600); 
	}
	
	// let them specify the paramaters, we'll just replace them with real info
	  $redirURL = str_replace(Array('%TOKEN%', '%USERNAME%'), Array($token, urlencode($username_real)), $URL);
	
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

$link = mysql_connect($dbhost, $dbuname, $dbpass)
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
