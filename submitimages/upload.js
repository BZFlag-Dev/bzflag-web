
function handleLicenseSelector(uploadIndex)
{
  var selected = $('licenseselector'+uploadIndex).value;
  
  // If they did not choose "Other OSI-Approved", just show them the license
  if (selected != 255)
  {
    $('otherlicense'+uploadIndex).className = 'hide';
    $('otherlicensename'+uploadIndex).disabled = true;
    $('otherlicenseurl'+uploadIndex).disabled = true;
    $('otherlicensetext'+uploadIndex).disabled = true;
  }
  else
  {
    $('otherlicensename'+uploadIndex).disabled = false;
    $('otherlicenseurl'+uploadIndex).disabled = false;
    $('otherlicensetext'+uploadIndex).disabled = false;
    $('otherlicense'+uploadIndex).className = '';
  }
}

function showLicense(uploadIndex)
{
  var selected = $('licenseselector'+uploadIndex).value;
  
  if (!config_licenses || !config_licenses[selected]) return;
  
  var licensepopup = window.open(config_licenses[selected][1], 'licensepopup');
  licensepopup.focus();
}

function showTOS()
{  
  var tospopup = window.open(config_paths_baseURL+'tos.php', 'tos');
  if (!tospopup) return true;
  tospopup.focus();
  return false;
}

function validateForm()
{
  // Error output
  var output = "";
  
  // If there was an error, this will be set to true. We won't bail out of the
  // script until everything was validated so that we can highlight every error
  var isError = false;
  
  // Only show the permission page once
  // This is triggered when they have "I stole this" for an image license
  var shownPermissionPage = false;
  
  // Keep track of how many images the user wants to upload
  var imageUploads = 0;
  
  // Check each upload
  for (var i = 0; i < config_upload_maxFiles; i++)
  {
    var file = $('file'+i);
    if (file.value.length > 0)
    {
      // They filled in the file upload box, so increment our counter
      imageUploads++;
      
      // TODO: Look for a .png extension
      
      // Verify they filled in an author field
      var authorname = $('authorname'+i);
      // Length is 0, they didn't fill in a value
      if (authorname.value.length == 0) {
        isError = true;
        output += "<li>File "+(i+1)+": No author specified.</li>";
        authorname.className = 'invalid';
      }
      else authorname.className = '';
      
      // Do some checks on the license selector.
      // A value of 0 == "I stole this"
      // A value of 255 == "Other OSI-Approved"
      var licenseselector = $('licenseselector'+i);
      if (licenseselector.value == 0)
      {
        isError = true;
        output += "<li>File "+(i+1)+": Invalid license selected.</li>";
        licenseselector.className = 'invalid';
        
        // Open a popup, but only once per validation
        if (!shownPermissionPage) {
          var istolethis = window.open(config_paths_baseURL+'permission.php', 'istolethis');
          if (istolethis) {
            shownPermissionPage = true;
            istolethis.focus();
          }
        }
      } // if (licenseselector.value == 0)
      else if (licenseselector.value == 255)
      {
        var otherlicensename = $('otherlicensename'+i);
        var otherlicenseurl = $('otherlicenseurl'+i);
        var otherlicensetext = $('otherlicensetext'+i);
        
        // Assume they provide a license name, and either a license url or
        // license text
        licenseselector.className = '';
        
        // If there is no license name, mark the selector and the name invalid
        if (otherlicensename.value.length == 0)
        {
          isError = true;
          output += "<li>File "+(i+1)+": Other OSI-Approved license selected, but no license name entered.</li>";
          licenseselector.className = 'invalid';
          otherlicensename.className = 'invalid';
        }
        else otherlicensename.className = '';
        
        // If there is no license url or text, mark the selector and the url and
        // text fields invalid
        if (otherlicenseurl.value.length == 0 && otherlicensetext.value.length == 0)
        {
          isError = true;
          output += "<li>File "+(i+1)+": Other OSI-Approved license selected, but no license URL or license text entered.</li>";
          licenseselector.className = 'invalid';
          otherlicenseurl.className = 'invalid';
          otherlicensetext.className = 'invalid';
        }
        else
        {
          otherlicenseurl.className = '';
          otherlicensetext.className = '';
        }
      } // else if (licenseselector.value == 255)
      else licenseselector.className = '';
      
      // They must also a confirm their image follows the TOS
      var confirm = $('confirm'+i);
      if (!confirm.checked)
      {
        isError = true;
        output += "<li>File "+(i+1)+": You did not confirm that your images follows the <a href=\""+config_paths_baseURL+"tos.php\" onclick=\"javascript:return showTOS();\">Terms Of Service</a></li>";
        $('confirmlabel'+i).className = 'invalid';
      }
      else $('confirmlabel'+i).className = '';
        
    } // if (file.value.length > 0)
  }
  
  // If the user didn't fill in any of the upload boxes, we have nothing to do
  if (imageUploads == 0)
  {
    output += "<li>No files were specified for upload.</li>";
    isError = true;
  } 
  
  // Check uploader portion
  var uploaderfirstname = $('uploaderfirstname');
  var uploaderlastname = $('uploaderlastname');
  var confirmtos = $('confirmtos');
  var confirmaccurate = $('confirmaccurate');
  
  // Check uploader first name
  if (uploaderfirstname.value.length == 0)
  {
    isError = true;
    output += "<li>Uploader information: No first name specified.</li>";
    uploaderfirstname.className = 'invalid';
  }
  else uploaderfirstname.className = '';
  
  // Check uploader last name
  if (uploaderlastname.value.length == 0)
  {
    isError = true;
    output += "<li>Uploader information: No last name specified.</li>";
    uploaderlastname.className = 'invalid';
  }
  else uploaderlastname.className = '';
  
  // Verify the first name doesn't start with 's' and that the last name isn't
  // 'ubmitimages'
  if (uploaderfirstname.value.substr(0, 1).toLowerCase() == "s" && uploaderlastname.value.toLowerCase() == "ubmitimages")
  {
    isError = true;
    output += "<li>Uploader information: The upload first name and last name were not valid.</li>";
    uploaderfirstname.className = 'invalid';
    uploaderlastname.className = 'invalid';
  }
  
  // Check overall TOS confirmation
  if (!confirmtos.checked)
  {
    isError = true;
    output += "<li>Uploader information: You did not confirm that your images follows the <a href=\""+config_paths_baseURL+"tos.php\" onclick=\"javascript:return showTOS();\">Terms Of Service</a></li>";
    $('confirmtoslabel').className = 'invalid';
  }
  else $('confirmtoslabel').className = '';
  
  // Check confirmation to accuracy
  if (!confirmaccurate.checked)
  {
    isError = true;
    output += "<li>Uploader information: You did not confirm the accuracy of the information on this form.</li>";
    $('confirmaccuratelabel').className = 'invalid';
  }
  else $('confirmaccuratelabel').className = '';
  
  var erroroutput = $('erroroutput');
  if (isError) erroroutput.innerHTML = "<legend>Errors</legend><p>The following errors were detected. Please correct them and try again.<ul>"+output+"</ul></p>";
  else erroroutput.innerHTML = '<legend>Please Wait...</legend><p>We are currently uploading your images. This can take some time depending on the speed your Internet.</p>';
  
  erroroutput.style.display = '';
  $('errorhr').style.display = '';
  
  return !isError;
}
