<?php
  // List server rewrite by blast007 WiP
  // Started 6/4/2007
  
  /* Random thoughts and ideas
  - Country code for servers - a_temp_dist
  - < L4m3r> possibly a bit to choose whether or not other server owners are allowed to "use" the group
    So effective a non-sharable group. This would require the other part of L4m3r's idea
     * L4m3r thinks the list server should have an "owner" field for all listed servers
     This would also tie in with requiring a valid username/password for running public servers
     So, to summarize, it would be the following:
     - Username/password specified to run public servsers
     - Use this user's ID as the Owner ID of the server
     - Allow groups to have a non-sharable bit set, where the group can only be used if the owner of the group
        and owner of the server match     
  
  
  
  
  */
  
  include('config.php'); if (!defined('CONFIGURATION')) die("ERROR: Unable to load configuration\n");;
 
  // Set up some headers right away
  header('Cache-Control: no-cache');
  header('Pragma: no-cache');
  header("Connection: close");
  // TODO: Should also send proper content-type (text/html or text/plain)
  // depending on if we're showing the bzfls web form or not
  header('Content-Type: text/plain');
  
  //////////////////////////////////////////
  
  // Read any user input, and determine the course of action to take
  
  // NOTE: In the previous incarnation of bzfls.php, slashes were added to input
  // and expected on input. In this version, they will be stripped away if the
  // input contains them, and only escaping user input when working with
  // database queries
  
  // The $_REQUEST variable contins the combination of the $_POST, $_GET, and
  // $_COOKIE variables. The order of precidence is based on the configuration
  // in php.ini
  // We use array_key_exists to check if they were specified, in case of a null
  // or empty value
  
  // Keep the global scope clean, and place all input into an array
  $input = Array();
  
  // TODO: Validate the input using validation functions
  // TODO: Make some validation functions
  
  // Determine which action we will be performing
  if (array_key_exists('action', $_REQUEST))
  {
    $input['action'] = strtoupper(smart_strip_slashes($_REQUEST['action']));
    
    // List of valid actions:
    // ADD - Request from bzfs to add a public server
    // REMOVE - Request from bzfs to remove a public server
    // LIST - Request from client to obtain a list of servers
    // GETTOKEN - Request from client to obtain an authentication token
    // CHECKTOKENS - Request from server to validate an authentication token
    // REGISTER - Request from client to register a callsign
    // CONFIRM - Request from browser(?) to confirm a callsign registration
    // NOTE: REGISTER and CONFIRM might be altered from the original
    //   implementation. Should not be a problem, since they aren't used anyway.
    
    if (!in_array($input['action'], Array('ADD', 'REMOVE', 'LIST', 'GETTOKEN', 'CHECKTOKENS', 'REGISTER', 'CONFIRM')))
    {
      die("ERROR: Invalid action specified. Insert magic HTML form here.\n");
    }
    
    
    // Now that we have the action, determine what other input we will need
    
    // nameport (ADD, REMOVE) - Public hostname/IP and port, in the form of
    //   name:port or name
    // NOTE: We won't use 'nameport' internally. This is simply used for 
    // compatability reasons with 2.0.x and 1.10.x servers
    if (array_key_exists('nameport', $_REQUEST) && ($input['action'] == 'ADD' || $input['action'] == 'REMOVE'))
    {
      $input['nameport'] = smart_strip_slashes($_REQUEST['nameport']);
      
      // Also populate the 'name' and 'port'
      
      // If there is not a colon, use port 5154
      if (strpos($input['nameport'], ':') === FALSE)
      {
        // No port was specified, so the whole 'nameport' is our name
        $input['name'] = $input['nameport'];
        // No port was specified, use 5154
        $input['port'] = 5154;
      }
      else
      {
        // Populate 'name' and 'port' by splitting 'nameport' by the colon
        list($input['name'], $input['port']) = explode(':', $input['nameport']);
      }
      
      // We don't need no nameport!
      unset($input['nameport']);
    }
    // If we didn't use 'nameport', see if we used 'name' and/or 'port'
    else
    {
      // name (ADD, REMOVE) - Public hostname/IP
      if (array_key_exists('name', $_REQUEST) && $input['action'] == 'ADD' || $input['action'] == 'REMOVE')
        $input['name'] = smart_strip_slashes($_REQUEST['name']);
      
      // port (ADD, REMOVE) - Public port
      if (array_key_exists('port', $_REQUEST) && ($input['action'] == 'ADD' || $input['action'] == 'REMOVE'))
        $input['port'] = smart_strip_slashes($_REQUEST['port']);
      // If no port was specified, and we're doing an ADD or REMOVE, use 5154
      else if ($input['action'] == 'ADD' || $input['action'] == 'REMOVE')
        $input['port'] = 5154;
    }

    // build (ADD) - Build string of server; Could also be used for clients
    if (array_key_exists('build', $_REQUEST) && ($input['action'] == 'ADD'))
      $input['build'] = smart_strip_slashes($_REQUEST['build']);
    
    // version (ADD, LIST) - Protocol string, specified to filter which
    //   servers are shown in a LIST, in the form BZFSxxxx, where xxxx is a
    //   number; 2.0.x uses BZFS0026, for example
    if (array_key_exists('version', $_REQUEST) && ($input['action'] == 'ADD' || $input['action'] == 'LIST'))
      $input['version'] = smart_strip_slashes($_REQUEST['version']);
    
    // gameinfo (ADD) - Hex string that defines server configuration and
    //   player counts; Technically could be removed, and have bzfls.php
    //   query the server to obtain this information
    if (array_key_exists('gameinfo', $_REQUEST) && ($input['action'] == 'ADD'))
      $input['gameinfo'] = smart_strip_slashes($_REQUEST['gameinfo']);
    
    // title (ADD) - Public title/description of a server
    if (array_key_exists('title', $_REQUEST) && ($input['action'] == 'ADD'))
      $input['title'] = smart_strip_slashes($_REQUEST['title']);
    
    // checktokens (ADD, CHECKTOKENS) - Contains tokens that will be verified;
    //   These will be in the form callsign0=token or callsign0@ipaddress=token,
    //   and multiple instances of these can be seperated by \n
    if (array_key_exists('checktokens', $_REQUEST) && ($input['action'] == 'ADD' || $input['action'] == 'CHECKTOKENS'))
      $input['checktokens'] = smart_strip_slashes($_REQUEST['checktokens']);
    
    // groups (ADD, CHECKTOKENS) - List of groups to check for membership
    if (array_key_exists('groups', $_REQUEST) && ($input['action'] == 'ADD' || $input['action'] == 'CHECKTOKENS'))
      $input['groups'] = smart_strip_slashes($_REQUEST['groups']);
    
    // callsign (LIST, GETTOKEN, REGISTER) - Callsign of player
    if (array_key_exists('callsign', $_REQUEST) && ($input['action'] == 'LIST' || $input['action'] == 'GETTOKEN' || $input['action'] == 'REGISTER'))
      $input['callsign'] = smart_strip_slashes($_REQUEST['callsign']);
    
    // email (REGISTER, CONFIRM) - Email for registration
    if (array_key_exists('email', $_REQUEST) && ($input['action'] == 'REGISTER' || $input['action'] == 'CONFIRM'))
      $input['email'] = smart_strip_slashes($_REQUEST['email']);
    
    // password (LIST, GETTOKEN, REGISTER, CONFIRM) - User's password
    if (array_key_exists('password', $_REQUEST) && ($input['action'] == 'LIST' || $input['action'] == 'GETTOKEN' || $input['action'] == 'REGISTER' || $input['action'] == 'CONFIRM'))
      $input['password'] = smart_strip_slashes($_REQUEST['password']);
    
    // advertgroups (ADD) - Specific groups to advertise the server to
    if (array_key_exists('advertgroups', $_REQUEST) && ($input['action'] == 'ADD'))
      $input['advertgroups'] = smart_strip_slashes($_REQUEST['advertgroups']);
  } // if (array_key_exists('action', $_REQUEST))
  
  // No action specified
  // TODO: Print HTML form thingy
  else
  {
    die("ERROR: No action specified. Insert magic HTML form here.\n");
  }
  
  
  //////////////////////////////////////////
  
  // Ready, Camera, ACTION!!
  
  //////////////////////////////////////////
  // ADD
  //////////////////////////////////////////
  if ($input['action'] == 'ADD')
  {
    // First off, check for some required input
  
    // We need to have a protocol version. If we do not, abort.
    if (!isset($input['version']) || empty($input['version']))
      die("ERROR: Protocol version not specified.\n");
      
    // We need to have the game info. If we do not, abort.
    if (!isset($input['gameinfo']) || empty($input['gameinfo']))
      die("ERROR: Game info not specified.\n");
      
    // Start off by copying all user input into our values to be passed
    $values = $input;
    
    // Next, see if we have a 'name' specified
    // Make sure it's not already a valid IP address
    if (isset($values['name'])) {
      // First, check if this already IS an IP address
      if (valid_IP_Address($values['name']))
      {
        $values['ipaddress'] = $values['name'];
      }
      // Was not an IP address. Try to resolve it.
      else
      {
        $values['ipaddress'] = gethostbyname($values['name']);
        
        // Unable to resolve the name, use the REMOTE_ADDR
        if ($values['ipaddress'] == $values['name'])
        {
          $values['name'] = $values['ipaddress'] = $_SERVER['REMOTE_ADDR'];
        }
      }
      
      // If the IP address equals 0.0.0.0, set it to the requesting address
      if ($values['ipaddress'] == '0.0.0.0')
      {
        $values['name'] = $values['ipaddress'] = $_SERVER['REMOTE_ADDR'];
      }
      
      // Verify that the IP address they specify is actually the one requesting
      // the ADD. This prevents a rogue user from altering list entries.
      else if (!$config['disableIPCheck'] && $values['ipaddress'] != $_SERVER['REMOTE_ADDR'])
      {
        die("ERROR: Specified IP did not match the requesting IP.\n");
      } 
    }
    // No name specified, use the request IP for 'name' and 'ipaddress'
    else
    {
      $values['name'] = $values['ipaddress'] = $_SERVER['REMOTE_ADDR'];
    }
    
    // Set up the lastmodified value
    $values['lastmodified'] = mktime();
    
    
    // Now that we have the IP address, see if this server is already in our
    // database.  If so, just update, otherwise, insert a new record
    
    // Load the DataLayer 
    @include_once('includes/datalayer.class.php');
    // Make sure the DataLayer class loaded sucessfully
    if (!class_exists('DataLayer')) die("ERROR: Unable to load DataLayer class.\n");
    $dl = new DataLayer($config['sql']['hostname'], $config['sql']['username'], $config['sql']['password'], $config['sql']['database']);
    
    // Server already exists, update it
    if ($dl->Server_Fetch_ByIPAddressPort($values) !== false)
    {
      $dl->Server_Update_ByIPAddressPort($values);
    }
    // Server doesn't exist, insert it
    else 
    {
      $dl->Server_Insert($values);
    }

  } // if ($input['action'] == 'ADD')
  
  
  // NOTE: The REMOVE action does not completely work when disableIPCheck is
  // enabled.  We do not resolve the name specified, so it might not always
  // match what is given. Minor, but should be fixed.
  else if ($input['action'] == 'REMOVE')
  {
    // Load the DataLayer 
    @include_once('includes/datalayer.class.php');
    // Make sure the DataLayer class loaded sucessfully
    if (!class_exists('DataLayer')) die("ERROR: Unable to load DataLayer class.\n");
    $dl = new DataLayer($config['sql']['hostname'], $config['sql']['username'], $config['sql']['password'], $config['sql']['database']);
    
    // Values that will be passed to our DataLayer
    $values = Array();
       
    // We do not need to worry about the hostname. Just use the requesting
    // address.
    $values['ipaddress'] = $_SERVER['REMOTE_ADDR'];
    
    // Grab the port that was specified from our input
    $values['port'] = $input['port'];
    
    
    // Now that we have the IP address, see if this server is already in our
    // database.  If so, just update, otherwise, insert a new record
    
    // Remove the server
    $dl->Server_Delete_ByIPAddressPort($values);
  }
  
  // List servers
  else if ($input['action'] == 'LIST')
  {
    // Load the DataLayer 
    @include_once('includes/datalayer.class.php');
    // Make sure the DataLayer class loaded sucessfully
    if (!class_exists('DataLayer')) die("ERROR: Unable to load DataLayer class.\n");
    $dl = new DataLayer($config['sql']['hostname'], $config['sql']['username'], $config['sql']['password'], $config['sql']['database']);
    
    // Prune out dead servers
    $dl->Servers_Delete_ByAge(mktime()-$config['maximumServerAge']);
    
    // Are we filtering just for a specific version?
    if (isset($input['version']) && !empty($input['version']))
      $data['servers'] = $dl->Servers_Fetch_ByVersion($input['version']);
    // Else, show all the servers
    else
      $data['servers'] = $dl->Servers_Fetch_All();
    // name:port version gameinfo ipaddress title
    
    if ($data['servers'] && sizeof($data['servers']) > 0)
    {
      foreach($data['servers'] as $server)
        echo $server['name'].':'.$server['port'].' '.$server['version'].' '.$server['gameinfo'].' '.$server['ipaddress'].' '.$server['title']."\n";
    }
    
  }
  else if ($input['action'] == 'GETTOKEN')
  {
  
  }
  else if ($input['action'] == 'CHECKTOKENS')
  {
  
  }
  else if ($input['action'] == 'REGISTER')
  {
    if (!isset($input['callsign']) || !valid_callsign($input['callsign']))
    {
      die("ERROR: A valid callsign was not specified. Callsigns must use only ".
          "alphanumeric characters, hyphens, underscores, or periods. Must be ".
          "2 to 25 characters long.");
    }

    // TODO: Allow banning of email addresses with wildcard support
    if (!isset($input['email']) || !valid_email($input['email']))
    {
      die("ERROR: A valid email was not specified.");
    }
    
    if (!isset($input['password']) || !valid_password($input['password']))
    {
      die("ERROR: Password must be between 4 and 30 characters long");
    }
  }
  else if ($input['action'] == 'CONFIRM')
  {
  
  }
  
  
  //////////////////////////////////////////
  // VALIDATION FUNCTIONS
  //////////////////////////////////////////
  
  // This function validates a callsign
  // Here are the rules for callsigns:
  // - Must be between 2 and 25 characters long
  // - Cannot have leading or trailing whitespace
  // - Can only have the following characters: 0 to 9, a to z, A to Z, spaces, 
  //     or any of "-_."
  function valid_callsign($callsign)
  {
    // Check the length
    if (strlen($callsign) < 2 || strlen($callsign) > 25)
      return false;
       
    // Check for leading or trailing whitespace
    if (strcmp($callsign, trim($callsign)) !== 0)
      return false;
    
    // Check for invalid characters
    for ($i = 0; $i < strlen($callsign); $i++)
    {
      // Convert the character to an ASCII value
      // NOTE: This prevents UTF-8 support, so keep in mind for later
      $char = ord(substr($callsign, $i, 1));
      
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
      

  //////////////////////////////////////////
  // UTILITY FUNCTIONS
  //////////////////////////////////////////
 

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


?>
