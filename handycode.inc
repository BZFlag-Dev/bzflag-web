<?

require('serversettings.php');


/** set the theme and appearance of the interface
 */
$theme = 'industrial';
if (isset($_COOKIE['theme'])) {
	if($_COOKIE['theme'] == 'industrial') {
		setcookie ("theme", 'industrial', time()+60*60*24*30);
		$theme = 'industrial';
	} elseif ($_COOKIE['theme'] == 'bluetangerine') {
		setcookie ("theme", 'bluetangerine', time()+60*60*24*30);
		$theme = 'bluetangerine';
	} elseif ($_COOKIE['theme'] == 'test') {
		setcookie ("theme", 'test', time()+60*60*24*30);
		$theme = 'test';
	} elseif($_COOKIE['theme'] == 'none') {
		setcookie ("theme", 'none', time()+60*60*24*30);
		$theme = 'none';
	}
}
if (isset($_GET['theme'])) {
	if ($_GET['theme'] == 'industrial') {
		setcookie ("theme", 'industrial', time()+60*60*24*30);
		$theme = 'industrial';
	} elseif ($_GET['theme'] == 'bluetangerine') {
		setcookie ("theme", 'bluetangerine', time()+60*60*24*30);
		$theme = 'bluetangerine';
	} elseif ($_GET['theme'] == 'test') {
		setcookie ("theme", 'test', time()+60*60*24*30);
		$theme = 'test';
	} elseif ($_GET['theme'] == 'none') {
		setcookie ("theme", 'none', time()+60*60*24*30);
		$theme = 'none';
	}
}

/** set whether the page should auto-refresh
 */
$refresh = 'no';
if (isset($_COOKIE['refresh'])) {
	if($_COOKIE['refresh'] == 'no') {
		setcookie ("refresh", 'no', time()+60*60*24*30);
		$refresh = 'no';
	} elseif($_COOKIE['refresh'] == 'yes') {
		setcookie ("refresh", 'yes', time()+60*60*24*30);
		$refresh = 'yes';
	}
}
if (isset($_GET['optionschanged'])) {
	if (isset($_GET['refresh'])) {
		if($_GET['refresh'] == 'yes') {
			setcookie ("refresh", 'yes', time()+60*60*24*30);
			$refresh = 'yes';
		} else {
			setcookie ("refresh", 'no', time()+60*60*24*30);
			$refresh = 'no';
		}
	} else {
		setcookie ("refresh", 'no', time()+60*60*24*30);
		$refresh = 'no';
	}
} elseif (isset($_GET['refresh'])) {
	if($_GET['refresh'] == 'yes') {
		setcookie ("refresh", 'yes', time()+60*60*24*30);
		$refresh = 'yes';
	} else {
		setcookie ("refresh", 'no', time()+60*60*24*30);
		$refresh = 'no';
	}
}


/*
if (isset($_COOKIE['language'])) {
	if ($_COOKIE['language'] == 'it') {
		include ('i18n_it.inc');
		setcookie ("language", 'it', time()+60*60*24*30);
	} elseif ($_COOKIE['language'] == 'de') {
		include ('i18n_de.inc');
		setcookie ("language", 'de', time()+60*60*24*30);
	} else {
		include ('i18n_en.inc');
		setcookie ("language", 'en', time()+60*60*24*30);
	}
} elseif (isset($_GET['language'])) {
	if ($_GET['language'] == 'it') {
		include ('i18n_it.inc');
		setcookie ("language", 'it', time()+60*60*24*30);
	} elseif ($_GET['language'] == 'de') {
		include ('i18n_de.inc');
		setcookie ("language", 'de', time()+60*60*24*30);
	} elseif ($_GET['language'] == 'en') {
		include ('i18n_en.inc');
		setcookie ("language", 'en', time()+60*60*24*30);
	} else {
		include ('i18n_en.inc');
		setcookie ("language", 'en', time()+60*60*24*30);
	}
} else {
	include ('i18n_en.inc');
	setcookie ("language", 'en', time()+60*60*24*30);
}
*/

/** set the title depending on how the url was called
 */
if (isset($_SERVER['SCRIPT_FILENAME'])) {
	switch (basename($_SERVER['SCRIPT_FILENAME'])) {
	case "currentplayers.php":
		$title = "Current Players";
		break;
	case "playerstats.php":
		$title = "Player Stats";
		break;
	case "playersearch.php":
		$title = "Player Search";
		break;
	case "playerinfo.php":
		$title = "Player Info - $callsign";
		break;
	case "serverinfo.php":
		$title = "Server Info - $server";
		break;
	case "serverlist.php":
		$title = "Server List";
		break;
	case "help.php":
		$title = "Help";
		break;
	case "copyright.php":
		$title = "Copyright Info";
		break;
	case "link.php":
		$title = "Link to Us";
		break;
	case "rssgenerator.php":
		$title = "RSS Feed Generator";
		break;
	case "stats.php":
	default:
		$title = "Welcome!";
		break;

	}
}


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
	$refreshed_date = date('D, M d H:i:s T Y', $row['lastrefreshed']);
	$refreshed_epoch = $row['lastrefreshed'];
	$refreshed_rfc2822 = date('r', $row['lastrefreshed']);
} else {
	$refreshed_date = "never";
}

?>
