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
        // Create or open the SQLite database
        $this->sql = new PDO('sqlite:'.$filename);

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
          
          if (file_exists(dirname($filename).'/author.txt'))
          {
            $olduploadername = false;
            $oldfiles = Array();
            
            foreach (file(dirname($filename).'/author.txt') as $oldline)
            {
              if (strlen(trim($oldline)) == 0) continue;
        
              if (strncmp("Licenses for ", $oldline, strlen("Licenses for ")) == 0)
              {
                $olduploadername = explode(" ", substr($oldline, strlen("Licenses for "), strpos($oldline, "'s images.")-strlen("Licenses for ")), 2); 
              }
              
              else if (strncmp("License for ", $oldline, strlen("License for ")) == 0)
              {
                $oldfiles[] = Array('filename' => substr($oldline, strlen("License for "), strpos($oldline, ":")-strlen("License for ")),
                                    'licensename' => trim(substr($oldline, strpos($oldline, ":")+2) ));
              }
            }
            
            // If we couldn't find the uploader's name, assume the file was not in the
            // correct format.  Otherwise, continue the conversion process.
            if ($olduploadername)
            {
            
              $data = Array();
              // Set up some data that will be common to all of these images
              $data['file']['uploaderfirstname'] = $olduploadername[0];
              $data['file']['uploaderlastname'] = $olduploadername[1];
              $data['file']['moderationmessage'] = "Imported from old system.";
              
              // The old system only had two built-in licenses: GPL and Public Domain.
              // Provide URLs here to insert into the new database for these licenses.
              $licenses['gpl'] = "http://opensource.org/licenses/gpl-2.0.php";
              $licenses['publicdomain'] = "http://creativecommons.org/licenses/publicdomain/";
            
              foreach($oldfiles as $oldfile)
              {
                $data['file']['filename'] = $oldfile['filename'];
                
                $data['file']['licensename'] = $oldfile['licensename'];
                
                if ($oldfile['license'] == "GPL")
                  $data['file']['licenseurl'] = $licenses['gpl'];
                else if ($oldfile['license'] == "Public Domain")
                  $data['file']['licenseurl'] = $licenses['publicdomain'];
                else
                  $data['file']['licenseurl'] = "";
                  
                if (file_exists(dirname($filename).'/'.$oldfile['filename']))
                  $data['file']['filemd5'] = md5_file(dirname($filename).'/'.$oldfile['filename']);
                else
                  $data['file']['filemd5'] = "";
                
                $this->File_Insert($data['file']);
              }
            }
          } // if (file_exists(dirname($filename).'/author.txt'))
        } // if (!$query['checkifnew'] || sizeof($query['checkifnew']->fetchAll()) == 0)
        
        // Else, if tables already exist, see if we need to upgrade (or bail, if
        // it's a newer database version)
        else
        {
          // Verify that we don't need to upgrade the database file
          $query['checkVersion'] = $this->sql->query("SELECT value FROM info WHERE name = 'dbversion'");
          $data['checkVersion'] = $query['checkVersion']->fetch();
          
          if ($data['checkVersion']['value'] < DIRECTORY_VERSION)
          {
            for ($version = $data['checkVersion']['value']+1; $version <= DIRECTORY_VERSION; $version++)
            {
              echo "Upgrading to version $version<br>";
            }
            
            exit;
            // TODO: Upgrade it
            return false;
          }
          else if ($data['checkVersion']['value'] > DIRECTORY_VERSION)
          {
            // The database version is newer than our script. We can't read this file.
            return false;
          }
        }
      }
      catch( PDOException $exception )
      {
        die("Encountered the following when trying to open ".$filename.": ".$exception->getMessage());
      }
    }
    
    function File_Insert($data)
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
    
    function Files_Fetch_All()
    {
      $query = $this->sql->query("SELECT * FROM files");
      if ($query)
        return $query->fetchAll(PDO::FETCH_ASSOC);
      else
        return false;
    }

    function File_Fetch_ByFilename($filename)
    {
      $query = $this->sql->query("SELECT * FROM files WHERE filename = ".$this->sql->quote($filename));
      if ($query)
        return $query->fetch(PDO::FETCH_ASSOC);
      else
        return false;
    }
  }
  
?>
