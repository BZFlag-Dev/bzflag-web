<?php

// Start a PHP session
session_start();

// Load the configuration file if it is readable
if (is_readable('config.php'))
  require('config.php');
// If not, kill the script now
else
  die("Unable to load configuration file.");

// Show PHP errors if configured
if ($config['enablePHPErrors'])
  error_reporting(E_ALL);
else
  error_reporting(0);

// Load Smarty
require('./smarty/Smarty.class.php');

//Set basic Smarty options
$smarty = new Smarty();
$smarty->template_dir = 'templates';
$smarty->compile_dir = 'templates_c';

//Start assigning Smarty variables
$smarty->assign('title', 'BZFlag Image Submission System');


//Let's validate a login request.
if($_GET['page'] == 'login' and !isset($_SESSION['image_user'])){
	include('checktoken.php');
	$level = validate_token($_GET['token'], urldecode($_GET['callsign']), array('site.imageauth'));
	if($level == 0) $text = '<p>You didn\'t get logged in. You should not see this error. If you see this, 1) the list server is down, 2) you are trying unsucessfully to hack into a user\'s account, or 3) monkeys ate the program. If the answer is 1 or 2, then you just have to be patient and log in again normally at a later time. If it is number 3, then tell somebody!</p>';
	else if($level == 1){
		$_SESSION['image_user'] = $_GET['callsign'];
		$_SESSION['admin'] = false;
	}
	else if($level == 2){
		$text = '<p>Welcome back, '.$_GET['callsign'].'. We recognize you as a BZFlag Image Moderator.</p>';
		$_SESSION['image_user'] = $_GET['callsign'];
		$_SESSION['admin'] = true;
	} else {
		$text = '<p>There has been an unknown login error.</p>';
	}
}

//For logging out
if($_GET['page'] == 'logout' and isset($_SESSION['image_user'])){
	unset($_SESSION['image_user']);
	unset($_SESSION['admin']);
	$text = '<p>You have been logged out.</p>';
}

