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
  
  // Use the same time throughout the whole duration of the script
  define('NOW', mktime());
 
  // Set up some headers right away
  header('Cache-Control: no-cache');
  header('Pragma: no-cache');
  header("Connection: close");
  
  include('config.php'); if (!defined('CONFIGURATION'))
  {
    header('Content-Type: text/plain');
    die("ERROR: Unable to load configuration.\n");
  }
  
  include('includes/functions.utility.php');
  include('includes/functions.validation.php');

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
  // or empty value.
  
  // Keep the global scope clean, and place all input into an array
  $input = Array();
    
  // List of valid actions:
  // ADD - Request from bzfs to add a public server
  // REMOVE - Request from bzfs to remove a public server
  // LIST - Request from client to obtain a list of servers
  // GETTOKEN - Request from client to obtain an authentication token
  // CHECKTOKENS - Request from server to validate an authentication token
  // REGISTER - Request from client to register a username
  // CONFIRM - Request from browser(?) to confirm a username registration
  // NOTE: REGISTER and CONFIRM might be altered from the original
  //   implementation. Should not be a problem, since they aren't used anyway.
  
  // First check if we will be displaying the HTML form
  if (!array_key_exists('action', $_REQUEST) || !in_array($_REQUEST['action'], Array('ADD', 'REMOVE', 'LIST', 'GETTOKEN', 'CHECKTOKENS', 'REGISTER', 'CONFIRM')))
  {
    // We are displaying HTML, so send out the right content type
    header('Content-Type: text/html');
    die("<strong>ERROR:</strong> Invalid action specified. Insert magic HTML form here.");
  }
  else
  {
    // We won't be showing the HTML form, so this will be text
    header('Content-Type: text/plain');
    
    // Store the action.  After this, grab any other input we may need
    $input['action'] = strtoupper(smart_strip_slashes($_REQUEST['action']));
    
    
    // nameport (ADD, REMOVE) - Public hostname/IP and port, in the form of
    //   name:port or name
    // NOTE: We won't use 'nameport' internally. This is simply used for 
    // compatability reasons with 2.0.x and 1.10.x servers
    // DEPRECATED: in favor of 'host' and 'port'
    if (array_key_exists('nameport', $_REQUEST) && ($input['action'] == 'ADD' || $input['action'] == 'REMOVE'))
    {
      $input['nameport'] = smart_strip_slashes($_REQUEST['nameport']);
      
      // Also populate the 'name' and 'port'
      
      // If there is not a colon, use port 5154
      // NOTE: This would not be IPv6 friendly.
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
      
      // Discard 'nameport', since internally we use 'name' and 'port'
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
    //   These will be in the form username0=token or username0@ipaddress=token,
    //   and multiple instances of these can be seperated by \n
    if (array_key_exists('checktokens', $_REQUEST) && ($input['action'] == 'ADD' || $input['action'] == 'CHECKTOKENS'))
      $input['checktokens'] = smart_strip_slashes($_REQUEST['checktokens']);
    
    // groups (ADD, CHECKTOKENS) - List of groups to check for membership
    if (array_key_exists('groups', $_REQUEST) && ($input['action'] == 'ADD' || $input['action'] == 'CHECKTOKENS'))
      $input['groups'] = smart_strip_slashes($_REQUEST['groups']);
    
    // callsign (LIST, GETTOKEN) - Callsign of player
    // DEPRECATED: in favor of 'username'
    if (array_key_exists('callsign', $_REQUEST) && ($input['action'] == 'LIST' || $input['action'] == 'GETTOKEN'))
      // Store it as username
      $input['username'] = smart_strip_slashes($_REQUEST['callsign']);
      
    // username (LIST, GETTOKEN, REGISTER, CONFIRM) - Username of player
    if (array_key_exists('username', $_REQUEST) && ($input['action'] == 'LIST' || $input['action'] == 'GETTOKEN' || $input['action'] == 'REGISTER' || $input['action'] == 'CONFIRM'))
      $input['username'] = smart_strip_slashes($_REQUEST['username']);
    
    // email (REGISTER) - Email for registration
    if (array_key_exists('email', $_REQUEST) && ($input['action'] == 'REGISTER'))
      $input['email'] = smart_strip_slashes($_REQUEST['email']);
    
    // password (LIST, GETTOKEN, REGISTER) - User's password
    if (array_key_exists('password', $_REQUEST) && ($input['action'] == 'LIST' || $input['action'] == 'GETTOKEN' || $input['action'] == 'REGISTER'))
      $input['password'] = smart_strip_slashes($_REQUEST['password']);
    
    // advertgroups (ADD) - Specific groups to advertise the server to
    if (array_key_exists('advertgroups', $_REQUEST) && ($input['action'] == 'ADD'))
      $input['advertgroups'] = smart_strip_slashes($_REQUEST['advertgroups']);
    
    // activationkey (CONFIRM) - Randomly generated activation code sent via
    // email to the user.
    if (array_key_exists('activationkey', $_REQUEST) && ($input['action'] == 'CONFIRM'))
      $input['activationkey'] = smart_strip_slashes($_REQUEST['activationkey']);
  } // if (array_key_exists('action', $_REQUEST))
  
  
  //////////////////////////////////////////
  // Ready, Camera, ACTION!!
  
  
  
  
  //////////////////////////////////////////
  // ADD
  //////////////////////////////////////////
  if ($input['action'] == 'ADD')
  {
    // First off, check for some required input
  
    // We need to have a protocol version. If we do not, abort.
    if (!valid_version($input['version']))
      die("ERROR: Invalid version specified. Must begin with 'BZFS' and be followed by a four digit, zero-padded number (e.g. BZFS0026)\n");
      
    // We need to have the game info. If we do not, abort.
    if (!isset($input['gameinfo']) || empty($input['gameinfo']))
      die("ERROR: Game info not specified.\n");
       
    // Verify we have a valid port
    if (!is_numeric($input['port']) || $input['port'] < 1 || $input['port'] > 65535)
      die("ERROR: Invalid port specified.\n");
    // If the port is below 1024, they are running bzfs at elevated privledges
    else if ($input['port'] < 1024)
      echo "NOTICE: Running bzfs as root/superuser/administrator could be a security risk.\n";
          
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
    $values['lastmodified'] = NOW;
    
    
    if (!$config['disableConnectBack'])
    {
      // Verify that we can connect to the server.  If we can't open a socket
      // in 5 seconds, assume the server is down.
      // Include an @ before fsockopen and fsockclose to suppress error output
      $socket = @fsockopen ($values['ipaddress'], $values['port'], $socketerrno, $socketerrstring, 5);
      if (!$socket)
      {
        die("ERROR: Unable to connect back to server. Verify the port specified in your -p and -publicaddr values match, and that your firewall is not blocking our attempt to connect.\n");
      }
      // TODO: Query the server with this same socket
      @fclose($socket);
    }
    
    
    // Now that we have the IP address, see if this server is already in our
    // database.  If so, just update, otherwise, insert a new record
    
    // Load the DataLayer 
    @include_once('includes/datalayer.class.php');
    // Make sure the DataLayer class loaded sucessfully
    if (!class_exists('DataLayer')) die("ERROR: Unable to load DataLayer class.\n");
    $dl = new DataLayer($config['datalayer']);
    if ($dl === false) die("ERROR: Unable to connect to database.\n");
    
    // Server already exists, update it
    if ($dl->Server_Exists_ByIPAddressPort($values) !== false)
    {
      if ($dl->Server_Update_ByIPAddressPort($values))
        echo "NOTICE: Server record updated successfully!\n";
      else
        echo "ERROR: There was an error updating the server record.\n";
    }
    // Server doesn't exist, insert it
    else 
    {
      if ($dl->Server_Insert($values))
        echo "NOTICE: Server record added successfully!\n";
      else
        echo "ERROR: There was an error adding the server record.\n";
    }
    
    //////////////////////////////////
    // Check tokens in ADD too
    //////////////////////////////////
    
    if (isset($input['checktokens']))
    {
      $values = Array();
      
      // We can check multiple tokens at once. Each callsign/ip/token is seperated
      // by a CR/LF (%0D%0A)
      $values['checktokens'] = explode("\r\n", $input['checktokens']);
      
      // Initialize groups to any empty array
      $values['groups'] = Array();
      
      // We can check multiple groups at once as well. Each group is seperated
      // by a CR/LF (%0D%0A)
      if (!empty($input['groups']))
      {
        $values['groups'] = explode("\r\n", $input['groups']);
      }
  
      // Make sure we have an array before we iterate through it.  It should have
      // at least one item if we were passed any data.
      if (is_array($values['checktokens']))
      {
        // Use the variable $c as our counter
        for ($c = 0; $c < count($values['checktokens']); $c++)
        {
          // If this is blank, continue on to the next
          if (strlen(trim($values['checktokens'][$c])) == 0)
            continue;
            
          // Focus on one item at a time
          $item = Array();
          
          // If the line has an @, that means an IP address was specified.  This
          // will be checked with the token IP to prevent a token from being used
          // be another user
          if (strpos($values['checktokens'][$c], "@") !== FALSE)
          {
            // First explode by the @ symbol, to pull the username off
            list($item['username'], $item['rest']) = explode('@', $values['checktokens'][$c]);
            // Next, explode the rest by = to seperate the ip address and token
            list($item['ipaddress'], $item['token']) = explode('=', $item['rest']);
            // We are done with this variable
            unset($item['rest']);
          }
          // Else, no IP address was specified
          else
          {
            // Explode by = to seperate the username and token
            list($item['username'], $item['token']) = explode('=', $values['checktokens'][$c]);
          }
          
          // Start the response to the server
          echo "MSG: checktoken callsign=".$item['username'].", ip=".$item['ipaddress'].", token=".$item['token'];
          
          // Loop through the groups that we were checking for and display them
          for ($g = 0; $g < count($values['groups']); $g++)
          {
            // If this is blank, continue on to the next
            if (strlen(trim($values['groups'][$g])) == 0)
              continue;
              
            echo " group=".$values['groups'][$g];
          }
          // Add the usual line feed
          echo "\n";
          
          $data['player'] = $dl->Player_Fetch_ByUsername($item['username']);
          
          
          // We will respond with a TOKBAD if any of the following are met:
          // 1) If the user does not exist
          // 2) The user does not have a token
          // 3) If the token doesn't match
          // 4) The user's token has expired
          // 5) If the IP was specified, and the IP doesn't match 
          if (
              // Condition 1
              $data['player'] === false ||
              // Condition 2
              empty($data['player']['token']) ||
              // Condition 3
              $item['token'] != $data['player']['token'] ||
              // Condition 4
              $data['player']['tokendate'] + $config['token']['lifetime'] < NOW ||
              // Condition 5
              (!empty($item['ipaddress']) && $item['ipaddress'] != $data['player']['tokenipaddress'])
          )
          {
            echo "TOKBAD: ".$item['username']."\n";
          }
          // Else, their token is good. Send back a TOKGOOD
          else
          {
            // We will be using the token. Remove it from their account.
            if ($dl->Player_Update_ByUsername($data['player']['username'], Array('token' => '')))
            {
              // TODO: Enumerate the groups they are a member of (of the ones
              // requested) in the form:   :Group.Name
              // Ex: TOKGOOD: SomeUser:Group1.Name:Group2.Name
              echo "TOKGOOD: ".$item['username']."\n";
              // Write out their BZID as well
              echo "BZID: ".$data['player']['playerid']." ".$data['player']['username']."\n";
            }
            // Else, we were unable to remove the token. To prevent a security
            // issue, we won't allow the token to be used.
            else
            {
              echo "TOKBAD: ".$item['username']."\n";
            }
          }
  
        } // for ($c = 0; $c < count($values['checktokens']); $c++)
      } // if (is_array($values['checktokens']))
    } // if (isset($input['checktokens'])

  } // if ($input['action'] == 'ADD')
  
  
  //////////////////////////////////////////
  // REMOVE
  //////////////////////////////////////////
  else if ($input['action'] == 'REMOVE')
  {
  
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
  
    // Load the DataLayer 
    @include_once('includes/datalayer.class.php');
    // Make sure the DataLayer class loaded sucessfully
    if (!class_exists('DataLayer')) die("ERROR: Unable to load DataLayer class.\n");
    $dl = new DataLayer($config['datalayer']);
    if ($dl === false) die("ERROR: Unable to connect to database.\n");
    
    // Now that we have the IP address, see if this server is already in our
    // database.  If so, just update, otherwise, insert a new record
    
    // Remove the server
    $dl->Server_Delete_ByIPAddressPort($values);
  }
  
  
  //////////////////////////////////////////
  // LIST
  //////////////////////////////////////////
  else if ($input['action'] == 'LIST')
  {
    // Check some of our input
    
    if (isset($input['version']) && !valid_version($input['version']))
      die("ERROR: Invalid version specified. Must begin with 'BZFS' and be followed by a four digit, zero-padded number (e.g. BZFS0026)\n");
    
    // If they specified a username, make sure it's valid. If not, unset it and
    // spit out an error message
    if (isset($input['username']) && !valid_username($input['username']))
    {
      echo "ERROR: A valid username was not specified. Usernames must use only ".
          "alphanumeric characters, hyphens, underscores, or periods. Must be ".
          "2 to 25 characters long.\n";
      unset($input['username']);
    }
    
    if (isset($input['password']) && !valid_password($input['password']))
    {
      echo "ERROR: Password must be between 4 and 30 characters long.\n";
      unset($input['password']);
    }
    
    $values = $input;
  
    // Load the DataLayer 
    @include_once('includes/datalayer.class.php');
    // Make sure the DataLayer class loaded sucessfully
    if (!class_exists('DataLayer')) die("ERROR: Unable to load DataLayer class.\n");
    $dl = new DataLayer($config['datalayer']);
    if ($dl === false) die("ERROR: Unable to connect to database.\n");
    
    // Prune out dead servers
    $dl->Servers_Delete_ByAge(NOW-$config['maximumServerAge']);
    
    // Check if they are requesting a token
    if (isset($values['username']) && isset($values['password']))
    {
      // Pull the player's information from the database
      $data['player'] = $dl->Player_Fetch_ByUsername($values['username']);
      
      // Verify that the user exists and that their password is correct
      if (!$data['player'] || md5($values['password']) != $data['player']['password'])
        // If not, they do not get a token. Inform the client.
        die("NOTOK: Invalid username or password.\n");
      else
      {
        // We're done with the password, so get rid of it
        unset($values['password']);
        
        // If there is still enough time left before the token expires, give it
        // back to them.  Verify this is a request from the same IP.
        if ($_SERVER['REMOTE_ADDR'] == $data['player']['tokenipaddress'] && !empty($data['player']['token']) && $data['player']['tokendate'] + ($config['token']['lifetime']-$config['token']['regenerationGracePeriod']) > NOW)
        {
          echo "TOKEN: ".$data['player']['token']."\n";
        }
        // If not, generate a new one for them
        else
        {
          // Generate a token.
          $values['token'] = generate_random_string($config['token']['length']); 
          $values['tokendate'] = NOW;
          $values['tokenipaddress'] = $_SERVER['REMOTE_ADDR'];
          
          if ($dl->Player_Update_ByUsername($values['username'], $values))
            echo "TOKEN: ".$values['token']."\n";
          else
            die("NOTOK: There was an error during token generation.\n");
        }
      }
    }
    
    // Are we filtering just for a specific version?
    if (isset($values['version']) && !empty($values['version']))
      $data['servers'] = $dl->Servers_Fetch_ByVersion($values['version']);
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
  
  
  //////////////////////////////////////////
  // GETTOKEN
  //////////////////////////////////////////
  else if ($input['action'] == 'GETTOKEN')
  {
    // Verify that the username is valid
    if (!isset($input['username']) || !valid_username($input['username']))
    {
      die("ERROR: A valid username was not specified. Usernames must use only ".
          "alphanumeric characters, hyphens, underscores, or periods. Must be ".
          "2 to 25 characters long.\n");
    }
    
    if (!isset($input['password']) || !valid_password($input['password']))
    {
      die("ERROR: Password must be between 4 and 30 characters long.\n");
    }
    
    // Copy the input over
    $values = $input;
    
    // Load the DataLayer 
    @include_once('includes/datalayer.class.php');
    // Make sure the DataLayer class loaded sucessfully
    if (!class_exists('DataLayer')) die("ERROR: Unable to load DataLayer class.\n");
    $dl = new DataLayer($config['datalayer']);
    if ($dl === false) die("ERROR: Unable to connect to database.\n");
    
    
    // Pull the player's information from the database
    $data['player'] = $dl->Player_Fetch_ByUsername($values['username']);
    
    // Verify that the user exists and that their password is correct
    if (!$data['player'] || md5($values['password']) != $data['player']['password'])
      // If not, they do not get a token. Inform the client.
      die("NOTOK: Invalid username or password.\n");
    else
    {
      // We're done with the password, so get rid of it
      unset($values['password']);
      
      // If there is still enough time left before the token expires, give it
      // back to them.  Verify this is a request from the same IP.
      if ($_SERVER['REMOTE_ADDR'] == $data['player']['tokenipaddress'] && $data['player']['tokendate'] + ($config['token']['lifetime']-$config['token']['regenerationGracePeriod']) > NOW)
      {
        echo "TOKEN: ".$data['player']['token']."\n";
      }
      // If not, generate a new one for them
      else
      {
        // Generate a token.
        $values['token'] = generate_random_string($config['token']['length']); 
        $values['tokendate'] = NOW;
        $values['tokenipaddress'] = $_SERVER['REMOTE_ADDR'];
        
        if ($dl->Player_Update_ByUsername($values['username']))
          echo "TOKEN: ".$values['token']."\n";
        else
          die("NOTOK: There was an error during token generation.\n");
      }
    }
    
  }
  
  
  //////////////////////////////////////////
  // CHECKTOKENS
  //////////////////////////////////////////
  else if ($input['action'] == 'CHECKTOKENS')
  {
    if (!isset($input['checktokens']))
    {
      die("ERROR: No tokens specified.");
    }

    
    $values = Array();
      
    // We can check multiple tokens at once. Each callsign/ip/token is seperated
    // by a CR/LF (%0D%0A)
    $values['checktokens'] = explode("\r\n", $input['checktokens']);
    
    // Initialize groups to any empty array
    $values['groups'] = Array();
    
    // We can check multiple groups at once as well. Each group is seperated
    // by a CR/LF (%0D%0A)
    if (!empty($input['groups']))
    {
      $values['groups'] = explode("\r\n", $input['groups']);
    }

    // Make sure we have an array before we iterate through it.  It should have
    // at least one item if we were passed any data.
    if (is_array($values['checktokens']))
    {
      // Load the DataLayer 
      @include_once('includes/datalayer.class.php');
      // Make sure the DataLayer class loaded sucessfully
      if (!class_exists('DataLayer')) die("ERROR: Unable to load DataLayer class.\n");
      $dl = new DataLayer($config['datalayer']);
      if ($dl === false) die("ERROR: Unable to connect to database.\n");
      
      // Use the variable $c as our counter
      for ($c = 0; $c < count($values['checktokens']); $c++)
      {
        // If this is blank, continue on to the next
        if (strlen(trim($values['checktokens'][$c])) == 0)
          continue;
          
        // Focus on one item at a time
        $item = Array();
        
        // If the line has an @, that means an IP address was specified.  This
        // will be checked with the token IP to prevent a token from being used
        // be another user
        if (strpos($values['checktokens'][$c], "@") !== FALSE)
        {
          // First explode by the @ symbol, to pull the username off
          list($item['username'], $item['rest']) = explode('@', $values['checktokens'][$c]);
          // Next, explode the rest by = to seperate the ip address and token
          list($item['ipaddress'], $item['token']) = explode('=', $item['rest']);
          // We are done with this variable
          unset($item['rest']);
        }
        // Else, no IP address was specified
        else
        {
          // Explode by = to seperate the username and token
          list($item['username'], $item['token']) = explode('=', $values['checktokens'][$c]);
        }
        
        // Start the response to the server
        echo "MSG: checktoken callsign=".$item['username'].", ip=".$item['ipaddress'].", token=".$item['token'];
        
        // Loop through the groups that we were checking for and display them
        for ($g = 0; $g < count($values['groups']); $g++)
        {
          // If this is blank, continue on to the next
          if (strlen(trim($values['groups'][$g])) == 0)
            continue;
            
          echo " group=".$values['groups'][$g];
        }
        // Add the usual line feed
        echo "\n";
        
        $data['player'] = $dl->Player_Fetch_ByUsername($item['username']);
        
        
        // We will respond with a TOKBAD if any of the following are met:
        // 1) If the user does not exist
        // 2) The user does not have a token
        // 3) If the token doesn't match
        // 4) The user's token has expired
        // 5) If the IP was specified, and the IP doesn't match 
        if (
            // Condition 1
            $data['player'] === false ||
            // Condition 2
            empty($data['player']['token']) ||
            // Condition 3
            $item['token'] != $data['player']['token'] ||
            // Condition 4
            $data['player']['tokendate'] + $config['token']['lifetime'] < NOW ||
            // Condition 5
            (!empty($item['ipaddress']) && $item['ipaddress'] != $data['player']['tokenipaddress'])
        )
        {
          echo "TOKBAD: ".$item['username']."\n";
        }
        // Else, their token is good. Send back a TOKGOOD
        else
        {
          // We will be using the token. Remove it from their account.
          if ($dl->Player_Update_ByUsername($data['player']['username'], Array('token' => '')))
          {
            // TODO: Enumerate the groups they are a member of (of the ones
            // requested) in the form:   :Group.Name
            // Ex: TOKGOOD: SomeUser:Group1.Name:Group2.Name
            echo "TOKGOOD: ".$item['username']."\n";
            // Write out their BZID as well
            echo "BZID: ".$data['player']['playerid']." ".$data['player']['username']."\n";
          }
          // Else, we were unable to remove the token. To prevent a security
          // issue, we won't allow the token to be used.
          else
          {
            echo "TOKBAD: ".$item['username']."\n";
          }
        }

      } // for ($c = 0; $c < count($values['checktokens']); $c++)
    } // if (is_array($values['checktokens']))
  } // else if ($input['action'] == 'CHECKTOKENS')
  
  
  //////////////////////////////////////////
  // REGISTER
  //////////////////////////////////////////
  else if ($input['action'] == 'REGISTER')
  {
    if (!isset($input['username']) || !valid_username($input['username']))
    {
      die("ERROR: A valid username was not specified. Usernames must use only ".
          "alphanumeric characters, hyphens, underscores, or periods. Must be ".
          "2 to 25 characters long.\n");
    }

    // TODO: Allow banning of email addresses with wildcard support
    if (!isset($input['email']) || !valid_email($input['email']))
    {
      die("ERROR: A valid email was not specified.\n");
    }
    
    if (!isset($input['password']) || !valid_password($input['password']))
    {
      die("ERROR: Password must be between 4 and 30 characters long.\n");
    }
    
    // Load the DataLayer 
    @include_once('includes/datalayer.class.php');
    // Make sure the DataLayer class loaded sucessfully
    if (!class_exists('DataLayer')) die("ERROR: Unable to load DataLayer class.\n");
    $dl = new DataLayer($config['datalayer']);
    if ($dl === false) die("ERROR: Unable to connect to database.\n");
    
    
    $values = $input;
    
    // Check if the user already exists before going further
    if ($dl->Player_Exists_ByUsername($values['username']))
    {
      die("ERROR: The username '".$values['username']."' already exists. Please choose another username.\n");
    }
    else
    {
      // Leave the 'email' and 'password' blank. These will be filled after
      // the user activates their account
      $values['email'] = '';
      $values['password'] = '';
      
      $values['created'] = $values['lastaccess'] = NOW;
      $values['createdipaddress'] = $values['lastaccessipaddress'] = $_SERVER['REMOTE_ADDR'];
      
      // When the account is activated, these will be moved to 'password' and
      // 'email', respectively.
      $values['newpassword'] = md5($values['password']);
      $values['newemail'] = $values['email'];
      $values['activationkey'] = generate_random_string($config['activation']['keyLength']);
      $values['activated'] = 0;
      $values['token'] = '';
      $values['tokendate'] = 0;
      
      // Insert the player into the database
      $data['insertid'] = $dl->Player_Insert($values);
      
      if ($data['insertid'] !== false)
      {
        // Generate the nice welcome email for the user.
        $email = 
'Welcome to the BZFlag community! Your new account has been created.
Please keep this email for your records. Your account information is as follows:

Username: '.$values['username'].'
Password: '.$input['password'].'

Before you begin, you need to activate your account. Please follow the link below:
<a href="http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?action=CONFIRM&username='.urlencode($values['username']).'&activationkey='.urlencode($values['activationkey']).'">http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?action=CONFIRM&username='.urlencode($values['username']).'&activationkey='.urlencode($values['activationkey']).'</a>

If the above link does not work, copy and paste the following URL into your address bar:
http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?action=CONFIRM&username='.urlencode($values['username']).'&activationkey='.urlencode($values['activationkey']).'


Thank you for registering.';

        // TODO: Actually send the email. For now, we just echo it out.
        echo $email;

      }
      else
      {
        die("ERROR: There was a problem during registration. Please contact an administrator.\n");
      }
    }
  }
  
  
  //////////////////////////////////////////
  // CONFIRM
  //////////////////////////////////////////
  else if ($input['action'] == 'CONFIRM')
  {
    // Verify that we have received a valid username
    if (!isset($input['username']) || !valid_username($input['username']))
    {
      die("ERROR: A valid username was not specified. Usernames must use only ".
          "alphanumeric characters, hyphens, underscores, or periods. Must be ".
          "2 to 25 characters long.\n");
    }
    
    // Verify we have an activation key
    if (!isset($input['activationkey']) || strlen($input['activationkey']) != $config['activation']['keyLength'])
    {
      die("ERROR: Invalid activation key specified.\n");
    }
  
    // Load the DataLayer 
    @include_once('includes/datalayer.class.php');
    // Make sure the DataLayer class loaded sucessfully
    if (!class_exists('DataLayer')) die("ERROR: Unable to load DataLayer class.\n");
    $dl = new DataLayer($config['datalayer']);
    if ($dl === false) die("ERROR: Unable to connect to database.\n");
    
    $values = $input;
    
    $data['player'] = $dl->Player_Fetch_ByUsername($values['username']);
    
    if ($data['player'] !== false)
    {
      if ($input['activationkey'] == $data['player']['activationkey'])
      {
        if (isset($data['player']['newpassword']))
        {
          $data['player']['password'] = $data['player']['newpassword'];
          $data['player']['newpassword'] = '';
        }
        
        if (isset($data['player']['newemail']))
        {
          $data['player']['email'] = $data['player']['newemail'];
          $data['player']['newemail'] = '';
        }
        
        $data['player']['activationkey'] = '';
        $data['player']['activated'] = 1;
        
        $values['lastaccess'] = NOW;
        $values['lastaccessipaddress'] = $_SERVER['REMOTE_ADDR'];
        
        if ($dl->Player_Update_ByUsername($data['player']['username'], $data['player']))
        {
          die("NOTICE: Your account has been activated! Welcome to BZFlag!\n");
        }
        else
        {
          die("ERROR: There was an error activating your account. Please contact an administrator.\n");
        }
      }
      else
      {
        die("ERROR: The specified username and activation key combination was not found in our database.\n");
      }
    }
    else
    {
      die("ERROR: The specified username and activation key combination was not found in our database.\n");
    }
  }
  
?>
