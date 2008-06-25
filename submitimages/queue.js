
// Validate an individual Accept/Reject form for the queue
function validateForm(index)
{
  var message = $('message'+index);
  var accept = $('accept'+index);
  var reject = $('reject'+index);
  
  // We need all three of these to continue. If we can not get them, just
  // return true to let the form continue without prompting. This is better
  // than preventing a buggy/old browser from using the system.
  if (!message || !accept || !reject)
    return true;

  // Accepted
  if (accept.checked)
    return true;
  // Rejected
  else if (reject.checked)
  {
    // Message wasn't set, which is required for rejecting images
    if (message.value.length == 0)
    {
      alert("You need to write a message to the user when rejecting an image.");
      return false;
    }
  }
  else
  {
    alert("You must choose either Accept or Reject.");
    return false;
  }
}
