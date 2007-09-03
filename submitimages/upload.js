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
  if (selected != 0)
  {
    $('otherlicense'+uploadIndex).style.display = 'none';
    $('otherlicensename'+uploadIndex).disabled = true;
    $('otherlicenseurl'+uploadIndex).disabled = true;
    $('otherlicensetext'+uploadIndex).disabled = true;
  }
  else
  {
    $('otherlicensename'+uploadIndex).disabled = false;
    $('otherlicenseurl'+uploadIndex).disabled = false;
    $('otherlicensetext'+uploadIndex).disabled = false;
    $('otherlicense'+uploadIndex).style.display = '';
  }
}

function showLicense(uploadIndex)
{
  var selected = $('licenseselector'+uploadIndex).value;
  
  if (!config_licenses || !config_licenses[selected]) return;
  
  var licensepopup = window.open(config_licenses[selected][1], 'licensepopup');
  licensepopup.focus();
}
