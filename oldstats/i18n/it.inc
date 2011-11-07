<?

switch ($SCRIPT_FILENAME) {
   case "index.php":
      $i18n[title] = "Benvenuti!";
      break;
   case "currentplayers.php":
      $i18n[title] = "Chi gioca adesso?";
      break;
   case "playerstats.php":
      $i18n[title] = "Graduatorie";
      break;
   case "playersearch.php":
      $i18n[title] = "Cerca un giocatore";
      break;
   case "playerinfo.php":
      $i18n[title] = "Info Giocatori";
      break;
   case "serverinfo.php":
      $i18n[title] = "Server Info";
      break;
   case "help.php":
      $i18n[title] = "Aiuto";
      break;
   case "copyright.php":
      $i18n[title] = "Copyright Info";
      break;
}

$i18n['Last refreshed:'] = 'Ultimo aggiornamento:';
$i18n['view source'] = 'Vedi i sorgenti';
$i18n['this page'] = 'questa pagina';
$i18n['header'] = 'intestazione';
$i18n['footer'] = 'fondo pagina';

$i18n['News:'] = 'Notizie:';

$i18n['Commonly queried servers:'] = 'Server pi&ugrave; richiesti:';
$i18n['Current facts: (sampled every 2 minutes)'] = 'Attualit&agrave;: (ogni 2 minuti)';
$i18n['Most popular server'] = 'Server pi&ugrave; popolari';
$i18n['Player with highest score'] = 'Giocatore con il massimo punteggio';
$i18n['Player with highest Strength Index'] = 'Giocatore con il pi&ugrave; alto Indice di Forza';
$i18n['Player with lowest score'] = 'Giocatore con il minimo punteggio';
$i18n['Player with lowest Strength Index'] = 'Giocatore con il pi&ugrave; basso Indice di Forza';

function i18n_teamonserverkickingbutt1 ($server) {
        return "Team su $server che sta rompendo le ossa agli altri";
}
function i18n_teamonserverkickingbutt2 ($server) {
        return "Team su $server che sta facendo lo stesso";
}

function i18n_serverwithplayers ($server, $numplayers) {
        return "$server con $numplayers giocatori";
}
function i18n_playerwithscore ($player, $score) {
        return "$player con $score punti";
}
function i18n_playerwithsi ($player, $strengthindex) {
        return "$player con Indice di Forza di $strengthindex";
}
function i18n_teamwithscore ($team, $score) {
        return "$team con $score punti";
}

$i18n['Nobody'] = 'Nessuno';
$i18n['No info available'] = 'Nessuna informazione disponibile';

$i18n['HOME'] = 'HOME';
$i18n['CURRENT PLAYERS'] = 'CHI STA GIOCANDO';
$i18n['PLAYER STATS'] = 'GRADUATORIE';
$i18n['PLAYER SEARCH'] = 'CERCA UN GIOCATORE';
$i18n['HELP'] = 'AIUTO';
$i18n['IRC CHAT'] = 'CHAT IRC';
$i18n['BZFlag Links'] = 'BZFlag Link';

$i18n['Information sampled every 2 minutes'] = 'Informazioni aggiornate ogni 2 minuti';
$i18n['Callsign'] = 'Callsign';
$i18n['Server'] = 'Server';
$i18n['Score'] = 'Punteggi';
$i18n['Strength Index'] = 'Indice di Forza';
$i18n['Team'] = 'Team';
$i18n['Last Server'] = 'Ultimo Server';

$i18n['List Server Error'] = 'O non c\'&egrave; nessuno a giocare, o Noodleman ha di nuovo incasinato tutto, oppure il list-server &egrave; stato ingoiato dalle scimmie vampiro del Bronx!';

$i18n['Red'] = 'Rosso';
$i18n['Green'] = 'Verde';
$i18n['Blue'] = 'Blue';
$i18n['Purple'] = 'Porpora';
$i18n['Rogue'] = 'Ribelle';
$i18n['Observer'] = 'Osservatore';
$i18n['Rabbit'] = 'Coniglio';

$i18n['Observers'] = 'Osservatori';

function i18n_statsforplayer ($player) {
        return "Statistiche per $player";
}
function i18n_noinfoforplayer ($player) {
        return "Nessuna informazione disponibile su $player.";
}
$i18n['Email String'] = 'e-mail';
$i18n['High Score'] = 'High Score';
$i18n['Low Score'] = 'Low Score';
$i18n['High Strength Index'] = 'Indice di Forza pi&ugrave; alto';
$i18n['Low Strength Index'] = 'Indice di Forza pi&ugrave; basso';
$i18n['High Kill Ratio'] = 'Kill-Ratio pi&ugrave; alto';
$i18n['Low Kill Ratio'] = 'Kill-Ratio pi&ugrave; basso';
$i18n['Most Wins'] = 'Maggior Vittorie';
$i18n['Most Losses'] = 'Maggiori Sconfitte';
$i18n['Last Seen'] = 'Ultimo Visto';
$i18n['Last Server'] = 'Ultimo Server';

function i18n_currentinfoforserver ($server) {
        return "Info attuali per $server";
}
function i18n_noinfoforserver ($server) {
        return "Nessuna informazione disponibile su $server.";
}

$i18n['Size'] = 'Punteggio';
$i18n['Wins'] = 'Vittorie';
$i18n['Losses'] = 'Sconfitte';

$i18n['Hall of Fame'] = 'Hall of Fame';
$i18n['High Scores'] = 'High Scores';
$i18n['High Strength Indexes'] = 'Giocatori pi&ugrave; forti';

$i18n['Hall of Shame'] = 'Hall of Shame';
$i18n['Low Scores'] = 'Low Scores';
$i18n['Low Strength Indexes'] = 'Giocatori pi&ugrave; deboli';


$i18n['Go to:'] = 'Vai a:';
$i18n['Previous'] = 'Precedente';
$i18n['Next'] = 'Successivo';
$i18n['Top'] = 'Sopra';

?>








