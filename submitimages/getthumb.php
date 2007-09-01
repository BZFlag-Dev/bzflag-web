<?php
// This file makes thumbnails of images to see.

//Provide some security from people poking around the server
$_GET['image'] = stripslashes($_GET['image']);
$_GET['image'] = str_replace('../', '', $_GET['image']);
$_GET['image'] = addslashes($_GET['image']);

if(file_exists('./image_holding/'.$_GET['image'])){

	// Header
	header("Content-type: image/png");

	// Get new dimensions
	list($width, $height) = getimagesize('./image_holding/'.$_GET['image']);

	if($width > 200 || $height > 200){
		if($width == $height){
			$new_width = 200;
			$new_height = 200;
		}else if($width > $height){
			$new_width = 200;
			$ratio = ($width / 200);
			$new_height = ($height / $ratio); 
		}else if($width < $height){
			$new_height = 200;
			$ratio = ($height / 200);
			$new_width = ($width / $ratio);
		}
	}else{
		$new_width = $width;
		$new_height = $height;
	}

	// Resample
	$image_p = imagecreatetruecolor(round($new_width), round($new_height));
	$image = imagecreatefrompng('./image_holding/'.$_GET['image']);
	imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	
	if(!imagepng($image_p)){
		//If the image doesn't display, then GD can't make a thumb of it, and we should notify the user.
		
	}
}else{
	// Header
	header("Content-type: image/png");

	// Resample
	$image_p = imagecreatetruecolor(200, 200);
	
	$black = imagecolorallocate($image_p, 0, 0, 0);
	$red = imagecolorallocate($image_p, 255, 0, 0);
	
	imagefill($image_p, 0, 0, $red);
	
	imagerectangle($image_p, 0, 0, 199, 199, $black);
	
	imagestring($image_p, 5, 9, 2, "Image File Not Found", $black);
	imagestring($image_p, 3, 9, 20, "Filename:", $black);
	imagestring($image_p, 3, 9, 30, $_GET['image'], $black);
	
	imagestring($image_p, 2, 1, 170, "This is a BZFlag Image Submission", $black);
	imagestring($image_p, 2, 1, 180, "System Error", $black);
		
	imagepng($image_p);
}
?>