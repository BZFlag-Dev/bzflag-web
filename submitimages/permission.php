<?php

  // Include common.php w/ Smarty
  define('USING_SMARTY', true);
  require('common.php');
  
  // Set the page title
  $page['title'] = 'Permission to Use Content';
  
  // Render the page
  $tpl->display('header.tpl');
  $tpl->display('permission.tpl');
  $tpl->display('footer.tpl');
  
?>
