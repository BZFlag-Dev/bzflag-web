<?php

  define('USING_SMARTY', true);
  
  require('common.php');
  
  $tpl->display('header.tpl');
  $tpl->display('tos.tpl');
  $tpl->display('footer.tpl');
  
?>
