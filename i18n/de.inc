<?

switch ($SCRIPT_FILENAME) {
   case "index.php":
      $i18n[title] = "Willkommen!";
      break;
   case "currentplayers.php":
      $i18n[title] = "Momentane Spieler";
      break;
   case "playerstats.php":
      $i18n[title] = "Spieler Stats";
      break;
   case "playersearch.php":
      $i18n[title] = "Spielersuche";
      break;
   case "playerinfo.php":
      $i18n[title] = "Spieler Info";
      break;
   case "serverinfo.php":
      $i18n[title] = "Server Info";
      break;
   case "help.php":
      $i18n[title] = "Hilfe";
      break;
   case "copyright.php":
      $i18n[title] = "Copyright Info";
      break;
}

$i18n['Last refreshed:'] = 'Zuletzt gerefreshed:';
$i18n['view source'] = 'Betrachtung der Quelle';
$i18n['this page'] = 'Diese Seite';
$i18n['header'] = 'Kopfzeile';
$i18n['footer'] = 'Fusszeile';

$i18n['News:'] = 'Nachrichten:';

$i18n['Commonly queried servers:'] = 'Commonly queried servers:';
$i18n['Current facts: (sampled every 2 minutes)'] = 'Momentane Fakten: (jede 2 Minuten)';
$i18n['Most popular server'] = 'Most popular server';
$i18n['Player with highest score'] = 'Spieler mit höchster Punktzahl';
$i18n['Player with highest Strength Index'] = 'Spieler mit höchster Stärke Index';
$i18n['Player with lowest score'] = 'Spieler mit niedrigster Punktzahl';
$i18n['Player with lowest Strength Index'] = 'Spieler mit niedrigster Stärke Index';

function i18n_teamonserverkickingbutt1 ($server) {
	return "Mannschaft auf dem $server das am Gewinnen ist";
}
function i18n_teamonserverkickingbutt2 ($server) {
	return "Mannschaft auf dem $server dass, dass gleiche macht";
}

function i18n_serverwithplayers ($server, $numplayers) {
	return "$server mit $numplayers Spieler";
}
function i18n_playerwithscore ($player, $score) {
	return "$player mit $score ";
}
function i18n_playerwithsi ($player, $strengthindex) {
	return "$player mit Stärke Index von $strengthindex";
}
function i18n_teamwithscore ($team, $score) {
	return "$team mit $score";
}

$i18n['Nobody'] = 'Niemand';
$i18n['No info available'] = 'Keine Info zur Verfügung';

$i18n['HOME'] = 'ZUHAUSE';
$i18n['CURRENT PLAYERS'] = 'MOMENTANE SPIELER';
$i18n['PLAYER STATS'] = 'SPIELER STATS';
$i18n['PLAYER SEARCH'] = 'SPIELER SUCHE';
$i18n['HELP'] = 'HILFE';
$i18n['IRC CHAT'] = 'IRC CHAT';
$i18n['BZFlag Links'] = 'BZFlag Links';

$i18n['Information sampled every 2 minutes'] = 'Information sampled every 2 minutes';
$i18n['Callsign'] = 'Rufname';
$i18n['Server'] = 'Server';
$i18n['Score'] = 'Ergebniss';
$i18n['Strength Index'] = 'Stärke Index';
$i18n['Team'] = 'Mannschaft';
$i18n['Last Server'] = 'Letzter Server';

$i18n['List Server Error'] = 'Entweder spielt niemand oder Noodleman hat wieder was versaut,  oder die Liste vom Server wurde von Vampiraffen gefressen von der Bronx!';

$i18n['Red'] = 'Rot';
$i18n['Green'] = 'Grün';
$i18n['Blue'] = 'Blau';
$i18n['Purple'] = 'Violett';
$i18n['Rogue'] = 'Rogue';

function i18n_statsforplayer ($player) {
	return "Stats für $player";
}
function i18n_noinfoforplayer ($player) {
	return "Keine Info für den $player vorhanden.";
}

$i18n['Email String'] = 'Email String';
$i18n['High Score'] = 'Höchste Punktzahl';
$i18n['Low Score'] = 'Niedrigste Punktzahl';
$i18n['High Strength Index'] = 'Hohe Stärke Index';
$i18n['Low Strength Index'] = 'Niedrige Stärke Index';
$i18n['High Raw Kill Ratio'] = 'High Raw Kill Ratio';
$i18n['Low Raw Kill Ratio'] = 'Low Raw Kill Ratio';
$i18n['Most Wins'] = 'Am meisten Gewonnen';
$i18n['Most Losses'] = 'Am meisten verloren';
$i18n['Last Seen'] = 'Zuletzt gesehen';
$i18n['Last Server'] = 'Letzter Server';

function i18n_currentinfoforserver ($server) {
	return "Momentane Info zu dem $server";
}
function i18n_noinfoforserver ($server) {
	return "Keine Info erhältlich für den $server.";
}

$i18n['Size'] = 'Größe';
$i18n['Wins'] = 'Gewinne';
$i18n['Losses'] = 'Verloren';

$i18n['Hall of Fame'] = 'Der Raum der Berühmtheiten';
$i18n['High Scores'] = 'Hohe Ergebnisse';
$i18n['High Strength Indexes'] = 'Hohe Stärke Indexe';

$i18n['Hall of Shame'] = 'Der Raum der Versager';
$i18n['Low Scores'] = 'Niedrige Ergebnisse';
$i18n['Low Strength Indexes'] = 'Niedrige Stärke Indexe';

$i18n['Go to:'] = 'Gehe zu:';
$i18n['Previous'] = 'Vorhergehendes';
$i18n['Next'] = 'Nächstes';
$i18n['Top'] = 'Obere Seite';

?>


