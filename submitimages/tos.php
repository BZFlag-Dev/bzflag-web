<?php

  define('USING_SMARTY', true);
  
  require('common.php');
  
  $page['title'] = 'Terms Of Service';
  
  $tpl->display('header.tpl');
  $tpl->display('tos.tpl');
  $tpl->display('footer.tpl');
  
?>
