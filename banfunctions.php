<?php

  // Passing in the following array items for $values
  // ipaddress
  // hostname
  // username or bzid

  function IsBanned($values, $bans) {
    foreach($bans as $ban) {
      // Only do the check if we have this type of value
      if (!isset($values[$ban['type']]))
        continue;
      
      // If we don't have an array for a value, toss it into one
      if (!is_array($values[$ban['type']]))
        $values[$ban['type']] = Array($values[$ban['type']]);
      // If we have an array, make sure we have items or just go to the next ban
      else if (sizeof($values[$ban['type']]) == 0)
        continue;
        
      foreach ($values[$ban['type']] as $value) {
        
        //echo "Checking $value against the ban {$ban['value']}\n";
        
        // IP Address
        // Supports exact matches, wildcards, and CIDR notation
        // Ex: 192.168.0.1
        //     192.168.0.*
        //     192.168.*.*
        //     192.*.*.*
        //     192.168.0.0/24
        if ($ban['type'] == 'ipaddress') {
          // Check if the ban has a CIDR
          if (strpos($ban['value'], '/') !== false) {
            // Split apart the IP and CIDR
            list($ip, $cidr) = explode('/', $ban['value']);
            
            // Do a binary match based on the CIDR value
            if (strncmp(sprintf("%032b", ip2long($ip)), sprintf("%032b", ip2long($value)), $cidr) == 0)
              return $ban; 
          }
          // Check if the ban has a wildcard
          else if (strpos($ban['value'], '*') !== false) {
            // Split the values into octets
            $banparts = explode('.', $ban['value']);
            $parts = explode('.', $value);
            
            // We need four parts for each
            if (sizeof($parts) != 4 || sizeof($banparts) != 4)
              continue;
            
            // Check to see if all of the parts match
            for ($i = 0; $i < 4; $i++)
              if ($banparts[$i] != '*' && $banparts[$i] != $parts[$i])
                break;
            
            // If $i is set to 4, that means that the for loop above completed.
            // If so, that means that all four octets matched up.
            if ($i == 4)
              return $ban;
          }
          else {
            // No CIDR or wildcard, so do an exact match
            if (ip2long($value) == ip2long($ban['value']))
              return $ban;
          }
        }
        
        // Hostname
        // Supports exact matches and wildcards
        // Ex: example.org
        //     *.example.org
        //     some-region*.someisp.com
        else if ($ban['type'] == 'hostname') {
          // Check if the ban has a wildcard
          if (strpos($ban['value'], '*') !== false) {
            // We have a wildcard, compare with a regex
            if (eregi(str_replace(Array('.', '*'), Array('\.', '(.*)'), $ban['value']), $value))
              return $ban;
          }
          else {
            // No wildcard, so do an exact match
            if ($value == $ban['value'])
              return $ban;
          }
        }
        
        // BZID
        // Only supports exact matches
        else if ($ban['type'] == 'bzid') {
          // Check for an exact match
          if ($value == $ban['value'])
            return $ban;
        }
        
      } // foreach ($values as $value)
      
    } // foreach($bans as $ban)
    
    // No ban matched
    return false;
    
  } // function CheckBan($values, $bans)

?>
