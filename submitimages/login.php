<?php

  define('USING_SMARTY', true);

  include('common.php');
  
  include($config['paths']['includes'].'checktoken.php');
	
	$results = validate_token($_GET['token'], urldecode($_GET['callsign']), Array($config['imageAdminGroup']), false);
	
	if (isset($results['bzid']))
  {
  	$_SESSION['bzid'] = $user['bzid'] = $results['bzid'];
		$_SESSION['username'] = $user['username'] = $results['username'];

	  if(!isset($results['groups']) || !in_array($config['imageAdminGroup'], $results['groups'])){
      $_SESSION['admin'] = $user['admin'] = false;
    }
    else {
      $_SESSION['admin'] = $user['admin'] = true;
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
