<?php

  // Include common.php w/ Smarty
  define('USING_SMARTY', true);
  include('common.php');
  
  // We also need to the validate_token function, so include checktoken.php
  include($config['paths']['includes'].'checktoken.php');
  
  // Set the page title
  $page['title'] = 'Login';
	
	// Read user input
	if (isset($_GET['token']))
    $input['token'] = $_GET['token'];
  
  if (isset($_GET['username']))
    $input['username'] = $_GET['username'];
  
  
  // Process data
  if (isset($input['token'], $input['username']))
  {
    // Send a request to the list server via the CHECKTOKEN action.  This will
    // figure out of this token we received is valid for this use. The fourth
    // argument (checkIP) is currently set to false since it impairs local
    // testing.
    // TODO: Remove the fourth argument, set it to true, or add a config option
    $results = validate_token($_GET['token'], urldecode($_GET['username']), Array($config['imageModeratorGroup']), false);
  	
  	// The BZID must be returned in our response, and it must be greater than 0
  	if (isset($results['bzid']) && is_numeric($results['bzid']) && $results['bzid'] > 0)
    {
      // Assign the bzid and username to our session
    	$_SESSION['bzid'] = $user['bzid'] = $results['bzid'];
  		$_SESSION['username'] = $user['username'] = $results['username'];
  
      // If the user is in our moderator group, give them moderator rights
  	  if(isset($results['groups']) && in_array($config['imageModeratorGroup'], $results['groups'])){
        $_SESSION['moderator'] = $user['moderator'] = true;
      }
      // If not, they're just a regular user with upload rights
      else {
        $_SESSION['moderator'] = $user['moderator'] = false;
      }
      
      // Since they have logged in successfully, we'll redirect them back to
      // the main page in 5 seconds.
      $tpl->assign('redirect', Array('url' => $config['paths']['baseURL'], 'delay' => 5));
    }
    // We had a token and callsign, but they did not validate. Set an error bit.
    else $messages['loginFailed'] = true;
  }
  // We did not have both a token and callsign. Set an error bit.
  else $messages['loginFailed'] = true;
	
	
	// Render the page
	$tpl->display('header.tpl');
	$tpl->display('login.tpl');
	$tpl->display('footer.tpl');
	
?>
