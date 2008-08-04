<?php

  require('common.php');
  $page['title'] = "Current Players";
  $page['area'] = "players";
  
  // Handle input
  
  $input['order'] = $_GET['order'];
  
  // Fetch data
  
  // Generate the sort order the user wants
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
  
  // List of players
  $data['players'] = $db->FetchAll("SELECT * FROM `currentplayers` ORDER BY $orderby");
  
  
  // Display output
  
  $tpl->display('header.tpl');
  $tpl->display('currentplayers.tpl');
  $tpl->display('footer.tpl');

?>
