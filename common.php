<?php

  // Load configuration file
  include('/etc/bzflag/serversettings.php');
  
  // Connect to database
  require('includes/EasyDB.php');
  $db = new EasyDB($dbhost, $dbuname, $dbpass, $dbname);
  
  $misc = $db->FetchAll("SELECT * FROM `miscinfo`");
  
  // Set up some variables
  $page = Array();
  $data = Array();
  $input = Array();
  
  // Temporarily put team names here until a better place/way is found
  $data['teamnames'] = Array(
		   'R' => 'Red',
		   'G' => 'Green',
		   'B' => 'Blue',
		   'P' => 'Purple',
		   'O' => 'Observer',
		   'H' => 'Rabbit',
		   'X' => 'Rogue',
		   );
  
  $page['theme'] = 'industrial';
  $page['servername'] = $_SERVER['SERVER_NAME'];
  $page['baseURL'] = 'http://'.$_SERVER['HTTP_HOST'].'/';

	$page['refreshed_date'] = date('D, M d H:i:s T Y', $misc[0]['lastrefreshed']);
	$page['refreshed_epoch'] = $misc[0]['lastrefreshed'];
	$page['refreshed_rfc2822'] = date('r', $misc[0]['lastrefreshed']);
  $page[''] = '';
  $page[''] = '';
  $page['separator'] = '<div class="center">&middot; &middot; &middot; &middot; &middot; &middot; &middot;</div>';
  
  // Set up Smarty Template Engine
  include('includes/smarty/Smarty.class.php');
  $tpl = new Smarty;
  
  $tpl->assign_by_ref('page', $page);
  $tpl->assign_by_ref('data', $data);
  $tpl->assign_by_ref('input', $input);

?>
