<?php

  // Include common.php w/ Smarty
  define('USING_SMARTY', true);  
  require('common.php');
  
  // Set the page title
  $page['title'] = 'Terms Of Service';
  
  // Render the page
  $tpl->display('header.tpl');
  $tpl->display('tos.tpl');
  $tpl->display('footer.tpl');
  
?>
