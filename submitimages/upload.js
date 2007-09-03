function $(id) {
  if (typeof id == 'string')
  {
    if (document.getElementById)
      return document.getElementById(id);
    else
      return false;
  }
  
  return false;
}


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
  if (!config_upload_maxFiles || config_upload_maxFiles <= 0) return;
  
  // Check each upload
  for (var i = 0; i < config_upload_maxFiles; i++)
  {
    var file = $('file'+i);
    var authorname = $('authorname'+i);
    var licenseselector = $('licenseselector'+i);
    var otherlicensename = $('otherlicensename'+i);
    var otherlicenseurl = $('otherlicenseurl'+i);
    var otherlicensetext = $('otherlicensetext'+i);
  }
  
  // Check uploader portion
  
  
  return false;
}
