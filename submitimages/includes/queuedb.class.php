<?php

  class QueueDB
  {
    var $sql;
        
    function QueueDB($filename)
    {
      
      // Try to create an SQLite3 database via the PDO functions
      try
      {
        $this->sql = new PDO('sqlite:'.$filename);
        $this->Init();
      }
      catch( PDOException $exception )
      {
        //die($exception->getMessage());
      }
    }
    
    function Init()
    {     
      // See if there are any tables already in the database.
      $query['checkifnew'] = $this->sql->query("SELECT name FROM sqlite_master WHERE type = 'table'");
    
      // If there are no tables, create them.
      if (!$query['checkifnew'] || sizeof($query['checkifnew']->fetchAll(PDO::FETCH_ASSOC)) == 0)
      {
        // Create the queue table. This will store information about the files
        // contained in a single directory. The filenames will be relative to allow
        // us to rename directories.
        
        $this->sql->exec('CREATE TABLE queue(queueid INTEGER PRIMARY KEY AUTOINCREMENT, filename TEXT, bzid INTEGER, username TEXT, ipaddress TEXT, uploaderfirstname TEXT, uploaderlastname TEXT, uploaderemail TEXT, filemd5 TEXT, authorname TEXT, licensename TEXT, licenseurl TEXT, licensetext TEXT)');
        
        // TODO: Do some error checking to make sure everything worked
        return true;
      }
      
      return true;
    }
    
    

    function Queue_Insert($values)
    {
      // Prepare a query to insert new entries into the 'queue' table
      $query = $this->sql->prepare("INSERT INTO queue (bzid, username, ipaddress, uploaderfirstname, uploaderlastname, uploaderemail, filename, filemd5, authorname, licensename, licenseurl, licensetext) VALUES (:bzid, :username, :ipaddress, :uploaderfirstname, :uploaderlastname, :uploaderemail, :filename, :filemd5, :authorname, :licensename, :licenseurl, :licensetext)");
      // Run it and return the result
      return $query->execute($values);
    }
    
    function Queue_Fetch_All()
    {
      $query = $this->sql->query("SELECT * FROM queue");
      
      if ($query) {
        $rows = Array();
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
          $rows[$row['queueid']] = $row;
        }
        
        if (sizeof($rows) > 0) return $rows;
        else return false;
      }
      else return false;
    }
    
    function Queue_Fetch_ByBZID($bzid)
    {
      $query = $this->sql->prepare("SELECT * FROM queue WHERE bzid = :bzid");
      
      if ($query && $query->execute(Array('bzid' => $bzid))) {
        
        $rows = Array();
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
          $rows[$row['queueid']] = $row;
        }
        
        if (sizeof($rows) > 0) return $rows;
        else return false;
      }
      else return false;
    }
    
    function Queue_Fetch_ByID($queueid)
    {
      $query = $this->sql->prepare("SELECT * FROM queue WHERE queueid = :queueid LIMIT 1");
      
      if ($query && $query->execute(Array('queueid' => $queueid))) {
        return $query->fetch(PDO::FETCH_ASSOC);
      }
      else return false;
    }
    
    function Queue_Delete_ByID($queueid)
    {
      $query = $this->sql->prepare("DELETE FROM queue WHERE queueid = :queueid");
      
      if ($query && $query->execute(Array('queueid' => $queueid))) {
        if ($query->rowCount() == 1) return true;
        else return false;
      }
      else return false;
    }
  }
  
?>
