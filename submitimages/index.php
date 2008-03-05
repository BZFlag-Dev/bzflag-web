<?php
  
  // Include common.php w/ Smarty
  define('USING_SMARTY', true);
  define('USING_QUEUEDB', true);
  
  include('common.php');
  
  // Set the page title
	$page['title'] = false;
	
	// If the user is logged in, check if the have any pending images. If they do.
	// then allow them to view them.
	if ($user)
	{
	  $data['pending'] = $qdb->Queue_Fetch_ByBZID($user['bzid']);
	}
	
	// Render the page
	$tpl->display('header.tpl');
	$tpl->display('home.tpl');
	$tpl->display('footer.tpl');

?>
