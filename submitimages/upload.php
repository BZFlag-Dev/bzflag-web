<?php

  define('USING_SMARTY', true);
  define('USING_QUEUEDB', true);

  include('common.php');
  // Must be logged in to upload images
  if (!$user)
  {
    header('Location: '.$config['paths']['baseURL']);
    exit;
  }
  
  include($config['paths']['includes'].'validation.php');
  
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
        $input['files'][$i]['authorname'] = processInput($_POST['authorname'][$i]);
      
      // Selected license (REQUIRED)
      // 0 == "I stole this"
      // 255 == "Other OSI-Approved"
      if (isset($_POST['licenseselector'][$i]))
        $input['files'][$i]['licenseselector'] = $_POST['licenseselector'][$i];
      
      // Run the following input checks only if the licenseselector is 255
      if ($input['files'][$i]['licenseselector'] == 255)
      {
        // If licenseselector is 255, we need this
        if (isset($_POST['licensename'][$i]))
          $input['files'][$i]['licensename'] = processInput($_POST['licensename'][$i]);
        
        // If licenseselector is 255, we need this and/or licensetext
        if (isset($_POST['licenseurl'][$i]))
          $input['files'][$i]['licenseurl'] = processInput($_POST['licenseurl'][$i]);
        
        // If licenseselector is 255, we need this and/or licenseurl
        if (isset($_POST['licensetext'][$i]))
          // Passing a false into processInput so we don't strip whitespace. Some
          // licenses may use that to show proper formatting.
          $input['files'][$i]['licensetext'] = processInput($_POST['licensetext'][$i], false);
      }
      
      // This is the confirmation that this image does not violate the TOS (REQUIRED)
      if (isset($_POST['confirm'][$i]))
        $input['files'][$i]['confirm'] = $_POST['confirm'][$i];
    } // for ($i = 1; $i <= $config['upload']['maxFiles']; $i++)
    
    // Uploader first name (REQUIRED)
    if (isset($_POST['uploaderfirstname']))
      $input['uploaderfirstname'] = processInput($_POST['uploaderfirstname']);
    
    // Uploader last name (REQUIRED)
    if (isset($_POST['uploaderlastname']))
      $input['uploaderlastname'] = processInput($_POST['uploaderlastname']);

    // Uploader email address (REQUIRED)
    if (isset($_POST['uploaderemail']))
      $input['uploaderemail'] = processInput($_POST['uploaderemail']);
    
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
      $messages['errors'][] = $lang['errorUploaderfirstnameInvalid'];
      $invalid['uploaderfirstname'] = true;
    }
    
    // Uploader last name
    if (strlen($input['uploaderlastname']) == 0)
    {
      $input['invalid'] = true;
      $messages['errors'][] = $lang['errorUploaderlastnameInvalid'];
      $invalid['uploaderlastname'] = true;
    }
    
    if (strtolower(substr($input['uploaderfirstname'], 0, 1)) == "s" && strtolower($input['uploaderlastname']) == "ubmitimages")
    {
      $input['invalid'] = true;
      $messages['errors'][] = $lang['errorUploaderfirstnameInvalid'];
      $invalid['uploaderfirstname'] = true;
      $messages['errors'][] = $lang['errorUploaderlastnameInvalid'];
      $invalid['uploaderlastname'] = true;
    }

    // Uploader email address
     if (strlen($input['uploaderemail']) == 0 || ereg("^([A-Za-z0-9_\-\.])+@([A-Za-z0-9\-\.])+\.([A-Za-z]{2,4})$", $input['uploaderemail']) === false)
     {
       $input['invalid'] = true;
       $messages['errors'][] = $lang['errorUploaderemailInvalid'];
       $invalid['uploaderemail'] = true;
     }
    
    // Confirm the Terms of Service
    if (!isset($input['confirmtos']) || !$input['confirmtos'])
    {
      $input['invalid'] = true;
      $messages['errors'][] = $lang['errorConfirmtosInvalid'];
      $invalid['confirmtos'] = true;
    }
    
    // Confirm the accuracy of the form data
    if (!isset($input['confirmaccurate']) || !$input['confirmaccurate'])
    {
      $input['invalid'] = true;
      $messages['errors'][] = $lang['errorConfirmaccurateInvalid'];
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
        $messages['errors'][] = str_replace('%ID%', $i, $lang['errorMaximumFileSizeExceeded']);
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
        $messages['errors'][] = str_replace('%ID%', $i, $lang['errorMiscUploadError']);
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
        $messages['errors'][] = str_replace('%ID%', $i, $lang['errorMiscUploadError']);
        $invalid['files'][$i]['file'] = true;
        
        // Don't unlink() the file because it wasn't something the user actually
        // uploaded. For instance, it could be a system file.
      }
      else if (strtolower(end(explode('.', $input['files'][$i]['file']['filename']))) != 'png')
      {
        $input['files'][$i]['invalid'] = true;
        $messages['errors'][] = str_replace('%ID%', $i, $lang['errorInvalidFileFormat']);
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
          $messages['errors'][] = str_replace('%ID%', $i, $lang['errorInvalidFileFormat']);
          $invalid['files'][$i]['file'] = true;
          
          // Delete the temporary file
          if (!empty($input['files'][$i]['file']['tmpfilename']))
            unlink($input['files'][$i]['file']['tmpfilename']);
        }
        // Verify that it has a width and height greater than 0
        else if ($img[0] == 0 || $img[1] == 0)
        {
          $input['files'][$i]['invalid'] = true;
          $messages['errors'][] = str_replace('%ID%', $i, $lang['errorInvalidFileFormat']);
          $invalid['files'][$i]['file'] = true;
          
          // Delete the temporary file
          if (!empty($input['files'][$i]['file']['tmpfilename']))
            unlink($input['files'][$i]['file']['tmpfilename']);
        }
        // Verify it's a PNG
        else if ($img[2] != IMAGETYPE_PNG)
        {
          $input['files'][$i]['invalid'] = true;
          $messages['errors'][] = str_replace('%ID%', $i, $lang['errorInvalidFileFormat']);
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
        $messages['errors'][] = str_replace('%ID%', $i, $lang['errorAuthornameInvalid']);
        $invalid['files'][$i]['authorname'] = true;
        unset($input['files'][$i]['authorname']);
      }
      
      // Selected license (REQUIRED)
      // 0 == "I stole this"
      // 255 == "Other OSI-Approved"
      if (
          // If it wasn't set, it's invalid
          !isset($input['files'][$i]['licenseselector']) ||
          // If it's not a number, it's invalid
          !is_numeric($input['files'][$i]['licenseselector']) ||
          // Or if the license index doesn't exist, it's invalid
          !isset($config['licenses'][ $input['files'][$i]['licenseselector'] ])
         )
      {
        $input['files'][$i]['invalid'] = true;
        $messages['errors'][] = str_replace('%ID%', $i, $lang['errorLicenseselectorInvalid']);
        $invalid['files'][$i]['licenseselector'] = true;
        unset($input['files'][$i]['licenseselector']);
      }
      // If they picked "I stole this", invalidate their upload request
      else if ($input['files'][$i]['licenseselector'] == 0)
      {
        $input['files'][$i]['invalid'] = true;
        $messages['errors'][] = str_replace('%ID%', $i, $lang['errorLicenseselectorInvalid']);
        $invalid['files'][$i]['licenseselector'] = true;
        unset($input['files'][$i]['licenseselector']);
      }
      // If they picked "Other OSI-Approved", then make sure they filled in the
      // license name, and the URL and/or text of that license
      else if ($input['files'][$i]['licenseselector'] == 255)
      {
        
        // If licenseselector is 255, we need this
        if (empty($input['files'][$i]['licensename']))
        {
          $input['files'][$i]['invalid'] = true;
          $messages['errors'][] = str_replace('%ID%', $i, $lang['errorOtherlicensenameInvalid']);
          $invalid['files'][$i]['licensename'] = true;
          unset($input['files'][$i]['licensename']);
        }
        else
          // Tag Other OSI-Approved License names with an asterisk.
          $input['files'][$i]['licensename'] = "*".$input['files'][$i]['licensename'];
        
        // If licenseselector is 255, we need licenseurl and/or licensetext
        if (empty($input['files'][$i]['licenseurl']) && empty($input['files'][$i]['licensetext']))
        {
          $input['files'][$i]['invalid'] = true;
          $messages['errors'][] = str_replace('%ID%', $i, $lang['errorOtherlicenseurlandtextInvalid']);
          $invalid['files'][$i]['licenseurl'] = true;
          $invalid['files'][$i]['licensetext'] = true;
          unset($input['files'][$i]['licenseurl'], $input['files'][$i]['licensetext']);
        }
        // TODO: Add some checks to make sure the URL is valid if it was specified
      }
      // Else, at this point, we can determine that they used a preselected
      // license that was not "I stole this" or "Other OSI-Approved". Fill in
      // the license name and URL. 
      else
      {
        $input['files'][$i]['licensename'] = $config['licenses'][ $input['files'][$i]['licenseselector'] ]['name'];
        $input['files'][$i]['licenseurl'] = $config['licenses'][ $input['files'][$i]['licenseselector'] ]['url'];
      }
      
      // This is the confirmation that this image does not violate the TOS (REQUIRED)
      if (!isset($input['files'][$i]['confirm']))
      {
        $input['files'][$i]['invalid'] = true;
        $messages['errors'][] = str_replace('%ID%', $i, $lang['errorConfirmInvalid']);
        $invalid['files'][$i]['confirm'] = true;
        unset($input['files'][$i]['confirm']);
      }
    }
    
    // If the form was valid, and we had valid files, add them to the queue
    if (!$input['invalid'])
    {
      // Store files that were successfully uploaded and placed into the database.
      $validFileNames = Array();
      
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
          $data['queue']['uploaderemail'] = $input['uploaderemail'];
          $data['queue']['filename'] = $input['files'][$i]['file']['filename'];
          $data['queue']['filemd5'] = md5_file($config['paths']['tmp'].$user['bzid'].'_'.$input['files'][$i]['file']['filename']);
          $data['queue']['authorname'] = $input['files'][$i]['authorname'];
          $data['queue']['licensename'] = @$input['files'][$i]['licensename'];
          $data['queue']['licenseurl'] = @$input['files'][$i]['licenseurl'];
          $data['queue']['licensetext'] = @$input['files'][$i]['licensetext'];
          if (!$qdb->Queue_Insert($data['queue']))
          {
            $messages['errors'][] = str_replace('%ID%', $i, $lang['errorMiscUploadError']);
            $input['files'][$i]['invalid'] = true;
          }
          else
            $validFileNames[] = $data['queue']['filename'];
        }
        
      }
      
      // If some files were successfully uploaded, add a message to the user,
      // and send an email to moderators.
      if (sizeof($validFileNames) > 0)
      {
        $messages['top'][] = $lang['successfulUpload'].implode(', ', $validFileNames);
        $email['body'] = $tpl->fetch('email_upload.tpl');
      }
      
      // If it was uploaded successfully, there is no need to redisplay it
      // on the page, so just unset it.
      for ($i = 1; $i <= $config['upload']['maxFiles']; $i++)
      {
        if (!isset($input['files'][$i]['file']) || $input['files'][$i]['invalid']) continue;
        unset($input['files'][$i]);
      }
        
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
      echo "\n\nPOST Data:\n\n";
      print_r($_POST);
      echo "</pre>";
      exit;
    }
    
  } // if (size($_POST) > 0)

  $page['title'] = 'Upload Images';
  $page['javascripts'] = Array('lang.js.php?lang=en', 'util.js', 'upload.js');
	
  // Render the page
  $tpl->display('header.tpl');
  $tpl->display('upload.tpl');
  $tpl->display('footer.tpl');

?>
