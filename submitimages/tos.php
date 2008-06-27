<?php

  // Include common.php w/ Smarty
  define('USING_SMARTY', true);  
  require('common.php');
  
  require($config['paths']['includes'].'recaptcha/recaptchalib.php');
  
  // Set the page title
  $page['title'] = 'Terms Of Service';
  
  if (strlen($config['recaptchamailhide']['publickey']) > 0 && strlen($config['recaptchamailhide']['privatekey']) > 0)
    $data['mailhide'] = recaptcha_mailhide_html($config['recaptchamailhide']['publickey'], $config['recaptchamailhide']['privatekey'], $config['mail']['takedown']); 
  
  // Render the page
  $tpl->display('header.tpl');
  $tpl->display('tos.tpl');
  $tpl->display('footer.tpl');
  
?>
