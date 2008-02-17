<?php

  include('common.php');
  
  // This area is only for moderators. Everyone else, bye bye!
  if (!isset($user) || !$user || !$user['moderator'])
  {
    echo "You are not logged in as a moderator. This page is restricted.";
    exit;
  }

  // This file makes thumbnails of images to see.

  // Provide some security from people poking around the server
  $input['filename'] = basename($_GET['filename']);
  
  if (file_exists($config['paths']['tmp'].$input['filename']))
  {
  	// Send off the proper Content-Type HTTP header
  	header("Content-Type: image/png");
  
    // We don't want any warnings, notices, etc to show up during image generation
    error_reporting(0);
    
    readfile($config['paths']['tmp'].$input['filename']);
  }
  else
  {
    echo "The following file was not found, and may have already been approved or rejected: ".$input['filename'];
  }

?>
