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
    
    // FIXME: Should not call die or exit from within the DataLayer, since we
    // might want to show a graceful error message if this is reused for other
    // services
    
    if (!$this->link) die("ERROR: Unable to connect to database.");
    
    if (!mysql_select_db($database, $this->link)) die("ERROR: Unable to select database.");
    
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
    $sql .= "FROM_UNIXTIME('".mysql_real_escape_string($values['tokendate'], $this->link)."')";
    $sql .= ")";
    
    $result = mysql_query($sql);
    if ($result) return mysql_insert_id();
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
  
  function Servers_Fetch_All()
  {
    $sql = "SELECT serverid, name, port, ipaddress, title, owner, build, ".
           "version, gameinfo, UNIX_TIMESTAMP(lastmodified) FROM ".TBL_SERVERS;
           
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
           "version, gameinfo, UNIX_TIMESTAMP(lastmodified) FROM ".TBL_SERVERS." ".
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
