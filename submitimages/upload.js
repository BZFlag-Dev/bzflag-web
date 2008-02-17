function init()
{
  // The Other OSI-Approved form fields will show up by default, in case
  // someone has JavaScript disabled. So, instead, we hide them with JavaScript
  // initially.
  for (var i = 1; i <= config_upload_maxFiles; i++)
    handleLicenseSelector(i);
}
// Run our init() function on page load
window.onload=init;

function handleLicenseSelector(uploadIndex)
{
  var selected = $('licenseselector'+uploadIndex).value;
  
  // If they did not choose "Other OSI-Approved", just show them the license
  // selector
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

// Used to pop up a link to the currently selected license
function showLicense(uploadIndex)
{
  var selected = $('licenseselector'+uploadIndex).value;
  
  // Make sure we have the current license
  if (!config_licenses || !config_licenses[selected]) return;
  
  // Open the popup
  var licensepopup = window.open(config_licenses[selected][1], 'licensepopup');
  // Focus on the popup
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
  var output = Array();
  
  // If there was an error, this will be set to true. We won't bail out of the
  // script until everything was validated so that we can highlight every error
  var isError = false;
  
  // Only show the permission page once
  // This is triggered when they have "I stole this" for an image license
  var shownPermissionPage = false;
  
  // Keep track of how many images the user wants to upload
  var imageUploads = 0;
  
  // Check each upload
  for (var i = 1; i <= config_upload_maxFiles; i++)
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
        output.push(lang['errorAuthornameInvalid'].replace("%ID%", i));
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
        output.push(lang['errorLicenseselectorInvalid'].replace("%ID%", i));
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
          output.push(lang['errorOtherlicensenameInvalid'].replace("%ID%", i));
          licenseselector.className = 'invalid';
          otherlicensename.className = 'invalid';
        }
        else otherlicensename.className = '';
        
        // If there is no license url or text, mark the selector and the url and
        // text fields invalid
        if (otherlicenseurl.value.length == 0 && otherlicensetext.value.length == 0)
        {
          isError = true;
          output.push(lang['errorOtherlicenseurlandtextInvalid'].replace("%ID%", i));
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
        output.push(lang['errorConfirmInvalid'].replace("%ID%", i));
        $('confirmlabel'+i).className = 'invalid';
      }
      else $('confirmlabel'+i).className = '';
        
    } // if (file.value.length > 0)
  }
  
  // If the user didn't fill in any of the upload boxes, we have nothing to do
  if (imageUploads == 0)
  {
    output.push(lang['errorNoFilesSpecified']);
    isError = true;
  } 
  
  // Check uploader portion
  var uploaderfirstname = $('uploaderfirstname');
  var uploaderlastname = $('uploaderlastname');
  var uploaderemail = $('uploaderemail');
  var confirmtos = $('confirmtos');
  var confirmaccurate = $('confirmaccurate');
  
  // Check uploader first name
  if (uploaderfirstname.value.length == 0)
  {
    isError = true;
    output.push(lang['errorUploaderfirstnameInvalid']);
    uploaderfirstname.className = 'invalid';
  }
  else uploaderfirstname.className = '';
  
  // Check uploader last name
  if (uploaderlastname.value.length == 0)
  {
    isError = true;
    output.push(lang['errorUploaderlastnameInvalid']);
    uploaderlastname.className = 'invalid';
  }
  else uploaderlastname.className = '';
  
  // Verify the first name doesn't start with 's' and that the last name isn't
  // 'ubmitimages'
  if (uploaderfirstname.value.substr(0, 1).toLowerCase() == "s" && uploaderlastname.value.toLowerCase() == "ubmitimages")
  {
    isError = true;
    output.push(lang['errorUploaderfirstnameInvalid']);
    output.push(lang['errorUploaderlastnameInvalid']);
    uploaderfirstname.className = 'invalid';
    uploaderlastname.className = 'invalid';
  }

  // Check uploader email address
  var emailreg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9\-\.])+\.([A-Za-z]{2,4})$/;
  
  if (uploaderemail.value.length == 0 || emailreg.test(uploaderemail.value) == false)
  {
    isError = true;
    output.push(lang['errorUploaderemailInvalid']);
    uploaderemail.className = 'invalid';
  }
  else uploaderemail.className = '';
  
  // Check overall TOS confirmation
  if (!confirmtos.checked)
  {
    isError = true;
    output.push(lang['errorConfirmtosInvalid']);
    $('confirmtoslabel').className = 'invalid';
  }
  else $('confirmtoslabel').className = '';
  
  // Check confirmation to accuracy
  if (!confirmaccurate.checked)
  {
    isError = true;
    output.push(lang['errorConfirmaccurateInvalid']);
    $('confirmaccuratelabel').className = 'invalid';
  }
  else $('confirmaccuratelabel').className = '';
  
  var erroroutput = $('erroroutput');
  for (var a = 0; a < erroroutput.childNodes.length; a++)
  {
    if (erroroutput.childNodes[a].nodeName == "LEGEND")
      erroroutput.childNodes[a].textContent = (isError)?lang['errors']:lang['pleasewait'];
    else if (erroroutput.childNodes[a].nodeName == "UL")
    {
      // We'll be using this item a few times here, so store it in a short name
      var list = erroroutput.childNodes[a];
      // First step, remove all the child elements from our UL.
      for (var b = 0; b < list.childNodes.length; x++)
        list.removeChild(list.childNodes[b]);

      if (isError)
      {
        // Add the error output to the unordered list
        for (var x = 0; x < output.length; x++)
        {
          var item = document.createElement("LI");
          // Since we may include HTML in our messages, use innerHTML instead of
          // using textContent.
          item.innerHTML = output[x];
          list.appendChild(item);
        }
      }
      else
      {
        var item = document.createElement("LI");
        // Since we may include HTML in our messages, use innerHTML instead of
        // using textContent.
        item.innerHTML = lang['pleasewaituploading'];
        list.appendChild(item);
      }
    }
  }
  
  // Show the error output fieldset and the horizontal-rule above it
  erroroutput.style.display = '';
  $('errorhr').style.display = '';
  
  return !isError;
}
