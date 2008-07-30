<?php

  class EasyDB {
    var $link;
    
    function EasyDB($hostname, $username, $password, $database)
    {
      $this->link = mysql_connect($hostname, $username, $password);
      
      if (!$this->link)
        die("Could not connect to database");
      
      if (!mysql_select_db($database, $this->link))
        die("Could not select database");
    }
    
    function Escape($value)
    {
      return mysql_real_escape_string($value, $this->link);
    }
    
    function NumRows($sql)
    {
      $result = mysql_query($sql, $this->link);
      
      if ($result) return mysql_num_rows($result);
      else return 0;
    }
    
    function FetchAll($sql)
    {
      $result = mysql_query($sql, $this->link);
      
      $rows = Array();
      if ($result)
      {
        while ($row = mysql_fetch_assoc($result))
        {
          $rows[] = $row;
        }
      }
      
      //if (sizeof($rows) > 0)
        return $rows;
      //else
        //return false;
    }
    
    function FetchRow($sql)
    {
      $result = mysql_query($sql, $this->link);
      
      $row = false;
      if ($result)
      {
        $row = mysql_fetch_assoc($result);
        if (!$row) $row = false;
      }
      
      return $row;
    }
  }
?>
