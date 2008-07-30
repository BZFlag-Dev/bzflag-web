<?php

  require('common.php');
  $page['title'] = "Home";
  $page['area'] = "home";
  
  // Handle input
  $input['callsign'] = $_GET['callsign'];
  
  
  // Fetch data
  
  // Try to fetch the player data
  $data['player'] = $db->FetchRow("SELECT * FROM `playerinfo` where `callsign` = '".$db->Escape($input['callsign'])."'");
  
  // Display output
  
  $tpl->display('header.tpl');
  $tpl->display('playerinfo.tpl');
  $tpl->display('footer.tpl');

?>
