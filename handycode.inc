<?

if ($_GET[theme] == 'industrial') {
	setcookie ("theme", 'industrial', time()+60*60*24*30);
	$theme = 'industrial';
} elseif ($_GET[theme] == 'none') {
	setcookie ("theme", 'none', time()+60*60*24*30);
	$theme = 'none';
} elseif ($_GET[theme] == 'bluetangerine') {
	setcookie ("theme", 'bluetangerine', time()+60*60*24*30);
	$theme = 'bluetangerine';
} else {
	if($_COOKIE[theme] == 'industrial') {
		setcookie ("theme", 'industrial', time()+60*60*24*30);
		$theme = 'industrial';
	} elseif($_COOKIE[theme] == 'none') {
		setcookie ("theme", 'none', time()+60*60*24*30);
		$theme = 'none';
	} else {
		setcookie ("theme", 'bluetangerine', time()+60*60*24*30);
		$theme = 'bluetangerine';
	}
}
	
if($_GET[refresh] == '' and $optionschanged != 'yes') {
	setcookie ("refresh", 'yes', time()+60*60*24*30);
	$refresh = 'yes';
} elseif($_GET[refresh] == '' and $optionschanged == 'yes') {
	setcookie ("refresh", 'no', time()+60*60*24*30);
	$refresh = 'no';
} elseif($_GET[refresh] == 'yes') {
	setcookie ("refresh", 'yes', time()+60*60*24*30);
	$refresh = 'yes';
} else {
	if($_COOKIE[refresh] == 'no') {
		setcookie ("refresh", 'no', time()+60*60*24*30);
		$refresh = 'no';
	} else {
		setcookie ("refresh", 'yes', time()+60*60*24*30);
		$refresh = 'yes';
	}
}

/*
if ($_GET[language] == 'it') {
	include ('i18n_it.inc');
	setcookie ("language", 'it', time()+60*60*24*30);
} elseif ($_GET[language] == 'de') {
	include ('i18n_de.inc');
	setcookie ("language", 'de', time()+60*60*24*30);
} elseif ($_GET[language] == 'en') {
	include ('i18n_en.inc');
	setcookie ("language", 'en', time()+60*60*24*30);
} else {

	if ($_COOKIE[language] == 'it') {
		include ('i18n_it.inc');
		setcookie ("language", 'it', time()+60*60*24*30);
	} elseif ($_COOKIE[language] == 'de') {
		include ('i18n_de.inc');
		setcookie ("language", 'de', time()+60*60*24*30);
	} else {
		include ('i18n_en.inc');
		setcookie ("language", 'en', time()+60*60*24*30);
	}
}
*/

switch ($SCRIPT_FILENAME) {
   case "/var/www/db/stats.php":
      $title = "Welcome!";
      break;
   case "/var/www/db/currentplayers.php":
      $title = "Current Players";
      break;
   case "/var/www/db/playerstats.php":
      $title = "Player Stats";
      break;
   case "/var/www/db/playersearch.php":
      $title = "Player Search";
      break;
   case "/var/www/db/playerinfo.php":
      $title = "Player Info";
      break;
   case "/var/www/db/serverinfo.php":
      $title = "Server Info";
      break;
   case "/var/www/db/serverlist.php":
      $title = "Server List";
      break;
   case "/var/www/db/help.php":
      $title = "Help";
      break;
   case "/var/www/db/copyright.php":
      $title = "Copyright Info";
      break;
   case "/var/www/db/link.php":
      $title = "Link to Us";
      break;
   case "/var/www/db/rssgenerator.php":
      $title = "RSS Feed Generator";
      break;
}

require_once ('serversettings.php');

$link = mysql_pconnect($dbhost, $dbuname, $dbpass) or die("Could not connect");
mysql_select_db("bzflag", $link) or die("Could not select database");

$separator = '<div class="center">&middot; &middot; &middot; &middot; &middot; &middot; &middot;</div>';

function num_rows ($query)
{
	if ($result = mysql_query ($query)) {
		$numrows = mysql_num_rows($result);
		mysql_free_result ($result);
		return $numrows;
	} else {
	  echo mysql_error();
		return 0;
	}
}

//function callsignlink ($callsign)
function callsignlink ($callsign, $email = false, $team = false, $link = true)
{
$teamclass = (($team == 'R') ? "class=\"teamred\" " :
	     (($team == 'G') ? "class=\"teamgreen\" " :
	     (($team == 'B') ? "class=\"teamblue\" " :
	     (($team == 'P') ? "class=\"teampurple\" " :
	     false
		))));

 if ($link) {
	return "<a ".(($teamclass) ? $teamclass : "") . "href=\"playerinfo.php?callsign=".(urlencode($callsign)) . "\">$callsign".(($email)?" ($email)":'')."</a>";
 } else {
   return "playerinfo.php?callsign=".(urlencode($callsign));
 }

}

function serverlink ($server, $link = true)
{
  if ($link) {
    return "<a href=\"serverinfo.php?server=".(urlencode($server))."\">$server</a>";
  } else {
    return "serverinfo.php?server=".(urlencode($server));
  }
}

$teamnames = array(
		   'R' => 'Red',
		   'G' => 'Green',
		   'B' => 'Blue',
		   'P' => 'Purple',
		   'O' => 'Observer',
		   'H' => 'Rabbit',
		   'X' => 'Rogue',
		   );
		   

function teamname ($team)
{
	global $teamnames;

	return '<span class="team'.strtolower($teamnames[$team]).'">'.$teamnames[$team].'</span>';
}


$lastrefreshed = mysql_query ("SELECT * FROM `miscinfo`");

if ($row = mysql_fetch_array($lastrefreshed, MYSQL_ASSOC)) {
  $refreshed_date = date('D, M d H:i:s T Y', $row[lastrefreshed]);
  $refreshed_epoch = $row[lastrefreshed];
  $refreshed_rfc2822 = date('r', $row[lastrefreshed]);
}

?>
