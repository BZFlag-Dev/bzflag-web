<?php

  require('common.php');
  $page['title'] = "RSS Feed Generator";
  $page['area'] = "";
  
  // Handle input
  $input['callsign'] = $_GET['callsign'];
  
  if (isset($_GET['fullplayerlist']))
  {
    $input['fullplayerlist'] = $_GET['fullplayerlist'];
    
    if ($input['fullplayerlist'] == 'no')
    {
      if (isset($_GET['server']))
        $input['server'] = $_GET['server'];
    }
  }
  
  if (isset($_GET['order']))
    $input['order'] = $_GET['order'];
  
  if (isset($input['fullplayerlist'], $input['order']))
  {
    if ($input['fullplayerlist'] == "no")
    {
      if (empty($input['server']))
      {
        $tpl->assign('rsserror', "You must enter a server to display info from.");
  	   } else {
  	     $rssfeed = "&server=".$input['server'];
  	   }
    }
    if ($input['fullplayerlist'] == "yes" || !empty($input['server']))
    {
      $validSortOrders = Array('callsign', 'callsigndesc', 'server',
      'serverdesc', 'score', 'scoreasc', 'strengthindex', 'strengthindexasc');
      
      if (in_array($input['order'], $validSortOrders))
        $rssfeed .= "&order=".$input['order'];
      else
        $rssfeed .= "&order=callsign";
        
      $tpl->assign('rssfeed', $rssfeed);
    }
  }
  
  
  
  // Fetch data
  
  // Display output
  
  $tpl->display('header.tpl');
  $tpl->display('rssgenerator.tpl');
  $tpl->display('footer.tpl');

?>
