<?php

  define('DIRECTORY_VERSION', 1);

  class DirectoryDB
  {
    var $sql;
    
    function DirectoryDB($filename)
    {
      
      // Try to create an SQLite3 database via the PDO functions
      try
      {
        $this->sql = new PDO('sqlite:'.$filename);
        $this->Init();
      }
      catch( PDOException $exception )
      {
        die($exception->getMessage());
      }
    }
    
    function Init()
    {     
      // See if there are any tables already in the database.
      $query['checkifnew'] = $this->sql->query("SELECT name FROM sqlite_master WHERE type = 'table'");
    
      // If there are no tables, create them.
      if (!$query['checkifnew'] || sizeof($query['checkifnew']->fetchAll()) == 0)
      {
        // Create the info table, which will store information such as the
        // database version. (And that's all, so far)
        $sql = 'CREATE TABLE info(name TEXT PRIMARY KEY, value TEXT)';
        $this->sql->exec($sql);
        
        // Prepare a query that could be used multiple times to insert values
        // into the info table
        $query['infoInsert'] = $this->sql->prepare('INSERT INTO info (name, value) VALUES (:name, :value)');
        $info = Array('name' => false, 'value' => false);
        $query['infoInsert']->bindParam(':name', $info['name']);
        $query['infoInsert']->bindParam(':value', $info['value']);
        
        // Insert the database version
        $info['name'] = 'dbversion';
        $info['value'] = DIRECTORY_VERSION;
        $query['infoInsert']->execute();
      
        // Create the files table. This will store information about the files
        // contained in a single directory. The filenames will be relative to allow
        // us to rename directories.
        
        $this->sql->exec('CREATE TABLE files(filename TEXT PRIMARY KEY, bzid INTEGER, username TEXT, ipaddress TEXT, uploaderfirstname TEXT, uploaderlastname TEXT, uploaderemail TEXT, filemd5 TEXT, authorname TEXT, licensename TEXT, licenseurl TEXT, licensetext TEXT, moderationmessage TEXT, moderator TEXT)');
        
        // TODO: Do some error checking to make sure everything worked
        return true;
      }
      // Else, if tables already exist, see if we need to upgrade (or bail, if
      // it's a newer database version)
      else
      {
        // Verify that we don't need to upgrade the database file
        $query['checkVersion'] = $this->sql->query("SELECT value FROM info WHERE name = 'dbversion'");
        $data['checkVersion'] = $query['checkVersion']->fetch();
        
        if ($data['checkVersion']['value'] < DIRECTORY_VERSION)
        {
          // TODO: Upgrade it
          return false;
        }
        else if ($data['checkVersion']['value'] < DIRECTORY_VERSION)
        {
          // The database version is newer than our script. We can't read this file.
          return false;
        }
      }
      
      return true;
    }
    
    function AddEntry($data)
    {
      // Prepare a query to insert new entries into the 'files' table
      $query = $this->sql->prepare('INSERT INTO files (filename, bzid, username, ipaddress, uploaderfirstname, uploaderlastname, uploaderemail, filemd5, authorname, licensename, licenseurl, licensetext, moderationmessage, moderator) VALUES (:filename, :bzid, :username, :ipaddress, :uploaderfirstname, :uploaderlastname, :uploaderemail, :filemd5, :authorname, :licensename, :licenseurl, :licensetext, :moderationmessage, :moderator)');

      // They're all strings, except for bzid, so we can bind them in a nice
      // loop. Technically, we wouldn't have to bind all these, but do this for 
      // later when we'll add more than one at a time.
      foreach(array_keys($data) as $key)
        $query->bindParam(':'.$key, $data[$key], ($key != 'bzid')?(PDO::PARAM_STR):(PDO::PARAM_INT));
      
      // Run it and return the result
      return $query->execute();
    }
  }
  
?>
