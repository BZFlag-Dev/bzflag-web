<?php

  //////////////////////////////////////////
  // VALIDATION FUNCTIONS
  //////////////////////////////////////////
  //  
  //  This file will provide validation functions that will be used for the
  //  web services.  A shared file allows us to have a consistent set of rules
  //  for values such as usernames, as well as provide an easy way to validation
  //  items such as email.
  //  
  
  
  // This function validates a username
  // Here are the rules for usernames:
  // - Must be between 2 and 25 characters long
  // - Cannot have leading or trailing whitespace
  // - Can only have the following characters: 0 to 9, a to z, A to Z, spaces, 
  //     or any of "-_."
  function valid_username($username)
  {
    // Check the length
    if (strlen($username) < 2 || strlen($username) > 25)
      return false;
       
    // Check for leading or trailing whitespace
    if (strcmp($username, trim($username)) !== 0)
      return false;
    
    // Check for invalid characters
    for ($i = 0; $i < strlen($username); $i++)
    {
      // Convert the character to an ASCII value
      // NOTE: This prevents UTF-8 support, so keep in mind for later
      $char = ord(substr($username, $i, 1));
      
      // Useful ASCII values
      // space = 32
      // - = 45
      // . = 46
      // _ = 95
      // 0 = 48  (to)  9 = 57
      // A = 65  (to)  Z = 90
      // a = 97  (to)  z = 122
      
      if (!(
            ($char >= 65 && $char <= 90) || ($char >= 97 && $char <= 122) ||
            ($char >= 48 && $char <= 57) || $char == 45 || $char == 46 ||
            $char == 95 || $char == 32
      ))
        return false;
    }
  
    return true;
  }
  
  // This function validates an email address
  function valid_email($email)
  {
    // SOURCE: http://www.zend.com/zend/spotlight/ev12apr.php
    if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email))
      return false;
  
    return true;
  }
  
  // This function validates a password.
  // A password must be between 4 and 30 characters long
  function valid_password($password)
  {
    // Check the length
    if (strlen($password) < 4 || strlen($password) > 30)
      return false;
    
    return true;
  }
  
  // This function validates the protocol version
  function valid_version($version)
  {
    if (substr($version, 0, 4) != 'BZFS') return false;
    
    return true;
  }

  // This function validates an IP address for proper form.
  // NOTE: Does not support IPv6 at this point, since neither does BZFlag.
  function valid_IP_Address($ipaddress)
  {
    // Might be an IPv4 address, but could be a domain too. Run more checks.
    if (strpos($ipaddress, '.') !== FALSE)
    {
      // Split the value by periods
      $parts = explode('.', $ipaddress);
      // If it does not have four parts, it is not an IP address
      if (sizeof($parts) != 4) return false;
      
      // If any part is contains non-digit parts, it is not an IP address
      if (!ctype_digit($parts[0]) || !ctype_digit($parts[1]) || 
          !ctype_digit($parts[2]) || !ctype_digit($parts[3]))
        return false;
        
      // If any part is not between 0 and 255, it is not an IP address
      if (
          $parts[0] < 0 || $parts[0] > 255 ||
          $parts[1] < 0 || $parts[1] > 255 ||
          $parts[2] < 0 || $parts[2] > 255 ||
          $parts[3] < 0 || $parts[3] > 255)
        return false;
        
      // Seems to be valid
      return true;
    } // if (strpos($ipaddress, '.') !== FALSE)
    // TODO: At some point, when the game has IPv6 support, add a check for IPv6
    else return false;
  }
  
?>
