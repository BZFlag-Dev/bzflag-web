<?php

  define('USING_SMARTY', true);
  define('USING_DATABASE', true);

  include('common.php');
  
  // Process user input
  if (sizeof($_POST) > 0)
  {
  
    // Temporary sleep to simulate a larger upload
    sleep(2);
  
    $uploadErrors = Array();
  
    // Read any input from our form
    for ($i = 0; $i < $config['upload']['maxFiles']; $i++)
    {
      // If they specified something in the upload box, continue checking this
      // file.
      if(empty($_FILES['file']['name'][$i])) { echo "No file found for $i<br>"; continue; }
      
      $input['files'][$i]['file'] = Array();
      $input['files'][$i]['file']['filename'] = $_FILES['file']['name'][$i];
      $input['files'][$i]['file']['type'] = $_FILES['file']['type'][$i];
      $input['files'][$i]['file']['tmpfilename'] = $_FILES['file']['tmp_name'][$i];
      $input['files'][$i]['file']['error'] = $_FILES['file']['error'][$i];
      $input['files'][$i]['file']['filesize'] = $_FILES['file']['size'][$i];

      if (isset($_POST['authorname'][$i]))
        $input['files'][$i]['authorname'] = $_POST['authorname'][$i];
      
      if (isset($_POST['licenseselector'][$i]))
        $input['files'][$i]['licenseselector'] = $_POST['licenseselector'][$i];
      
      if (isset($_POST['otherlicensename'][$i]))
        $input['files'][$i]['otherlicensename'] = $_POST['otherlicensename'][$i];
        
      if (isset($_POST['otherlicenseurl'][$i]))
        $input['files'][$i]['otherlicenseurl'] = $_POST['otherlicenseurl'][$i];
      
      if (isset($_POST['otherlicensetext'][$i]))
        $input['files'][$i]['otherlicensetext'] = $_POST['otherlicensetext'][$i];
      
      if (isset($_POST['confirm'][$i]))
        $input['files'][$i]['confirm'] = $_POST['confirm'][$i];
    }
    
    if (isset($_POST['uploaderfirstname']))
      $input['uploaderfirstname'] = $_POST['uploaderfirstname'];
    
    if (isset($_POST['uploaderlastname']))
      $input['uploaderlastname'] = $_POST['uploaderlastname'];
    
    if (isset($_POST['confirmtos']))
      $input['confirmtos'] = $_POST['confirmtos'];
    
    if (isset($_POST['confirmaccurate']))
      $input['confirmaccurate'] = $_POST['confirmaccurate'];
  }
  
  // Process input and generate any errors
  
  if (sizeof($_POST) > 0)
  {
    // Start validating input
    
    // Temporary debug output
    if (true) {
      echo "<pre>";
      echo "\n\nInput:\n\n";
      print_r($input);
      echo "\n\nuploadErrors:\n\n";
      print_r($uploadErrors);
      echo "\n\nFiles:\n\n";
      print_r($_FILES);
      echo "</pre>";
      exit;
    }
  }

	$page['title'] = 'Upload Images';
	$page['javascripts'] = Array('upload.js');
	
	// Render the page
	$tpl->display('header.tpl');
	$tpl->display('upload.tpl');
	$tpl->display('footer.tpl');

  
  
/*
	//Pick how many images to upload.
	if(!isset($input['quantity']) and !isset($_POST['num_of_images'])){
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
/*		
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
		
/*		
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
		mail($config['mail']['notifyaddress'], 'An image awaits approval', $email_body, 'From: '.$config['mail']['fromaddress']);
	}
*/
?>
