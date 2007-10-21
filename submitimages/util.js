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
