<?php
 
  // Load the configuration file if it is readable
  if (is_readable('config.php'))
    require('config.php');
  // If not, kill the script now
  else
    die("Unable to load configuration file.");
  
  // Configure which errors to display and/or log
  error_reporting(E_ALL);
  
  // Show PHP errors if configured
  if ($config['enablePHPErrors'])
    ini_set('display_errors', true);
  else
    ini_set('display_errors', false);
    
  
  if (defined('USING_DATALAYER') && USING_DATALAYER)
  {
    require($config['paths']['includes'].'datalayer.class.php');
    $dl = new DataLayer($config['datalayer']);
    $data = Array();
  }
  
  
    
  // User authentication
  
  // First, start/resume the session
  session_start();
  
  // Check if we have the required session data
  // TODO: Add a timeout
  if (isset($_SESSION['bzid'], $_SESSION['username'], $_SESSION['moderator']) && $_SESSION['bzid'] > 0 && strlen($_SESSION['username']) > 0)
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
  
  // Load language file
  include($config['paths']['language'].'en.php');
  
  // Input
  $input = Array();
  
  // Used to contain messages
  $messages = Array();
    
  // Per-page information
  $page = Array();
  
  // Smarty Template System
  if (defined('USING_SMARTY') && USING_SMARTY)
  {
    // Load Smarty
    require($config['paths']['includes'].'smarty/Smarty.class.php');
    
    // Create a new Smarty object
    $tpl = new Smarty();
    // This is where uncompiled template files are located
    $tpl->template_dir = $config['paths']['templates'];
    // This is where compiled templates are stored
    $tpl->compile_dir = $config['paths']['templates_c'];

    // Assign the configuration to Smarty
    // TODO: Once we have DB support, remove those from the config array
    // before assigning this to Smarty.
    $tpl->assign('config', $config);
        
    // Assign several variables by reference, since they may/will change during
    // script execution
    $tpl->assign_by_ref('user', $user);
    $tpl->assign_by_ref('input', $input);
    $tpl->assign_by_ref('messages', $messages);
    $tpl->assign_by_ref('page', $page);
    
    if (defined('USING_DATALAYER') && USING_DATALAYER)
      $tpl->assign_by_ref('data', $data);
  }

?>
