function $(id)
{
  if (typeof id == 'string')
  {
    if (document.getElementById)
      return document.getElementById(id);
    else
      return false;
  }
  
  return false;
}

function doPopup(url, name)
{
  //config_paths_baseURL
  var popup = window.open(url, name);
  if (!popup) return true;
  popup.focus();
  return false;
}
