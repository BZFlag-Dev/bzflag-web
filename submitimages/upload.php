<?php

  define('USING_SMARTY', true);
  define('USING_DATALAYER', true);

  include('common.php');
  // Must be logged in to upload images
  if (!$user)
  {
    header('Location: '.$config['paths']['baseURL']);
    exit;
  }
  
  include($config['paths']['includes'].'validation.php');
  include($config['paths']['includes'].'functions.php');
  
  // If they submit the form, let's process that
  if (sizeof($_POST) > 0)
  {
    //////////////////////////////////////////
    // Read user input
    //////////////////////////////////////////
    
    // Temporary sleep to simulate a larger upload
    // TODO: Remove this before putting the system into production
    sleep(2);
  
    $messages['errors'] = Array();
    $messages['top'] = Array();
  
    // Read any input from our form
    for ($i = 1; $i <= $config['upload']['maxFiles']; $i++)
    {
      // If they specified something in the upload box, continue checking this
      // file.
      if(empty($_FILES['file']['name'][$i])) continue;
      
      // Pull the information from the $_FILES array and place it into our input
      $input['files'][$i]['file'] = Array();
      $input['files'][$i]['file']['filename'] = $_FILES['file']['name'][$i];
      $input['files'][$i]['file']['type'] = $_FILES['file']['type'][$i];
      $input['files'][$i]['file']['tmpfilename'] = $_FILES['file']['tmp_name'][$i];
      $input['files'][$i]['file']['error'] = $_FILES['file']['error'][$i];
      $input['files'][$i]['file']['filesize'] = $_FILES['file']['size'][$i];

      // Author name of this file (REQUIRED)
      if (isset($_POST['authorname'][$i]))
        $input['files'][$i]['authorname'] = $_POST['authorname'][$i];
      
      // Selected license (REQUIRED)
      // 0 == "I stole this"
      // 255 == "Other OSI-Approved"
      if (isset($_POST['licenseselector'][$i]))
        $input['files'][$i]['licenseselector'] = $_POST['licenseselector'][$i];
      
      // If licenseselector is 255, we need this
      if (isset($_POST['otherlicensename'][$i]))
        $input['files'][$i]['otherlicensename'] = $_POST['otherlicensename'][$i];
      
      // If licenseselector is 255, we need this and/or otherlicensetext
      if (isset($_POST['otherlicenseurl'][$i]))
        $input['files'][$i]['otherlicenseurl'] = $_POST['otherlicenseurl'][$i];
      
      // If licenseselector is 255, we need this and/or otherlicenseurl
      if (isset($_POST['otherlicensetext'][$i]))
        $input['files'][$i]['otherlicensetext'] = $_POST['otherlicensetext'][$i];
      
      // This is the confirmation that this image does not violate the TOS (REQUIRED)
      if (isset($_POST['confirm'][$i]))
        $input['files'][$i]['confirm'] = $_POST['confirm'][$i];
    } // for ($i = 1; $i <= $config['upload']['maxFiles']; $i++)
    
    // Uploader first name (REQUIRED)
    if (isset($_POST['uploaderfirstname']))
      $input['uploaderfirstname'] = $_POST['uploaderfirstname'];
    
    // Uploader last name (REQUIRED)
    if (isset($_POST['uploaderlastname']))
      $input['uploaderlastname'] = $_POST['uploaderlastname'];
    
    // Confirmation that none of the images violate the TOS (REQUIRED)
    if (isset($_POST['confirmtos']))
      $input['confirmtos'] = $_POST['confirmtos'];
    
    // Confirmation that all of the form information is accurate
    if (isset($_POST['confirmaccurate']))
      $input['confirmaccurate'] = $_POST['confirmaccurate'];
  
    //////////////////////////////////////////
    // Process input and generate any errors
    //////////////////////////////////////////
    
    // Validate the uploader information
    
    // Assume it is valid until proven otherwise
    $input['invalid'] = false;
    
    // TODO: Count how many errors there were. For now, we just force this to
    // one to make it display the error messages server side
    //$messages['errors']['count'] = 1;
    
    // Uploader first name
    if (strlen($input['uploaderfirstname']) == 0)
    {
      $input['invalid'] = true;
      $messages['errors'][] = $lang['errors']['uploaderfirstnameInvalid'];
      $invalid['uploaderfirstname'] = true;
    }
    
    // Uploader last name
    if (strlen($input['uploaderlastname']) == 0)
    {
      $input['invalid'] = true;
      $messages['errors'][] = $lang['errors']['uploaderlastnameInvalid'];
      $invalid['uploaderlastname'] = true;
    }
    
    if (strtolower(substr($input['uploaderfirstname'], 0, 1)) == "s" && strtolower($input['uploaderlastname']) == "ubmitimages")
    {
      $input['invalid'] = true;
      $messages['errors'][] = $lang['errors']['uploaderfirstnameInvalid'];
      $invalid['uploaderfirstname'] = true;
      $messages['errors'][] = $lang['errors']['uploaderlastnameInvalid'];
      $invalid['uploaderlastname'] = true;
    }
    
    // Confirm the Terms of Service
    if (!isset($input['confirmtos']) || !$input['confirmtos'])
    {
      $input['invalid'] = true;
      $messages['errors'][] = $lang['errors']['confirmtosInvalid'];
      $invalid['confirmtos'] = true;
    }
    
    // Confirm the accuracy of the form data
    if (!isset($input['confirmaccurate']) || !$input['confirmaccurate'])
    {
      $input['invalid'] = true;
      $messages['errors'][] = $lang['errors']['confirmaccurateInvalid'];
      $invalid['confirmaccurate'] = true;
    }

    // Validate each file and it's associated information
    for ($i = 1; $i <= $config['upload']['maxFiles']; $i++)
    {
      // If no file specified, go to the next
      if(!isset($input['files'][$i]['file'])) continue;
      
      // Assume the file is valid until proven otherwise
      $input['files'][$i]['invalid'] = false;
      
      // Various error codes for file uploads
      // UPLOAD_ERR_OK
      //     Value: 0; There is no error, the file uploaded with success.
      // UPLOAD_ERR_INI_SIZE
      //     Value: 1; The uploaded file exceeds the upload_max_filesize directive in php.ini.
      // UPLOAD_ERR_FORM_SIZE
      //     Value: 2; The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.
      // UPLOAD_ERR_PARTIAL
      //     Value: 3; The uploaded file was only partially uploaded.
      // UPLOAD_ERR_NO_FILE
      //     Value: 4; No file was uploaded.
      // UPLOAD_ERR_NO_TMP_DIR
      //     Value: 6; Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.
      // UPLOAD_ERR_CANT_WRITE
      //     Value: 7; Failed to write file to disk. Introduced in PHP 5.1.0.
      // UPLOAD_ERR_EXTENSION
      //     Value: 8; File upload stopped by extension. Introduced in PHP 5.2.0.
      
      
      // First check if the file was too large, either for PHP or for our config
      if (
          // File exceeded maximum upload size defined in php.ini (reliable)
          $input['files'][$i]['file']['error'] == UPLOAD_ERR_INI_SIZE ||
          // File exceeded MAX_FILE_SIZE value from form (unreliable)
          $input['files'][$i]['file']['error'] == UPLOAD_ERR_FORM_SIZE ||
          // File exceeded configuration setting (reliable)
          $input['files'][$i]['file']['filesize'] > $config['upload']['maxFileSize']
         )
      {
        $messages['errors'][] = str_replace('%ID%', $i, $lang['errors']['maximumFileSizeExceeded']);
        $invalid['files'][$i]['file'] = true;
        
        
        // If the temporary name is set, and it is found to be an uploaded file,
        // delete it
        if (!empty($input['files'][$i]['file']['tmpfilename']) && is_uploaded_file($input['files'][$i]['file']['tmpfilename']))
          unlink($input['files'][$i]['file']['tmpfilename']);
      }
      
      // If the file error code is not UPLOAD_ERR_OK, there was an error
      // during the upload. We already caught errors for files that are too
      // large, so this is other errors.
      else if ($input['files'][$i]['file']['error'] !== UPLOAD_ERR_OK)
      {
        // Other errors the user doesn't need to know about
        $messages['errors'][] = str_replace('%ID%', $i, $lang['errors']['miscUploadError']);
        $invalid['files'][$i]['file'] = true;
        
        // If the temporary name is set, and it is found to be an uploaded file,
        // delete it
        if (!empty($input['files'][$i]['file']['tmpfilename']) && is_uploaded_file($input['files'][$i]['file']['tmpfilename']))
          unlink($input['files'][$i]['file']['tmpfilename']);
      }
      
      // Technically this shouldn't get this far, but just to be safe. Verify
      // that the file is indeed an uploaded file. This helps prevent a
      // malicious user from gaining access to system files by exploiting the
      // upload form.
      else if (!is_uploaded_file($input['files'][$i]['file']['tmpfilename']))
      {
        $messages['errors'][] = str_replace('%ID%', $i, $lang['errors']['miscUploadError']);
        $invalid['files'][$i]['file'] = true;
        
        // Don't unlink() the file because it wasn't something the user actually
        // uploaded. For instance, it could be a system file.
      }
      else if (strtolower(end(explode('.', $input['files'][$i]['file']['filename']))) != 'png')
      {
        $input['files'][$i]['invalid'] = true;
        $messages['errors'][] = str_replace('%ID%', $i, $lang['errors']['invalidFileFormat']);
        $invalid['files'][$i]['file'] = true;
        
        // Delete the temporary file
        if (!empty($input['files'][$i]['file']['tmpfilename']))
          unlink($input['files'][$i]['file']['tmpfilename']);
      }
      // Verify that this file is indeed a PNG file. Could also use the output
      // from getimagesize to constrain images to a specific size. For now, we
      // just make sure that width and height at at least 1.
      else {
        $img = getimagesize($input['files'][$i]['file']['tmpfilename']);
        
        if (!$img)
        {
          $input['files'][$i]['invalid'] = true;
          $messages['errors'][] = str_replace('%ID%', $i, $lang['errors']['invalidFileFormat']);
          $invalid['files'][$i]['file'] = true;
          
          // Delete the temporary file
          if (!empty($input['files'][$i]['file']['tmpfilename']))
            unlink($input['files'][$i]['file']['tmpfilename']);
        }
        // Verify that it has a width and height greater than 0
        else if ($img[0] == 0 || $img[1] == 0)
        {
          $input['files'][$i]['invalid'] = true;
          $messages['errors'][] = str_replace('%ID%', $i, $lang['errors']['invalidFileFormat']);
          $invalid['files'][$i]['file'] = true;
          
          // Delete the temporary file
          if (!empty($input['files'][$i]['file']['tmpfilename']))
            unlink($input['files'][$i]['file']['tmpfilename']);
        }
        // Verify it's a PNG
        else if ($img[2] != IMAGETYPE_PNG)
        {
          $input['files'][$i]['invalid'] = true;
          $messages['errors'][] = str_replace('%ID%', $i, $lang['errors']['invalidFileFormat']);
          $invalid['files'][$i]['file'] = true;
          
          // Delete the temporary file
          if (!empty($input['files'][$i]['file']['tmpfilename']))
            unlink($input['files'][$i]['file']['tmpfilename']);
        }
      }
          
      
      // Validate the other file author information
      
      // Author name of this file (REQUIRED)
      if (!valid_authorname($input['files'][$i]['authorname']))
      {
        $input['files'][$i]['invalid'] = true;
        $messages['errors'][] = str_replace('%ID%', $i, $lang['errors']['authornameInvalid']);
        $invalid['files'][$i]['authorname'] = true;
        unset($input['files'][$i]['authorname']);
      }
      
      // Selected license (REQUIRED)
      // 0 == "I stole this"
      // 255 == "Other OSI-Approved"
      if (!isset($input['files'][$i]['licenseselector']) || !is_numeric($input['files'][$i]['licenseselector']))
      {
        $input['files'][$i]['invalid'] = true;
        $messages['errors'][] = str_replace('%ID%', $i, $lang['errors']['licenseselectorInvalid']);
        $invalid['files'][$i]['licenseselector'] = true;
        unset($input['files'][$i]['licenseselector']);
      }
      // If they picked "I stole this", invalidate their upload request
      else if ($input['files'][$i]['licenseselector'] == 0)
      {
        $input['files'][$i]['invalid'] = true;
        $messages['errors'][] = str_replace('%ID%', $i, $lang['errors']['licenseselectorInvalid']);
        $invalid['files'][$i]['licenseselector'] = true;
        unset($input['files'][$i]['licenseselector']);
      }
      // If they picked "Other OSI-Approved", then make sure they filled in the
      // license name, and the URL and/or text of that license
      else if ($input['files'][$i]['licenseselector'] == 255)
      {
        
        // If licenseselector is 255, we need this
        if (empty($input['files'][$i]['otherlicensename']))
        {
          $input['files'][$i]['invalid'] = true;
          $messages['errors'][] = str_replace('%ID%', $i, $lang['errors']['otherlicensenameInvalid']);
          $invalid['files'][$i]['otherlicensename'] = true;
          unset($input['files'][$i]['otherlicensename']);
        }
        
        // If licenseselector is 255, we need otherlicenseurl and/or otherlicensetext
        if (empty($input['files'][$i]['otherlicenseurl']) && empty($input['files'][$i]['otherlicensetext']))
        {
          $input['files'][$i]['invalid'] = true;
          $messages['errors'][] = str_replace('%ID%', $i, $lang['errors']['otherlicenseurlandtextInvalid']);
          $invalid['files'][$i]['otherlicenseurl'] = true;
          $invalid['files'][$i]['otherlicensetext'] = true;
          unset($input['files'][$i]['otherlicenseurl'], $input['files'][$i]['otherlicensetext']);
        }
        // TODO: Add some checks to make sure the URL is valid if it was specified
      }
      
      // This is the confirmation that this image does not violate the TOS (REQUIRED)
      if (!isset($input['files'][$i]['confirm']))
      {
        $input['files'][$i]['invalid'] = true;
        $messages['errors'][] = str_replace('%ID%', $i, $lang['errors']['confirmInvalid']);
        $invalid['files'][$i]['confirm'] = true;
        unset($input['files'][$i]['confirm']);
      }
    }
    
    $validFileNames = Array();
    
    // If the form was valid, and we had valid files, add them to the queue
    if (!$input['invalid'])
    {
      for ($i = 1; $i <= $config['upload']['maxFiles']; $i++)
      {
        // If no file specified, go to the next
        if(!isset($input['files'][$i]['file'])) continue;
        
        // If invalid, go to the next
        if ($input['files'][$i]['invalid']) continue;
        
        if (move_uploaded_file($input['files'][$i]['file']['tmpfilename'], $config['paths']['tmp'].$user['bzid'].'_'.$input['files'][$i]['file']['filename']))
        {
          // Insert into queue
          $data['queue'] = Array();
          $data['queue']['bzid'] = $user['bzid'];
          $data['queue']['username'] = $user['username'];
          $data['queue']['ipaddress'] = ip2hex($_SERVER['REMOTE_ADDR']);
          $data['queue']['uploaderfirstname'] = $input['uploaderfirstname'];
          $data['queue']['uploaderlastname'] = $input['uploaderlastname'];
          $data['queue']['filename'] = $input['files'][$i]['file']['filename'];
          $data['queue']['filemd5'] = md5_file($config['paths']['tmp'].$user['bzid'].'_'.$input['files'][$i]['file']['filename']);
          $data['queue']['authorname'] = $input['files'][$i]['authorname'];
          $data['queue']['licenseid'] = $input['files'][$i]['licenseselector'];
          $data['queue']['licensename'] = @$input['files'][$i]['licensename'];
          $data['queue']['licenseurl'] = @$input['files'][$i]['licenseurl'];
          $data['queue']['licensebody'] = @$input['files'][$i]['licensebody'];
          $data['queue']['moderationstatus'] = STATUS_PENDING;
          if (!$dl->Queue_Insert($data['queue']))
            die("Unable to insert image into queue database.");
          else {
            $validFileNames[] = $data['queue']['filename'];
            // If it was uploaded successfully, there is no need to redisplay it
            // on the page, so just unset it. 
            unset($input['files'][$i]);
          }
        }
        
      }
      
      $messages['top'][] = $lang['successfulUpload'].implode(',', $validFileNames);
    
    }
    
    // Temporary debug output
    if (false) {
      echo "<pre>";
      echo "\n\nInput:\n\n";
      print_r($input);
      echo "\n\nuploadErrors:\n\n";
      print_r($messages['errors']);
      echo "\n\nFiles:\n\n";
      print_r($_FILES);
      echo "</pre>";
      exit;
    }
    
  } // if (size($_POST) > 0)

  $page['title'] = 'Upload Images';
  $page['javascripts'] = Array('util.js', 'upload.js');
	
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
