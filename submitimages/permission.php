<?php

  define('USING_SMARTY', true);
  
  require('common.php');
  
  $tpl->display('header.tpl');
  $tpl->display('permission.tpl');
  $tpl->display('footer.tpl');
  
?>