//For uploading an image
if(isset($_SESSION['image_user'])){

if($_GET['page'] == 'upload'){

	//Pick how many images to upload.
	if(!isset($_GET['num_of_images']) and !isset($_POST['num_of_images'])){
		$text = '<form method="get">
		<p>How many images will you be uploading today, '.$_SESSION['image_user'].'? (max 9 per time)</p>
		<input class="formelement" type="text" name="num_of_images" maxlength="1" size="2">
		<input type="hidden" name="page" value="upload">
		<input class="formelement" type="submit" name="Submit" value="Let\'s upload!">
		</form>';
		
	//Present the upload form.
	}else if(isset($_GET['num_of_images']) and !isset($_POST['num_of_images'])){
		if($_GET['num_of_images'] > 9) $_GET['num_of_images'] = 9;
		if($_GET['num_of_images'] < 1) $_GET['num_of_images'] = 1;
		$text = '<p>Great! Let\'s upload those images!</p><form name="imagesubmit" method="post" enctype="multipart/form-data">
		<table border="0" cellspacing="0">
		<input type="hidden" name="num_of_images" value="'.$_GET['num_of_images'].'">';
		for($i = 1; $i <= $_GET['num_of_images']; $i++){
			$text .= '<tr><td colspan="2" align="center"><hr><h5>Image '.$i.'</h5></td></tr>
			<tr><td align="right">File</td><td><input type="file" class="formelement" name="image_'.$i.'" size="30"><br>(you may only upload PNG files)</td></tr>
			<tr><td>I have the rights<br>to post this image</td><td><input type="checkbox" class="formelement" name="haverights_'.$i.'" value="checkbox"></td></tr>';
		}
		
		$text .= '<tr><td colspan="2" align="center"><hr><h5>License</h5></td></tr>
		<tr><td align="right">What license would<br>you like to license<br>these images under?</td>';
		
		$licenses = file('licenses.txt');
		$all_licenses = '<td><select name="license" id="license" onChange="javascript:ShowHide()" class="formelement">';
		foreach($licenses as $license){
			$license = rtrim($license);
			
			$all_licenses .= '<option>'.$license.'</option>';
		}
		$all_licenses .= '<option value="Other...">Other...</option>
		</select>';
		
		$text .= $all_licenses;
		
		$text .= '<div style="display:none;" id="other_license"><textarea id="other_license" rows="10" cols="33" name="other_license" class="formelement"></textarea></div></td></tr>';
		
		$javascript = '<script language="JavaScript" src="gen_validatorv2.js" type="text/javascript"></script>
		<script language="Javascript" type="text/javascript">
		function ShowHide(){
			var divlayer = document.getElementById("other_license");
			if(document.getElementById("license").options[document.getElementById("license").selectedIndex].value == "Other..."){
				divlayer.style.display = "block";
			}else{
				divlayer.style.display = "none";
			}
		}
		
		function updateDisplayName(){
			var finitial = document.imagesubmit.fname.value.toLowerCase();
			var lname = document.imagesubmit.lname.value.toLowerCase();
			
			document.imagesubmit.dname.value = finitial.substring(0, 1) + lname;
		}
		</script>';
		
		//If this user can be recognized, be nice and fill some form values in for him.
		$fname = '';
		$lname = '';
		$dname = '';
		$email = '';
		
		/*
		$file = file('callsign_name_relationship.txt');
		foreach($file as $line){
			$user_data = explode('|', rtrim($line));
			
			if($user_data[0] == $_SESSION['image_user']){
				$fname = $user_data[1];
				$lname = $user_data[2];
				$email = $user_data[3];
				$dname = strtolower(substr($fname, 0, 1)).strtolower($lname);
				break;
			}
		}
		*/
		
		$text .= '<tr><td colspan="2" align="center"><hr><h5>Author Information</h5></td></tr>
		
		<tr><td align="right">First name</td><td><input type="text" name="fname" class="formelement" onChange="javascript:updateDisplayName()" value="'.$fname.'"></td></tr>
		
		<tr><td align="right">Last name</td><td><input type="text" name="lname" class="formelement" onChange="javascript:updateDisplayName()" value="'.$lname.'"></td></tr>
		
		<tr><td align="right">Display name</td><td><input type="text" name="dname" class="disabled" value="'.$dname.'" READONLY></td></tr>
		
		<tr><td align="right">E-mail address</td><td><input type="text" name="email" class="formelement" value="'.$email.'"></td></tr>
		
		<tr><td colspan="2" align="center"><hr><h5>Upload!</h5></td></tr>
		<tr><td><input type="reset" name="reset" value="Reset Form" class="formcancel"></td><td align="right"><input type="submit" name="Submit" value="Upload those images!" class="formelement"></td></tr>
		</table>
		<p>NOTE: Uploading may take some time depending on both the client and the server\'s internet speed.</p></form>
		
		<script language="Javascript" type="text/javascript">
		var frmvalidator = new Validator("imagesubmit");
		frmvalidator.addValidation("email", "email");
		</script>';
	}
	
	//Handle the actual image upload
	else if(isset($_POST['num_of_images'])){
		
		$email_body = "Some images await approval at the BZFlag Image Uploader.\nImages uploaded by {$_SESSION['image_user']} (Author: {$_POST['fname']} {$_POST['lname']} < {$_POST['email']} >)\n\n";
		$text = '';
		
		if($_POST['other_license'] != '') $_POST['license'] = str_replace("\n", '', str_replace("\r", '', $_POST['other_license']));
		
		
		if($_POST['email'] == '' or $_POST['fname'] == '' or $_POST['lname'] == ''){
			$smarty->assign('title', 'User Made Error');
			$smarty->assign('text', 'You have to input all the fields. They are all required. Go back and fix the form.');
			
			$smarty->display('white.tpl');
			exit();
		}
		
		//Weed out those trying to undermine security.
		$_POST['lname'] = str_replace('.', '', $_POST['lname']);
		$_POST['lname'] = str_replace('/', '', $_POST['lname']);
		$_POST['lname'] = str_replace('\\', '', $_POST['lname']);
		
		if(substr(strtolower($_POST['fname']), 0, 1).$_POST['lname'] == 'submitimages'){
			$smarty->assign('title', 'User Made Error');
			$smarty->assign('text', 'You are trying to break an un-breakable program. And it\'s not breaking.');
			
			$smarty->display('white.tpl');
			exit();
		}
		//Ok we're done owning the hackers.
		
		
		/*
		//Let's assign this callsign to this name, and e-mail address
		$namefound = false;
		$callsign_names = file('callsign_name_relationship.txt');
		foreach($callsign_names as $line){
			$linedetail = explode('|', rtrim($line));
			
			if($linedetail[0] == $_SESSION['image_user']) $namefound = true;
		}
		
		if(!$namefound){
			$fp = fopen('callsign_name_relationship.txt', 'a');
			fwrite($fp, "".str_replace('|', '', $_SESSION['image_user'])."|".str_replace('|', '', $_POST['fname'])."|".str_replace('|', '', $_POST['lname'])."|".str_replace('|', '', $_POST['email'])."|");
			fclose($fp);
		}
		*/
		
		
		for($i = 1; $i <= $_POST['num_of_images']; $i++){
			if($_POST['haverights_'.$i] != 'checkbox'){
				$text .= '<p><font color="red">You cannot upload image '.$i.', because you do not have the rights to upload it!</font></p>';
				continue;
			}
			
			//Determine what the file extention is.
			$file_parts = explode('.', $_FILES['image_'.$i]['name']);
			$extention = $file_parts[(count($file_parts) - 1)];
			
			if(($_FILES['image_'.$i]['type'] == 'image/png' or $_FILES['image_'.$i]['type'] == 'image/x-png') and strtolower($extention) == 'png'){
				//TASK: Go ahead. Upload the sucker.
				if(is_uploaded_file($_FILES['image_'.$i]['tmp_name'])){
					//Upload image, now that it's safe.
					
					$filepath = explode('/', str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']));
					//$filepath = explode('/', str_replace('\\', '/', $_SERVER['PATH_TRANSLATED']));
					array_pop($filepath);
					$filepath = implode('/', $filepath);
					
					//bool move_uploaded_file ( string filename, string destination )
					if(file_exists($filepath.'/image_holding/'.$_SESSION['image_user'].'_'.$_FILES['image_'.$i]['name'])){
						for($q = 1; true; $q++){
							//If the file we're uploading has teh same name as a preexisting file, then change the uploading file's filename!
							if(!file_exists($filepath.'/image_holding/'.$_SESSION['image_user'].'_'.$_FILES['image_'.$i]['name'].$q)){
								$_FILES['image_'.$i]['name'] = substr($_FILES['image_'.$i]['name'], 0, (strlen($_FILES['image_'.$i]['name']) - 4));
								$_FILES['image_'.$i]['name'] = $_FILES['image_'.$i]['name'].$i.'.png';
								break;
							}
						}
					}
					if(move_uploaded_file($_FILES['image_'.$i]['tmp_name'], $filepath.'/image_holding/'.$_SESSION['image_user'].'_'.$_FILES['image_'.$i]['name']))
						$text .= '<p>File moved from PHP temp folder to image temp folder</p>';
					else{
						$text .= '<p><font color="red">File could not be moved from PHP temp folder to image temp folder! Tell an admin!</font></p>';
						continue;
					}
					
					//Prepare an e-mail to send to the admin(s)
					//What's our current directory ?
					$directories = explode('/', $_SERVER['REQUEST_URI']);
					array_pop($directories);
					$path = implode('%2F', $directories);
	
					$email_body .= "Image Filename: ".$_SESSION['image_user'].$_FILES['image_'.$i]['name']."\nView: http://".urldecode($_SERVER['HTTP_HOST']).urldecode($path)."/getimage.php?image=".$_SESSION['image_user']."_".$_FILES['image_'.$i]['name']."\nLink: http://".urldecode($_SERVER['HTTP_HOST']).urldecode($path)."/index.php?page=approve\nLicense: ".$_POST['license']."\n\n";
					
					//Add the file to the list of files waiting to be approved
					$fp = fopen('imageswaiting.txt', 'a');
					fwrite($fp, "".str_replace('|', '', $_SESSION['image_user'])."|".str_replace('|', '', $_SESSION['image_user'])."_".str_replace('|', '', $_FILES['image_'.$i]['name'])."|".str_replace('|', '', stripslashes($_POST['license']))."|{$_POST['email']}|{$_POST['fname']}|{$_POST['lname']}|\n");
					
					$text .= '<p>Image '.$i.' (<b>'.str_replace('|', '', $_FILES['image_'.$i]['name']).'</b>) was uploaded without a hitch!</p>';
				}else{
					$text .= '<p><font color="red">Image '.$i.' could not be uploaded because the uploader is a hacker.</font></p>';
				}
			}else{
				$text .= '<p><font color="red">Image '.$i.' could not be uploaded, because it\'s not a PNG image.</font></p>';
			}
			
		}
		$text .= '<p>Now, if your images passed initial inspection, you can sit back and wait while your images are approved by an Administrator. You will recieve an e-mail notice when they have been approved / rejected.</p>';
		mail($mailtoaddress, 'An image awaits approval', $email_body, 'From: '.$mailfromaddress);
	}
}

}


//The approve image function
if($_GET['page'] == 'approve' and $_SESSION['admin'] == true){
	
	$javascript = '<script language="Javascript" type="text/javascript">
		function ShowDenyForm(image_number){
			document.getElementById(image_number).style.display = "block";
		}
	</script>';

	$text = '<p>Here are all the images that the minions want hosted, '.$_SESSION['image_user'].'. They are at your discretion. Click on the thumbnails for the life sized image.</p>
	<table border="0"><tr>';
	$file = file('imageswaiting.txt');
	$i = 0;
	foreach($file as $image){
		$image_info = explode('|', rtrim($image));
		
		$callsign = $image_info[0];
		$filename = $image_info[1];
		$license = $image_info[2];
		$email = $image_info[3];
		$fname = $image_info[4];
		$lname = $image_info[5];

		if($i % 3 == 0) $text .= '</tr><tr>';
		
		$text .= "<td valign='center'><a href='getimage.php?image=$filename' target='_blank'><img src='getthumb.php?image=$filename' border=0></a><br><b>$callsign</b> wants this image.<br>
		<b>Folder</b>: ".strtolower(substr($fname, 0, 1)).strtolower($lname)."<br>
		<b>Filename</b>: $filename<br><br>
		<b>Author</b>: $fname $lname<br>
		<b>Email</b>: $email<br>
		<b>License</b>: $license<br>
		<input type='button' name='approve' value='Approve it!' onClick=\"javascript:window.location='index.php?page=approveimage&image=".urlencode($filename)."&callsign=$callsign'\" class='formelement'><br>
		<input type='button' name='deny' value='denied.' onClick='javascript:ShowDenyForm($i)' class='formcancel'>
		<div id='$i' style='display:none;'>
		<form method='post' action='index.php?page=denyimage'>
			<input type='hidden' name='image' value='$filename'>
			<input type='hidden' name='email' value='$email'>
			<input type='hidden' name='author' value='$callsign'>
			Reason for denial?<br>
			<textarea name='reason' cols='22' rows='4' class='formelementred'></textarea><br>
			<input type='submit' name='submit' value='Delete Image. Inform User.' class='formcancel'>
		</form>
		</div></td>";
		
		$i++;
	}
	$text .= '</tr></table>';
}


//For denying the image and crushing the user's soul.
if($_GET['page'] == 'denyimage' and $_SESSION['admin'] == true){
	if(unlink('./image_holding/'.$_POST['image']))
		$text = '<p>Congrats! You crushed the soul of somebody by deleting their image from the server.</p>';
	else
		$text = '<b><font color="red">Something went wrong! The file could not be deleted from ./image_holding/'.$_GET['image'].' . You will have to delete it yourself.</font></b><br>';
	
	$images = file('imageswaiting.txt');
	$finalfile = '';
	foreach($images as $line){
		$image_info = explode('|', rtrim($line));
		
		//If this is the image that's being denied, let's take it out of the waiting file
		if($image_info[1] == $_POST['image']) continue;
		else $finalfile .= $line;
	}
	$fp = fopen('imageswaiting.txt', 'w');
	fwrite($fp, $finalfile);
	fclose($fp);
	
	$email_body = "{$_SESSION['image_user']} has rejected your image ({$_POST['image']}), {$_POST['author']}.\nReason: ";
	$email_body .= stripslashes($_POST['reason']);
	
	mail($_POST['email'], 'Your image was denied.', $email_body, 'From: '.$mailfromaddress);
}


//For actually approving the images.
if($_GET['page'] == 'approveimage' and $_SESSION['admin'] == true){

	//Get the acutal username of the user
	$file = file('imageswaiting.txt');
	foreach($file as $line){
		$image_detail = explode('|', rtrim($line));
		
		$email = $image_detail[3];
		$fname = $image_detail[4];
		$lname = $image_detail[5];
		$dname = strtolower(substr($fname, 0, 1)).strtolower($lname);
	}
	
	//If the user's directory isn't there, create it, and move the appropriate files into it.
	if(!file_exists("../$dname")){
		if(mkdir("../$dname", 0777)) $text .= '<p>New directory created for user!</p>';
		if(copy('./image_holding/index.php', "../$dname/index.php")) $text .= '<p>New directory set up for user!</p>';
		
		$fp = fopen("../$dname/author.txt", 'w');
		fwrite($fp, "Licenses for $fname $lname's images.\n\n");
		fclose($fp);
	}
	
	if(copy('./image_holding/'.$_GET['image'], "../$dname/{$_GET['image']}")){
		$text .= '<p>Congrats! The image was approved and copied sucessfully.</p>';
		
		$images = file('imageswaiting.txt');
		$finalfile = '';
		foreach($images as $line){
			$image_info = explode('|', rtrim($line));
			
			//If this is the image that's being approved, let's take it out of the waiting file. And snatch the license while we're at it.
			if($image_info[1] == $_GET['image']){
				$image_license = $image_info[2];
				continue;
			}else{
				$finalfile .= $line;
			}
		}
		$fp = fopen('imageswaiting.txt', 'w');
		fwrite($fp, $finalfile);
		fclose($fp);
		
		//Find the author.txt file in their directory and add the license to it.
		$fp = fopen("../$dname/author.txt", 'a');
		fwrite($fp, "License for {$_GET['image']}: $image_license\n\n");
		fclose($fp);
		
		$filepath = explode('/', str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']));
		//$filepath = explode('/', str_replace('\\', '/', $_SERVER['PATH_TRANSLATED']));
		array_pop($filepath);
		@array_pop($filepath);
		$filepath = implode('/', $filepath);

		$email_body = 'Congratulations, '.$fname.' '.$lname.', your image, '.$_GET['image'].' is now hosted at http://'.urldecode($_SERVER['HTTP_HOST']).urldecode($path).'/'.$dname.'/'.$_GET['image'];
		
		mail($email, 'BZFlag Image Submission System', $email_body, 'From: '.$mailfromaddress);
		
		
		if(unlink('./image_holding/'.$_GET['image']))
			$text .= '<p>Congrats! The image was deleted from the temporary holding folder.</p>';
		else
			$text .= '<b><font color="red">Something went wrong! The image could not be deleted from the holding folder. Please delete ./image_holding/'.$_GET['image'].' manually. Everything else worked and the image has been approved.</font></b><br>';
	}else
		$text = '<b><font color="red">Something went wrong! The image could not be copied. Check the filesystem to make sure permissions allow the PHP deamon to copy files to ../'.$_GET['image'].' . Thank you and goodbye.</font></b>';
}


//This is just to determine what links are displayed.
if(!isset($_SESSION['image_user'])){
	//Determine our current directory, to feed to the BZBB login script.
	//What's our current directory to give to the BZBB login script?
	$directories = explode('/', $_SERVER['REQUEST_URI']);
	array_pop($directories);
	$path = implode('%2F', $directories);
	
	$smarty->assign('links', array('Log In' => 'http://my.bzflag.org/weblogin.php?action=weblogin&url=http%3A%2F%2F'.$_SERVER['HTTP_HOST'].$path.'%2Findex.php%3Fpage%3Dlogin%26token%3D%25TOKEN%25%26callsign%3D%25USERNAME%25'));
}else{
	if($_SESSION['admin'] == true)
		$smarty->assign('links', array('Home' => 'index.php', 'Upload Images' => 'index.php?page=upload', 'Approve Images' => 'index.php?page=approve', 'Logout' => 'index.php?page=logout'));
	else
		$smarty->assign('links', array('Home' => 'index.php', 'Upload Images' => 'index.php?page=upload', 'Logout' => 'index.php?page=logout'));
}


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
?>
