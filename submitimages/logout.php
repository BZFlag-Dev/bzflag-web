<?php

  define('USING_SMARTY', true);

  include('common.php');
  
  // Clear away the session values
  unset($_SESSION);
  
  // Destroy the session
	session_destroy();
	
	// Set $user to false
	$user = false;
	
	
	// Render the page
	$tpl->display('header.tpl');
	$tpl->display('logout.tpl');
	$tpl->display('footer.tpl');
	
?>
