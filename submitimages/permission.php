<?php

  define('USING_SMARTY', true);
  
  require('common.php');
  
  $page['title'] = 'Permission to Use Content';
  
  $tpl->display('header.tpl');
  $tpl->display('permission.tpl');
  $tpl->display('footer.tpl');
  
?>
