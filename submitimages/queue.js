
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

  // Accepted, so confirm
  if (accept.checked)
    return confirm("Are you sure you want to ACCEPT this image?");
  // Rejected
  else if (reject.checked)
  {
    // Rejected, and message was set, so confirm
    if (message.value.length > 0)
      return confirm("Are you sure you want to REJECT this image?");
    // Message wasn't set, which is required for rejecting images
    else
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
