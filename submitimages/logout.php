<?php

  // Include common.php w/ Smarty
  define('USING_SMARTY', true);
  include('common.php');
  
  // Set the page title
  $page['title'] = 'Logout';
  
  // Clear away the session values
  unset($_SESSION);
  
  // Destroy the session
	session_destroy();
	
	// Set $user to false
	$user = false;
	
	// Redirect back to the home page after 5 seconds
	$tpl->assign('redirect', Array('url' => $config['paths']['baseURL'], 'delay' => 5));
	
	// Render the page
	$tpl->display('header.tpl');
	$tpl->display('logout.tpl');
	$tpl->display('footer.tpl');
	
?>
