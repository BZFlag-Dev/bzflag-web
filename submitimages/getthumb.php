<?php

  include('common.php');
  
  // This area is only for moderators. Everyone else, bye bye!
  if (!isset($user) || !$user || !$user['moderator'])
    exit;

  // This file makes thumbnails of images to see.

  // Provide some security from people poking around the server
  $input['filename'] = basename($_GET['filename']);
  
  // We don't want any warnings, notices, etc to show up during image generation
  error_reporting(0);

  if (file_exists($config['paths']['tmp'].$input['filename']))
  {
  	// Send off the proper Content-Type HTTP header
  	header("Content-Type: image/png");
  
  	// Get new dimensions
  	list($original['width'], $original['height']) = getimagesize($config['paths']['tmp'].$input['filename']);
  	
  	// Determine the ratio
  	$original['ratio'] = ($original['width'] / $original['height']);
  	
  	$constraint = Array();
    $constraint['width'] = $config['thumbnail']['maxWidth'];
    $constraint['height'] = $config['thumbnail']['maxHeight'];
    $constraint['ratio'] = ($constraint['width'] / $constraint['height']);

    $resized = Array();
    
    if ($original['ratio'] == $constraint['ratio'])
    {
      $resized['width'] = $constraint['width'];
      $resized['height'] = $constraint['height'];
    }
    else if ($original['ratio'] > $constraint['ratio'])
    {
      $resized['width'] = $constraint['width'];
      $resized['height'] = $original['height'] / ($original['width'] / $constraint['width']);
    }
    else if ($original['ratio'] < $constraint['ratio'])
    {
      $resized['width'] = $original['width'] / ($original['height'] / $constraint['height']);
      $resized['height'] = $constraint['height'];
    }
  	
  /*
  	if ($width > $config['thumbnail']['maxWidth'] || $height > $config['thumbnail']['maxHeight'])
    {
  		if ($width == $height)
      {
  			$new_width = $config['thumbnail']['maxWidth'];
  			$new_height = $config['thumbnail']['maxHeight'];
  		}
      else if ($width > $height)
      {
  			$new_width = $config['thumbnail']['maxWidth'];
  			$ratio = ($width / $config['thumbnail']['maxWidth']);
  			$new_height = ($height / $ratio); 
  		}
      else if ($width < $height)
      {
  			$new_height = $config['thumbnail']['maxHeight'];
  			$ratio = ($height / $config['thumbnail']['maxWidth']);
  			$new_width = ($width / $ratio);
  		}
  	}
    else
    {
  		$new_width = $width;
  		$new_height = $height;
  	}*/
  
  	// Resample
  	$image_p = imagecreatetruecolor(round($resized['width']), round($resized['height']));
  	$image = imagecreatefrompng($config['paths']['tmp'].$input['filename']);
  	imagecopyresampled($image_p, $image, 0, 0, 0, 0, $resized['width'], $resized['height'], $original['width'], $original['height']);
  	
  	if(!imagepng($image_p)){
  		//If the image doesn't display, then GD can't make a thumb of it, and we should notify the user.
  	}
  }
  else
  {
  	// Header
  	header("Content-type: image/png");
  
  	// Resample
  	$image_p = imagecreatetruecolor($config['thumbnail']['maxWidth'], $config['thumbnail']['maxHeight']);
  	
  	$black = imagecolorallocate($image_p, 0, 0, 0);
  	$red = imagecolorallocate($image_p, 255, 0, 0);
  	
  	imagefill($image_p, 0, 0, $red);
  	
  	imagerectangle($image_p, 0, 0, $config['thumbnail']['maxWidth']-1, $config['thumbnail']['maxHeight']-1, $black);
  	imagerectangle($image_p, 1, 1, $config['thumbnail']['maxWidth']-2, $config['thumbnail']['maxHeight']-2, $black);
  	
  	imagestring($image_p, 5, 9, 2, "Image File Not Found", $black);
  	imagestring($image_p, 3, 9, 20, "Filename:", $black);
  	imagestring($image_p, 3, 9, 30, (isset($input['filename']))?$input['filename']:'Not Specified', $black);
  		
  	imagepng($image_p);
  }
?>
