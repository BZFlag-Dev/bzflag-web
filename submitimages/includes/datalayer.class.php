<?php

  $prefix = "img_";
  define('TBL_QUEUE', $prefix.'queue');
  //define('TBL_', $prefix.'');

  // Few defines for moderation status
  define('STATUS_PENDING', 0);
  define('STATUS_APPROVED', 1);
  define('STATUS_REJECTED', 2);
  
class DataLayer
{
  var $link;

  function DataLayer($config)
  {
    $this->link = mysql_connect($config['hostname'], $config['username'], $config['password']);
    
    // Make sure the link is valid, and then try to select the database.
    // The ping verifies that the database server is responding.
    if (!$this->link || !mysql_select_db($config['database'], $this->link) || !mysql_ping($this->link))
      die("Unable to connect to the database");
  }
  
  
  //////////////////////////////////////////
  // Table 'queue'
  //////////////////////////////////////////
  
/*
CREATE TABLE `img_queue` (
  `queueid` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `bzid` MEDIUMINT UNSIGNED NOT NULL,
  `username` VARCHAR( 255 ) NOT NULL,
  `ipaddress` VARCHAR( 8 ) NOT NULL,
  `uploaderfirstname` VARCHAR( 255 ) NOT NULL,
  `uploaderlastname` VARCHAR( 255 ) NOT NULL,
  `filename` VARCHAR( 255 ) NOT NULL,
  `filemd5` VARCHAR( 32 ) NOT NULL,
  `authorname` VARCHAR( 255 ) NOT NULL,
  `licenseid` TINYINT UNSIGNED NOT NULL COMMENT 'If 255, it is custom',
  `licensename` VARCHAR( 255 ) NOT NULL,
  `licenseurl` VARCHAR( 255 ) NOT NULL,
  `licensebody` MEDIUMTEXT NOT NULL,
  `moderationstatus` TINYINT NOT NULL DEFAULT '0',
  INDEX ( `bzid` ),
  INDEX ( `filemd5` ),
  INDEX ( `moderationstatus` )
) TYPE = MYISAM ;
*/
  
  function Queue_Insert($values)
  {
    $sql = "INSERT INTO ".TBL_QUEUE." ";
    $sql .= "(queueid, bzid, username, ipaddress, uploaderfirstname, uploaderlastname, filename, filemd5, authorname, licenseid, licensename, licenseurl, licensebody, moderationstatus) ";
    $sql .= "VALUES (0, ";
    $sql .= "'".mysql_real_escape_string($values['bzid'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['username'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['ipaddress'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['uploaderfirstname'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['uploaderlastname'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['filename'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['filemd5'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['authorname'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['licenseid'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['licensename'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['licenseurl'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['licensebody'], $this->link)."', ";
    $sql .= "'".mysql_real_escape_string($values['moderationstatus'], $this->link)."'";
    $sql .= ")";
    
    $result = mysql_query($sql);
    if ($result) return mysql_insert_id();
    else return false;
  }
  
  function Queue_Fetch_All($onlyPending = true)
  {
    $sql = "SELECT * FROM ".TBL_QUEUE;
    if ($onlyPending) $sql .= " WHERE moderationstatus = ".STATUS_PENDING;
           
    $result = mysql_query($sql);
    
    if ($result && mysql_num_rows($result) >= 1)
    {
      $rows = Array();
      
      while ( ($row = mysql_fetch_assoc($result)) !== FALSE)
      {
        $rows[$row['queueid']] = $row;
      }
      
      return $rows;
    }
    else return false;
  }
  
  function Queue_Fetch_ByBZID($bzid, $onlyPending = true)
  {
    $sql = "SELECT * FROM ".TBL_QUEUE." WHERE ";
    $sql .= "bzid = '".mysql_real_escape_string($bzid, $this->link)."'";
    if ($onlyPending) $sql .= " AND moderationstatus = ".STATUS_PENDING;
           
    $result = mysql_query($sql);
    
    if ($result && mysql_num_rows($result) >= 1)
    {
      $rows = Array();
      
      while ( ($row = mysql_fetch_assoc($result)) !== FALSE)
      {
        $rows[$row['queueid']] = $row;
      }
      
      return $rows;
    }
    else return false;
  }
  
  function Queue_Fetch_ByID($queueid)
  {
    $sql = "SELECT * FROM ".TBL_QUEUE." WHERE ";
    $sql .= "queueid = '".mysql_real_escape_string($queueid, $this->link)."' ";
    $sql .= "LIMIT 1";
    
    $result = mysql_query($sql);
    
    if ($result && mysql_num_rows($result) == 1)
    {
      return mysql_fetch_assoc($result);
    }
    else return false;
  }
  
  function Queue_Update_ByID($queueid, $modified)
  {
    if (!is_array($modified) || sizeof($modified) < 1)
      return true;

    $sql = "UPDATE ".TBL_QUEUE." SET ";
    // Loop through the items we are updating
    foreach($modified as $field => $value)
      $sql .= $field."='".mysql_real_escape_string($value, $this->link)."', ";
    // Trim off the last ", " from the query
    $sql = substr($sql, 0, strlen($sql)-2);
    // Add on our WHERE statement
    $sql .= " WHERE queueid = '".mysql_real_escape_string($queueid, $this->link)."' ";
    // Limit this to affecting 1 item (shouldn't be necessary, but just in case)
    $sql .= "LIMIT 1";
    
    $result = mysql_query($sql);
    if ($result && mysql_affected_rows($this->link) == 1)
    {
      return true;
    }
    else return false;
  }

} // class DataLayer

?>
