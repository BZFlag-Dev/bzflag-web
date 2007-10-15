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


?>
