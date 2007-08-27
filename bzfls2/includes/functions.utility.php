<?php

  //////////////////////////////////////////
  // UTILITY FUNCTIONS
  //////////////////////////////////////////
  //  
  //  Utility functions provide shortcuts to common actions that will be used
  //  through-out the web services.
  //  
 

  // This automagically strips slashes from INPUT VARIABLES.  This will not
  // work on variables created within the script, or from data pulled from a
  // database. If those are needed, we could designate a $mode variable, or
  // create seperate functions to suit each area.
  function smart_strip_slashes($value)
  {
    // Some PHP configurations do not add escape slashes to input string.
    // This function will detect that, and only stripslashes if we need to

    // Detect if quotes are added for Get, Post, and Cookie input
    if (get_magic_quotes_gpc() == 1)
    {
      // Kill the slashes
      $value = stripslashes($value);
    }

    return $value;
  }
  
  // 32 characters max. That should be enough for anything...
  function generate_random_string($length=32)
  {
    if ($length > 32) $length = 32;
    else if ($length < 1) $length = 1;
    return substr(md5(uniqid(rand(), true)), 0, $length);
  }
  
?>
