<?php

include('common.php');




/*
if(!isset($_GET['page']) or $_GET['page'] == 'login'){
	if(isset($_SESSION['image_user'])){
		//The default text
		$text .= '<p>'.$_SESSION['image_user'].', you have the following images pending moderation:<br>';
			
		//Let's get all the entries in the waiting file into an array, delimited by line endings.
		$all_images = file('imageswaiting.txt');
		foreach($all_images as $line){
			$line = rtrim($line);
			$line_exploded = explode('|', $line);
			if($line_exploded[0] == $_SESSION['image_user']){
				$text .= '<b>'.$line_exploded[1].'</b> <br>';
			}
		}
		$text .= '</p>';
	}else{
		$text = '<p>The BZFlag Image Submission system by <a href="http://www.garrettsites.net">RPG</a> allows you to upload images to the BZFlag image server. Having your images on this server has a few benefits.</p>
		<ol>
		<li>You don\'t have to host them on your own server</li>
		<li>By default, your images will be permitted in the BZFlag client\'s downloadaccess.txt file</li>
		</ol>
		<p>To upload your images, you have to do the following.</p>
		<ol>
		<li>Log in</li>
		<li>Upload the images, and provide some information about them</li>
		<li>Wait for your images to be approved by a BZFlag Image moderator!</li>
		</ol>
		<p>Happy uploading :)</p>';
	}
}		

$smarty->assign('text', $text);
$smarty->assign('javascript', $javascript);
//Output the stuff!
$smarty->display('BZtheme.tpl');
*/
?>
