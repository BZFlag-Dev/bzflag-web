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


function handleLicenseSelector(obj)
{
  // licenseselector = 15 characters
  var num = obj.id.substring('licenseselector'.length);
  
  // If they did not choose "Other OSI-Approved", just show them the license
  if (obj.value != 0)
  {
    $('otherlicense'+num).style.display = 'none';
    $('licensename'+num).disabled = true;
    $('licenseurl'+num).disabled = true;
    $('licensetext'+num).disabled = true;
  }
  else
  {
    $('licensename'+num).disabled = false;
    $('licenseurl'+num).disabled = false;
    $('licensetext'+num).disabled = false;
    $('otherlicense'+num).style.display = '';
  }
}
