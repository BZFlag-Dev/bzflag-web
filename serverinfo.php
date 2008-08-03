<?php

  require('common.php');
  require('includes/functions.php');
  $page['title'] = "Server Info";
  $page['area'] = "servers";
  
  // Handle input
  $input['server'] = $_GET['server'];
  
  
  // Fetch data
  
  // Server information
  $data['serverinfo'] = $db->FetchRow("SELECT * FROM `serverinfo` WHERE `server` = '".$db->Escape($input['server'])."' LIMIT 1");
  
  if ($data['serverinfo'])
  {
    // Format the time limit nicely
    $data['serverinfo']['nicemaxtime'] = implode(':', secondsToUnits($data['serverinfo']['maxtime']));
  
    // Team information
    $data['teaminfo'] = $db->FetchAll("SELECT * FROM `teaminfo` WHERE `server` = '".$db->Escape($input['server'])."' AND `team` != 'X' ORDER BY `score` DESC");
    
    // Current players
    $data['currentplayers'] = $db->FetchAll("SELECT * FROM `currentplayers` WHERE `server` = '".$db->Escape($input['server'])."' ORDER BY `score` DESC");
  }
  
  
  // Display output
  
  $tpl->display('header.tpl');
  $tpl->display('serverinfo.tpl');
  $tpl->display('footer.tpl');

?>
