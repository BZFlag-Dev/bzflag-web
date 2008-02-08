<?php
  // English language file
  // Created by blast007
  $lang = Array();
  
  $lang['successfulUpload'] = "The following files were successfully uploaded and are now awaiting moderation: ";

  $lang['errors']['uploaderfirstnameInvalid'] = "The uploader first name was not valid.";
  $lang['errors']['uploaderlastnameInvalid'] = "The uploader last name was not valid.";
  $lang['errors']['confirmtosInvalid'] = "You did not confirm that your images follows the <a href=\"".$config['paths']['baseURL']."tos.php\" onclick=\"javascript:return showTOS();\">Terms Of Service</a>";
  $lang['errors']['confirmaccurateInvalid'] = "You did not confirm the accuracy of the information on this form.";

  $lang['errors']['maximumFileSizeExceeded'] = "File %ID%: The file exceeded the maximum allowed file size.";
  $lang['errors']['miscUploadError'] = "File %ID%: An error has occured during file upload. Please try again. If problem persists, please contact an administrator.";
  $lang['errors']['invalidFileFormat'] = "File %ID%: The file is not in PNG format.";
  $lang['errors']['authornameInvalid'] = "File %ID%: The author name was invalid.";
  $lang['errors']['licenseselectorInvalid'] = "File %ID%: The license was not specified or the specified license was invalid.";
  $lang['errors']['otherlicensenameInvalid'] = "File %ID%: You must specify a valid license name.";
  $lang['errors']['otherlicenseurlandtextInvalid'] = "File %ID%: You must specify a valid license URL or the license text.";
  $lang['errors']['otherlicenseurlInvalid'] = "File %ID%: You must specify a valid license URL.";
  $lang['errors']['otherlicensetextInvalid'] = "File %ID%: You must specify valid license text.";
  $lang['errors']['confirmInvalid'] = "File %ID%: You did not confirm that your images follow the <a href=\"".$config['paths']['baseURL']."tos.php\" onclick=\"javascript:return showTOS();\">Terms Of Service</a>";

?>
