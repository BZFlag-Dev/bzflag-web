<?php

  require('common.php');
  $page['title'] = "Server List";
  $page['area'] = "servers";
  
  // Handle input
  
  
  // Fetch data
  
  // List of servers
  $data['servers'] = $db->FetchAll("SELECT `server`, `players`, `ctf`, `rabbitchase`, `description` FROM `serverinfo` WHERE `version` = 'BZFS0026' ORDER BY `players` DESC");
  
  
  // Display output
  
  $tpl->display('header.tpl');
  $tpl->display('serverlist.tpl');
  $tpl->display('footer.tpl');

?>
