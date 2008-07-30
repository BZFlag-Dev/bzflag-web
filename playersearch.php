<?php

  require('common.php');
  $page['title'] = "Player Search";
  $page['area'] = "players";
  
  // Handle input
  
  $input['callsign'] = str_replace('%', '', $_GET['callsign']);
  if (strlen($input['callsign']) != 0)
    $page['title'] .= " - " . htmlentities($input['callsign']);
  else
    unset($input['callsign']);
    
  if (isset($_GET['page']) && ctype_digit($_GET['page']) && $_GET['page'] > 0)
    $input['page'] = $_GET['page'];
  else
    $input['page'] = 1;
  
  
  // Fetch data
  
  // Try to fetch the player data
  if (strlen($input['callsign']) != 0)
  {
    $data['playersearch'] = Array();
    $data['playersearch']['total'] = $db->NumRows("SELECT callsign, lastserver FROM `playerinfo` where `callsign` LIKE '".$db->Escape($input['callsign'])."%' ORDER BY `callsign`");
    $data['playersearch']['perpage'] = 50; 
    $data['playersearch']['pages'] = ceil($data['playersearch']['total'] / $data['playersearch']['perpage']);
    $data['playersearch']['data'] = $db->FetchAll("SELECT callsign, lastserver FROM `playerinfo` where `callsign` LIKE '".$db->Escape($input['callsign'])."%' ORDER BY `callsign` LIMIT ".(($input['page']-1)*$data['playersearch']['perpage']).",".$data['playersearch']['perpage']);
    echo mysql_error();
  }
  else
    $data['playersearch'] = false;
  
  
  // Display output
  
  $tpl->display('header.tpl');
  $tpl->display('playersearch.tpl');
  $tpl->display('footer.tpl');

?>
