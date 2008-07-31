<?php

  require('common.php');
  $page['title'] = "Player Stats";
  $page['area'] = "players";
  
  // Handle input
  
  
  // Fetch data
  
  // Players with highest score
  $data['playershighscore'] = $db->FetchAll("SELECT callsign,highscore,highstrengthindex FROM `playerinfo` ORDER BY `highscore` DESC LIMIT 20");
  
  // Players with highest strength index
  $data['playershighstrength'] = $db->FetchAll("SELECT callsign,highscore,highstrengthindex FROM `playerinfo` ORDER BY `highstrengthindex` DESC LIMIT 20");
  
  // Players with lowest score
  $data['playerslowscore'] = $db->FetchAll("SELECT callsign,lowscore,lowstrengthindex FROM `playerinfo` ORDER BY `lowscore` LIMIT 20");
  
  // Players with lowest strength index
  $data['playerslowstrength'] = $db->FetchAll("SELECT callsign,lowscore,lowstrengthindex FROM `playerinfo` ORDER BY `lowstrengthindex` LIMIT 20");
  
  
  // Display output
  
  $tpl->display('header.tpl');
  $tpl->display('playerstats.tpl');
  $tpl->display('footer.tpl');

?>
