<?php

  function ip2hex($ipaddress)
  {
    $parts = explode('.', $ipaddress);
    
    if (sizeof($parts) !== 4) return false;
    
    $hex = "";
    for ($i = 0; $i <= 3; $i++)
    {
      if ($parts[$i] < 16) $hex .= '0'.dechex($parts[$i]);
      else $hex .= dechex($parts[$i]);
    }
    
    return $hex;
  }
  
  function hex2ip($hex)
  {
    if (strlen($hex) != 8)
      return false;
    
    return hexdec(substr($hex, 0, 2)).".".hexdec(substr($hex, 2, 2)).".".hexdec(substr($hex, 4, 2)).".".hexdec(substr($hex, 6, 2));
  }
  
  // Function to process user input
  // 1) Strip slashes (if necessary)
  // 2) Trim whitespace (if $trimWhitespace == true)
  function processInput($value, $trimWhitespace = true)
  {
    // Step 1) Strip slashes (if necessary)

    // There are some PHP configs that strings slashes aren't added to input strings.
    // These will be accounted for here, to prevent doing a strip_slashes on a string
    // when you don't need to
    
    // Cache our call about magic quotes, and only run once per page request
    static $magicQuotes = -1;
    // It hasn't run yet, so look up magic quotes
    if ($magicQuotes == -1)
      $magicQuotes = get_magic_quotes_gpc();

    if ($magicQuotes == 1) {
      $value = stripslashes($value);
    }
    
    // Step 2) Trim whitespace if requested to do so. On by default.
    if ($trimWhitespace)
      $value = trim($value);

    return $value;
  }


?>
