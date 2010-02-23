<?php

  require('common.php');
  $page['title'] = "RSS Feed";
  
  // Handle input
  
  $input['order'] = $_GET['order'];
  
  if (isset($_GET['server']))
    $input['server'] = $_GET['server'];
  
  
  // Fetch data

    
  switch($input['order'])
  {
    case 'callsign': $orderby = '`callsign`'; break;
    case 'callsigndesc': $orderby = '`callsign` DESC'; break;
    case 'server': $orderby = '`server`'; break;
    case 'serverdesc': $orderby = '`server` DESC'; break;
    case 'score': $orderby = '`score` DESC'; break;
    case 'scoreasc': $orderby = '`score`'; break;
    case 'strengthindex': $orderby = '`strengthindex` DESC'; break;
    case 'strengthindexasc': $orderby = '`strengthindex`'; break;
    default: $orderby = '`callsign`';
  }
  
  // Try to fetch the player data
  if (!empty($input['server']))
  {
    $page['title'] = 'Players on '.$input['server'];
    $data['players'] = $db->FetchAll("SELECT * FROM `currentplayers` WHERE `server` = '".$db->Escape($input['server'])."' ORDER BY $orderby");
  }
  else
  {
    $page['title'] = 'Current Players';
    $data['players'] = $db->FetchAll("SELECT * FROM `currentplayers` ORDER BY $orderby");
  }

  // Strip off any cruft from the end of the email string to due to a bug
  // in PlayerInfo::cleanEMail() in 2.0.x
  foreach($data['players'] as $key => $player) {
    if ( ($pos = strpos($player['email'], "\0")) !== FALSE) {
      $data['players'][$key]['email'] = substr($player['email'], 0, $pos);
    }
  }
  
  // Display output
  
  // Include the right header
  header('Content-Type: text/xml');
  //header('Content-Type: application/rss+xml');
  $tpl->display('rss.tpl');

?>
