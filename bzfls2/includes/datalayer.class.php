<?php

  $prefix = "";
  define('TBL_SERVERS', $prefix.'servers');
  define('TBL_PLAYERS', $prefix.'players');
  define('TBL_GROUPS', $prefix.'groups');
  //define('TBL_', $prefix.'');

class DataLayer
{
  var $link;

  function DataLayer($hostname, $username, $password, $database)
  {
    $this->link = mysql_connect($hostname, $username, $password);
    
    // Make sure the link is valid, and then try to select the database
    if (!$this->link || !mysql_select_db($database, $this->link)) return false;
    
    // This verifies that the database server is responding, and returns true
    // if it is.
    return mysql_ping($this->link);
  }
  
  
  //////////////////////////////////////////
  // Table 'players'
  //////////////////////////////////////////
  
  function Player_Insert($values)
  {
    $sql = "INSERT INTO ".TBL_PLAYERS." ";
    $sql .= "(username, password, email, created, createdipaddress, lastaccess, lastaccessipaddress, newpassword, newemail, activationkey, activated, token, tokendate) ";
    $sql .= "VALUES (";
    $sql .= "'".mysql_real_escape_string($values['username'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['password'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['email'], $this->link)."', ";
    $sql .= "FROM_UNIXTIME('".mysql_real_escape_string($values['created'], $this->link)."'), ";
    $sql .= "'".mysql_real_escape_string($values['createdipaddress'], $this->link)."', ";
    $sql .= "FROM_UNIXTIME('".mysql_real_escape_string($values['lastaccess'], $this->link)."'), ";
    $sql .= "'".mysql_real_escape_string($values['lastaccessipaddress'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['newpassword'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['newemail'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['activationkey'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['activated'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['token'], $this->link)."', ";
    $sql .= "FROM_UNIXTIME('".mysql_real_escape_string($values['tokendate'], $this->link)."'), ";
    $sql .= "'".mysql_real_escape_string($values['tokenipaddress'], $this->link)."'";
    $sql .= ")";
    
    $result = mysql_query($sql);
    if ($result) return mysql_insert_id();
    else return false;
  }
  
  function Player_Update_ByUsername($values)
  {
    // We require the username to be passed in
    if (!array_key_exists('username', $values))
      return false;
  
    $sql = "UPDATE ".TBL_PLAYERS." SET ";

    
    if (array_key_exists('password', $values))
      $sql .= "password = '".mysql_real_escape_string($values['password'], $this->link)."', ";
    
    if (array_key_exists('email', $values))
      $sql .= "email = '".mysql_real_escape_string($values['email'], $this->link)."', ";
    
    if (array_key_exists('created', $values))
      $sql .= "created = FROM_UNIXTIME('".mysql_real_escape_string($values['created'], $this->link)."'), ";
    
    if (array_key_exists('createdipaddress', $values))
      $sql .= "createdipaddress = '".mysql_real_escape_string($values['createdipaddress'], $this->link)."', ";
    
    if (array_key_exists('lastaccess', $values))
      $sql .= "lastaccess = FROM_UNIXTIME('".mysql_real_escape_string($values['lastaccess'], $this->link)."'), ";
    
    if (array_key_exists('lastaccessipaddress', $values))
      $sql .= "lastaccessipaddress = '".mysql_real_escape_string($values['lastaccessipaddress'], $this->link)."', ";
    
    if (array_key_exists('newpassword', $values))
      $sql .= "newpassword = '".mysql_real_escape_string($values['newpassword'], $this->link)."', ";
    
    if (array_key_exists('newemail', $values))
      $sql .= "newemail = '".mysql_real_escape_string($values['newemail'], $this->link)."', ";
    
    if (array_key_exists('activationkey', $values))
      $sql .= "activationkey = '".mysql_real_escape_string($values['activationkey'], $this->link)."', ";
    
    if (array_key_exists('activated', $values))
      $sql .= "activated = '".mysql_real_escape_string($values['activated'], $this->link)."', ";
    
    if (array_key_exists('token', $values))
      $sql .= "token = '".mysql_real_escape_string($values['token'], $this->link)."', ";
    
    if (array_key_exists('tokendate', $values))
      $sql .= "tokendate = FROM_UNIXTIME('".mysql_real_escape_string($values['tokendate'], $this->link)."'), ";
      
    if (array_key_exists('tokenipaddress', $values))
      $sql .= "tokenipaddress = '".mysql_real_escape_string($values['tokenipaddress'], $this->link)."', ";
    
    // We already know we have the username
    $sql .= "username = '".mysql_real_escape_string($values['username'], $this->link)."' ";
    $sql .= "WHERE username = '".mysql_real_escape_string($values['username'], $this->link)."' ";
    $sql .= "LIMIT 1";
    
    $result = mysql_query($sql);
    
    if ($result && mysql_affected_rows() === 1) return true;
    else return false;
  }
  
  function Player_Fetch_ByUsername($values)
  {
    $sql = "SELECT username, password, email, UNIX_TIMESTAMP(created) as created, createdipaddress, UNIX_TIMESTAMP(lastaccess) as lastaccess, lastaccessipaddress, newpassword, newemail, activationkey, activated, token, UNIX_TIMESTAMP(tokendate) as tokendate, tokenipaddress FROM ".TBL_PLAYERS." WHERE ";
    $sql .= "username = '".mysql_real_escape_string($values['username'], $this->link)."' ";
    $sql .= "LIMIT 1";
    
    $result = mysql_query($sql);
    
    if ($result && mysql_num_rows($result) == 1)
    {
      return mysql_fetch_assoc($result);
    }
    else return false;
  }
  
  function Player_Exists_ByUsername($values)
  {
    $sql = "SELECT playerid FROM ".TBL_PLAYERS." WHERE ";
    $sql .= "username = '".mysql_real_escape_string($values['username'], $this->link)."' ";
    $sql .= "LIMIT 1";
    
    $result = mysql_query($sql);
    
    if ($result && mysql_num_rows($result) == 1) return true;
    else return false;
  }
  
  //////////////////////////////////////////
  // Table 'servers'
  //////////////////////////////////////////
  
  function Server_Insert($values)
  {
    $sql = "INSERT INTO ".TBL_SERVERS." ";
    $sql .= "(name, port, ipaddress, title, owner, build, version, gameinfo, lastmodified) ";
    $sql .= "VALUES (";
    $sql .= "'".mysql_real_escape_string($values['name'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['port'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['ipaddress'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['title'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['owner'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['build'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['version'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['gameinfo'], $this->link)."', ";
    $sql .= "FROM_UNIXTIME('".mysql_real_escape_string($values['lastmodified'], $this->link)."') ";
    $sql .= ")";
    
    $result = mysql_query($sql);
    if ($result) return mysql_insert_id();
    else return false;
  }
  
  function Server_Update_ByIPAddressPort($values)
  {
    $sql = "UPDATE ".TBL_SERVERS." SET ";
    $sql .= "name = '".mysql_real_escape_string($values['name'], $this->link)."', ";
    $sql .= "port = '".mysql_real_escape_string($values['port'], $this->link)."', ";
    $sql .= "ipaddress = '".mysql_real_escape_string($values['ipaddress'], $this->link)."', ";
    $sql .= "title = '".mysql_real_escape_string($values['title'], $this->link)."', ";
    $sql .= "owner = '".mysql_real_escape_string($values['owner'], $this->link)."', ";
    $sql .= "build = '".mysql_real_escape_string($values['build'], $this->link)."', ";
    $sql .= "version = '".mysql_real_escape_string($values['version'], $this->link)."', ";
    $sql .= "gameinfo = '".mysql_real_escape_string($values['gameinfo'], $this->link)."', ";
    $sql .= "lastmodified = FROM_UNIXTIME('".mysql_real_escape_string($values['lastmodified'], $this->link)."') ";
    $sql .= "WHERE ipaddress = '".mysql_real_escape_string($values['ipaddress'], $this->link)."' AND ";
    $sql .= "port = '".mysql_real_escape_string($values['port'], $this->link)."' LIMIT 1";
    
    $result = mysql_query($sql);
    
    if ($result && mysql_affected_rows() === 1) return true;
    else return false;
  }
  
  function Server_Delete_ByIPAddressPort($values)
  {
    $sql = "DELETE FROM ".TBL_SERVERS." ";
    $sql .= "WHERE ipaddress = '".mysql_real_escape_string($values['ipaddress'], $this->link)."' AND ";
    $sql .= "port = '".mysql_real_escape_string($values['port'], $this->link)."' LIMIT 1";
    
    $result = mysql_query($sql);
    
    if ($result && mysql_affected_rows() === 1) return true;
    else return false;
  }
  
  function Server_Fetch_ByIPAddressPort($values)
  {
    $sql = "SELECT * FROM ".TBL_SERVERS." WHERE ";
    $sql .= "ipaddress = '".mysql_real_escape_string($values['ipaddress'], $this->link)."' AND ";
    $sql .= "port = '".mysql_real_escape_string($values['port'], $this->link)."' ";
    $sql .= "LIMIT 1";
    
    $result = mysql_query($sql);
    
    if ($result && mysql_num_rows($result) == 1)
    {
      return mysql_fetch_assoc($result);
    }
    else return false;
  }
  
  function Server_Exists_ByIPAddressPort($values)
  {
    $sql = "SELECT * FROM ".TBL_SERVERS." WHERE ";
    $sql .= "ipaddress = '".mysql_real_escape_string($values['ipaddress'], $this->link)."' AND ";
    $sql .= "port = '".mysql_real_escape_string($values['port'], $this->link)."' ";
    $sql .= "LIMIT 1";
    
    $result = mysql_query($sql);
    
    if ($result && mysql_num_rows($result) == 1) return true;
    else return false;
  }
  
  function Servers_Fetch_All()
  {
    $sql = "SELECT serverid, name, port, ipaddress, title, owner, build, ".
           "version, gameinfo, UNIX_TIMESTAMP(lastmodified) as lastmodified FROM ".TBL_SERVERS;
           
    $result = mysql_query($sql);
    
    if ($result && mysql_num_rows($result) >= 1)
    {
      $rows = Array();
      
      while ( ($row = mysql_fetch_assoc($result)) !== FALSE)
      {
        $rows[$row['serverid']] = $row;
      }
      
      return $rows;
    }
    else return false;
  }
  
  function Servers_Fetch_ByVersion($version)
  {
    $sql = "SELECT serverid, name, port, ipaddress, title, owner, build, ".
           "version, gameinfo, UNIX_TIMESTAMP(lastmodified) as lastmodified FROM ".TBL_SERVERS." ".
           "WHERE version = '".mysql_real_escape_string($version)."'";
           
    $result = mysql_query($sql);
    
    if ($result && mysql_num_rows($result) >= 1)
    {
      $rows = Array();
      
      while ( ($row = mysql_fetch_assoc($result)) !== FALSE)
      {
        $rows[$row['serverid']] = $row;
      }
      
      return $rows;
    }
    else return false;
  }
  
  function Servers_Delete_ByAge($age)
  {
    
    $sql = "DELETE FROM ".TBL_SERVERS." ";
    $sql .= "WHERE lastmodified < FROM_UNIXTIME('".mysql_real_escape_string($age, $this->link)."')";
    
    $result = mysql_query($sql);
    
    if ($result) return true;
    else return false;
  
  
  }
  


}

?>
