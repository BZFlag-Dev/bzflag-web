<?php
// This file makes thumbnails of images to see.

//Provide some security from people poking around the server
$_GET['image'] = stripslashes($_GET['image']);
$_GET['image'] = str_replace('../', '', $_GET['image']);
$_GET['image'] = addslashes($_GET['image']);

if(file_exists('./image_holding/'.$_GET['image'])){
	// Header
	header("Content-type: image/png");

	// Image
	readfile('./image_holding/'.$_GET['image']);
}else{
	echo 'No image with specified filename was found. It may have already been approved / rejected.';
}
?>