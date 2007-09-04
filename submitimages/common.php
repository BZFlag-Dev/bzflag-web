<?php
 
  // Load the configuration file if it is readable
  if (is_readable('config.php'))
    require('config.php');
  // If not, kill the script now
  else
    die("Unable to load configuration file.");
    
  // Start a PHP session
  
  
  // Show PHP errors if configured
  if ($config['enablePHPErrors'])
    error_reporting(E_ALL);
  else
    error_reporting(0);
  
  
    
  // User authentication
  
  // First, start/resume the session
  session_start();
  
  // Check if we have the required session data
  if (isset($_SESSION['bzid'], $_SESSION['username'], $_SESSION['moderator']))
  {
    // We do, so fill the $user variable.
    $user = Array();
    $user['bzid'] = $_SESSION['bzid'];
    $user['username'] = $_SESSION['username'];
    $user['moderator'] = $_SESSION['moderator'];
  }
  // If we don't, just set $user to false.
  else
  {
    $user = false;
  }
  
  // Input
  $input = Array();
  
  // Used to trigger messages
  $messages = Array();
    
  // Per-page information
  $page = Array();
  
  // Smarty Template System
  if (defined('USING_SMARTY') && USING_SMARTY)
  {
    // Load Smarty
    require('./includes/smarty/Smarty.class.php');
    
    //Set basic Smarty options
    $tpl = new Smarty();
    $tpl->template_dir = 'templates';
    $tpl->compile_dir = 'templates_c';
    
    
    $tpl->assign_by_ref('config', $config);
    $tpl->assign_by_ref('user', $user);
    $tpl->assign_by_ref('input', $input);
    $tpl->assign_by_ref('messages', $messages);
    $tpl->assign_by_ref('page', $page);
  }

?>
