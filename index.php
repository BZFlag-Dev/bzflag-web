<?php

  require('common.php');
  $page['title'] = "Home";
  $page['area'] = "home";
  
  // Handle input
  
  
  // Fetch data
  
  // Most popular server
  $data['commonservers'] = Array(  
    'bzflag.hashmark.net:5154',
    'bzflag.secretplace.us:5255',
    'dragon.org:5255',
    'ducati.bzflag.org:5155',
    'ducati.bzflag.org:5156'
  );
  
  $data['mostpopularserver'] = $db->FetchRow("SELECT server,players FROM `serverinfo` ORDER BY `players` DESC LIMIT 1");
  
  // Player with highest score
  $data['playerhighestscore'] = $db->FetchRow("SELECT callsign,score,server FROM `currentplayers` ORDER BY `score` DESC LIMIT 1");
  
  // Player with highest strength index
  $data['playerhigheststrength'] = $db->FetchRow("SELECT callsign,strengthindex FROM `currentplayers` ORDER BY `strengthindex` DESC LIMIT 1");
  
  // Player with lowest score
  $data['playerlowestscore'] = $db->FetchRow("SELECT callsign,score,server FROM `currentplayers` ORDER BY `score` LIMIT 1");
  
  // Player with lowest strength index
  $data['playerloweststrength'] = $db->FetchRow("SELECT callsign,strengthindex FROM `currentplayers` ORDER BY `strengthindex` LIMIT 1");
  
  
  // Display output
  
  $tpl->display('header.tpl');
  $tpl->display('index.tpl');
  $tpl->display('footer.tpl');

?>
