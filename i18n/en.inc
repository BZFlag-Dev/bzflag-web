<?

switch ($SCRIPT_FILENAME) {
   case "index.php":
      $i18n[title] = "Welcome!";
      break;
   case "currentplayers.php":
      $i18n[title] = "Current Players";
      break;
   case "playerstats.php":
      $i18n[title] = "Player Stats";
      break;
   case "playersearch.php":
      $i18n[title] = "Player Search";
      break;
   case "playerinfo.php":
      $i18n[title] = "Player Info";
      break;
   case "serverinfo.php":
      $i18n[title] = "Server Info";
      break;
   case "serverlist.php":
      $i18n[title] = "Server List";
      break;
   case "help.php":
      $i18n[title] = "Help";
      break;
   case "copyright.php":
      $i18n[title] = "Copyright Info";
      break;
}

$i18n['Last refreshed:'] = 'Last refreshed:';
$i18n['view source'] = 'view source';
$i18n['this page'] = 'this page';
$i18n['header'] = 'header';
$i18n['footer'] = 'footer';

$i18n['News:'] = 'News:';

$i18n['Commonly queried servers:'] = 'Commonly queried servers:';
$i18n['Current facts: (sampled every 2 minutes)'] = 'Current facts: (sampled every 2 minutes)';
$i18n['Most popular server'] = 'Most popular server';
$i18n['Player with highest score'] = 'Player with highest score';
$i18n['Player with highest Strength Index'] = 'Player with highest Strength Index';
$i18n['Player with lowest score'] = 'Player with lowest score';
$i18n['Player with lowest Strength Index'] = 'Player with lowest Strength Index';

function i18n_teamonserverkickingbutt1 ($server) {
	return "Team on $server that is kicking butt";
}
function i18n_teamonserverkickingbutt2 ($server) {
	return "Team on $server that is doing the same";
}

function i18n_serverwithplayers ($server, $numplayers) {
	return "$server with $numplayers players";
}
function i18n_playerwithscore ($player, $score) {
	return "$player with $score points";
}
function i18n_playerwithsi ($player, $strengthindex) {
	return "$player with Strength Index of $strengthindex";
}
function i18n_teamwithscore ($team, $score) {
	return "$team with $score points";
}

$i18n['Nobody'] = 'Nobody';
$i18n['No info available'] = 'No info available';

$i18n['HOME'] = 'HOME';
$i18n['CURRENT PLAYERS'] = 'CURRENT PLAYERS';
$i18n['PLAYER STATS'] = 'PLAYER STATS';
$i18n['PLAYER SEARCH'] = 'PLAYER SEARCH';
$i18n['HELP'] = 'HELP';
$i18n['IRC CHAT'] = 'IRC CHAT';
$i18n['BZFlag Links'] = 'BZFlag Links';

$i18n['Information sampled every 2 minutes'] = 'Information sampled every 2 minutes';
$i18n['Callsign'] = 'Callsign';
$i18n['Server'] = 'Server';
$i18n['Score'] = 'Score';
$i18n['Strength Index'] = 'Strength Index';
$i18n['Team'] = 'Team';
$i18n['Last Server'] = 'Last Server';

$i18n['List Server Error'] = 'Either no one is playing, Noodleman has borked something again, or the list server has been eaten by vampire monkeys from the Bronx!';

$i18n['Red'] = 'Red';
$i18n['Green'] = 'Green';
$i18n['Blue'] = 'Blue';
$i18n['Purple'] = 'Purple';
$i18n['Rogue'] = 'Rogue';
$i18n['Observer'] = 'Observer';
$i18n['Rabbit'] = 'Rabbit';

$i18n['Observers'] = 'Observers';

function i18n_statsforplayer ($player) {
	return "Stats for $player";
}
function i18n_noinfoforplayer ($player) {
	return "No information available for $player.";
}

$i18n['Email String'] = 'Email String';
$i18n['High Score'] = 'High Score';
$i18n['Low Score'] = 'Low Score';
$i18n['High Strength Index'] = 'High Strength Index';
$i18n['Low Strength Index'] = 'Low Strength Index';
$i18n['High Kill Ratio'] = 'High Kill Ratio';
$i18n['Low Kill Ratio'] = 'Low Kill Ratio';
$i18n['Most Wins'] = 'Most Wins';
$i18n['Most Losses'] = 'Most Losses';
$i18n['Last Seen'] = 'Last Seen';
$i18n['Last Server'] = 'Last Server';

function i18n_currentinfoforserver ($server) {
	return "Current info for $server";
}
function i18n_noinfoforserver ($server) {
	return "No information available for $server.";
}

$i18n['Size'] = 'Size';
$i18n['Wins'] = 'Wins';
$i18n['Losses'] = 'Losses';

$i18n['Hall of Fame'] = 'Hall of Fame';
$i18n['High Scores'] = 'High Scores';
$i18n['High Strength Indexes'] = 'High Strength Indexes';

$i18n['Hall of Shame'] = 'Hall of Shame';
$i18n['Low Scores'] = 'Low Scores';
$i18n['Low Strength Indexes'] = 'Low Strength Indexes';

$i18n['Go to:'] = 'Go to:';
$i18n['Previous'] = 'Previous';
$i18n['Next'] = 'Next';
$i18n['Top'] = 'Top';

?>

