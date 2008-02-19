<?php
  // English language file
  // Created by blast007
  $lang = Array();

  $lang['home'] = "Home";
  $lang['login'] = "Log In";
  $lang['uploadimages'] = "Upload Images";
  $lang['moderationqueue'] = "Moderation Queue";
  $lang['logout'] = "Logout";
  $lang['termsofservice'] = "Terms Of Service";

  $lang['uploadsupport'] = "Currently only PNG files are supported. There is a limit of ".($config['upload']['maxFileSize']/1024)."KB per file, and you may upload ".$config['upload']['maxFiles']." files at a time.";
  
  $lang['success'] = "Success";
  $lang['successfulUpload'] = "The following files were successfully uploaded and are now awaiting moderation: ";

  $lang['fileinformation'] = "File Information";
  $lang['filenamewas'] = "Filename of this file was";
  $lang['imagefile'] = "Image File";
  
  $lang['authorinformation'] = "Author Information";
  $lang['authorname'] = "Author Name";
  $lang['selectalicense'] = "Select a License";
  $lang['licensename'] = "License Name";
  $lang['view'] = "View";
  $lang['specifylicenseurlortext'] = "Please specify a URL to the license and/or the license text itself.";
  $lang['licenseurl'] = "License URL";
  $lang['licensetext'] = "License Text";
  $lang['thisimagetos'] = "I confirm that this image does not violate the <a href=\"".$config['paths']['baseURL']."tos.php\" onclick=\"javascript:return showTOS();\">Terms Of Service</a>.";

  $lang['otherinformation'] = "Other Information";

  $lang['uploaderinformation'] = "Uploader Information";
  $lang['firstname'] = "First Name";
  $lang['lastname'] = "Last Name";
  $lang['emailaddress'] = "Email Address";
  $lang['allimagetos'] = "I confirm that none of the above images violate the <a href=\"".$config['paths']['baseURL']."tos.php\" onclick=\"javascript:return showTOS();\">Terms Of Service</a>.";
  $lang['confirmaccurate'] = "I confirm that all the information on this form is accurate.";
  $lang['uploadimages'] = "Upload Images";
  
  $lang['errors'] = "Errors";
  $lang['currentlynoerrors'] = "There are currently no errors.";
  
  $lang['pleasewait'] = "Please Wait...";
  $lang['pleasewaituploading'] = "We are currently uploading your images. This can take some time depending on the speed your Internet connection.";
  
  $lang['other'] = "Other";


  $lang['errorUploaderfirstnameInvalid'] = "The uploader first name was not valid.";
  $lang['errorUploaderlastnameInvalid'] = "The uploader last name was not valid.";
  $lang['errorUploaderemailInvalid'] = "The uploader email address was not valid.";
  $lang['errorConfirmtosInvalid'] = "You did not confirm that your images follows the <a href=\"".$config['paths']['baseURL']."tos.php\" onclick=\"javascript:return showTOS();\">Terms Of Service</a>.";
  $lang['errorConfirmaccurateInvalid'] = "You did not confirm the accuracy of the information on this form.";
  $lang['errorMaximumFileSizeExceeded'] = "File %ID%: The file exceeded the maximum allowed file size.";
  $lang['errorMiscUploadError'] = "File %ID%: An error has occured during file upload. Please try again. If problem persists, please contact an administrator.";
  $lang['errorInvalidFileFormat'] = "File %ID%: The file is not in PNG format.";
  $lang['errorAuthornameInvalid'] = "File %ID%: The author name was invalid.";
  $lang['errorLicenseselectorInvalid'] = "File %ID%: The license was not specified or the specified license was invalid.";
  $lang['errorOtherlicensenameInvalid'] = "File %ID%: When using an Other OSI-approved license, you must specify a valid license name.";
  $lang['errorOtherlicenseurlandtextInvalid'] = "File %ID%: When using an Other OSI-approved license, you must specify a valid license URL and/or the license text.";
  $lang['errorOtherlicenseurlInvalid'] = "File %ID%: When using an Other OSI-approved license, you must specify a valid license URL.";
  $lang['errorOtherlicensetextInvalid'] = "File %ID%: When using an Other OSI-approved license, you must specify valid license text.";
  $lang['errorConfirmInvalid'] = "File %ID%: You did not confirm that your image follows the <a href=\"".$config['paths']['baseURL']."tos.php\" onclick=\"javascript:return showTOS();\">Terms Of Service</a>.";
  $lang['errorNoFilesSpecified'] = "No files were specified for upload.";

?>
