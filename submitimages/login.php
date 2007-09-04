<?php

  define('USING_SMARTY', true);

  include('common.php');
  include($config['paths']['includes'].'checktoken.php');
  
  $page['title'] = 'Login';
	
	$results = validate_token($_GET['token'], urldecode($_GET['callsign']), Array($config['imageModeratorGroup']), false);
	
	if (isset($results['bzid']))
  {
  	$_SESSION['bzid'] = $user['bzid'] = $results['bzid'];
		$_SESSION['username'] = $user['username'] = $results['username'];

	  if(!isset($results['groups']) || !in_array($config['imageModeratorGroup'], $results['groups'])){
      $_SESSION['moderator'] = $user['moderator'] = false;
    }
    else {
      $_SESSION['moderator'] = $user['moderator'] = true;
    }
    
    $tpl->assign('redirect', Array('url' => $config['paths']['baseURL'], 'delay' => 5));
  }
  else
  {
    $messages['loginFailed'] = true;
  }
	
	
	// Render the page
	$tpl->display('header.tpl');
	$tpl->display('login.tpl');
	$tpl->display('footer.tpl');
	
?>
