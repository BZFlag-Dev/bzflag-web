<?php
  // Set up some variables for this session
  $filedirectory = $_SERVER['DOCUMENT_ROOT'].$_SERVER['REQUEST_URI'];
  $httpdirectory = $_SERVER['REQUEST_URI'];
  
  function nicefilesize($filename)
  {
    // Start off with the size in bytes
    $size = filesize($filename);
    
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    foreach ($units as $unit) {
      if ($size >= 1024) $size = $size / 1024;
      else break;
    }
  
    return round($size, 2).$unit;
  }
  
  // Set up the SQLite database object and query the file data for this directory
  
  define('DIRECTORY_VERSION', 1);
  
  $filename = $filedirectory.'/data.sqlite3';
  
  $data = Array();
  $data['files'] = Array();
  
  if (file_exists($filename))
  {
  
    // create a SQLite3 database file with PDO and return a database handle (Object Oriented)
    try
    {
      $sqlite = new PDO('sqlite:'.$filename);
      
      // Verify that we don't need to upgrade the database file
      $query['checkVersion'] = $sqlite->query("SELECT value FROM info WHERE name = 'dbversion'");
      $data['checkVersion'] = $query['checkVersion']->fetch();
  
      // For now, only run further queries if the database version matches   
      if ($data['checkVersion']['value'] == DIRECTORY_VERSION)
      {
        $query['files'] = $sqlite->query("SELECT * FROM files");
        $data['files'] = $query['files']->fetchAll();
      }
    }
    catch( PDOException $exception )
    {
      //die($exception->getMessage());
    }
  }
  else if (file_exists($filedirectory.'/author.txt'))
  {

    // Try to create an SQLite3 database via the PDO functions
    try
    {
      $sqlite = new PDO('sqlite:'.$filename);
      // Create the info table, which will store information such as the
      // database version. (And that's all, so far)
      $sql = 'CREATE TABLE info(name TEXT PRIMARY KEY, value TEXT)';
      $sqlite->exec($sql);
      
      // Prepare a query that could be used multiple times to insert values
      // into the info table
      $query['infoInsert'] = $sqlite->prepare('INSERT INTO info (name, value) VALUES (:name, :value)');
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
      
      $sqlite->exec('CREATE TABLE files(filename TEXT PRIMARY KEY, bzid INTEGER, username TEXT, ipaddress TEXT, uploaderfirstname TEXT, uploaderlastname TEXT, uploaderemail TEXT, filemd5 TEXT, authorname TEXT, licensename TEXT, licenseurl TEXT, licensetext TEXT, moderationmessage TEXT, moderator TEXT)');
    }
    catch( PDOException $exception )
    {
      die($exception->getMessage());
    }

    echo "Upgrading old author.txt to SQLite system<br>";
    $oldauthorfile = file($filedirectory.'/author.txt');
    
    $olduploadername = false;
    $oldfiles = Array();
    
    foreach ($oldauthorfile as $oldline)
    {
      if (strlen(trim($oldline)) == 0) continue;

      if (strncmp("Licenses for ", $oldline, strlen("Licenses for ")) == 0)
      {
        $olduploadername = explode(" ", substr($oldline, strlen("Licenses for "), strpos($oldline, "'s images.")-strlen("Licenses for "))); 
      }
      
      else if (strncmp("License for ", $oldline, strlen("License for ")) == 0)
      {
        $oldfiles[] = Array('filename' => substr($oldline, strlen("License for "), strpos($oldline, ":")-strlen("License for ")),
                            'license' => substr($oldline, strpos($oldline, ":")+2) );
      }
    }
    
    if (!$olduploadername)
      die("This author.txt file was not in the correct format, and cannot be upgraded. Bailing.");
      
    if (sizeof($olduploadername) != 2)
      die("Uploader name was not valid. Bailing.");  
    
    echo "Uploader: ".implode(" ", $olduploadername)."<br><pre>";
    print_r($oldfiles);
    echo "</pre>";
    
    // Prepare a query to insert new entries into the 'files' table
    $query = $sqlite->prepare('INSERT INTO files (filename, uploaderfirstname, uploaderlastname, filemd5, licensename) VALUES (:filename, :uploaderfirstname, :uploaderlastname, :filemd5, :licensename)');

    $query->bindParam(':uploaderfirstname', $olduploadername[0], PDO::PARAM_STR);
    $query->bindParam(':uploaderlastname', $olduploadername[1], PDO::PARAM_STR);
    
    $null = "";
    
    foreach($oldfiles as $oldfile)
    {
      $query->bindParam(':filename', $oldfile['filename'], PDO::PARAM_STR);
      $query->bindParam(':licensename', $oldfile['license'], PDO::PARAM_STR);
      if (file_exists($filedirectory.'/'.$oldfile['filename']))
      {
        $oldfile['filemd5'] = md5_file($filedirectory.'/'.$oldfile['filename']);
        $query->bindParam(':filemd5', $oldfile['filemd5'], PDO::PARAM_STR);
      }
      else
        $query->bindParam(':filemd5', $null, PDO::PARAM_STR);
      $query->execute();
    }
    
    exit;
  }

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <title>images.bzflag.org - Index of <?php echo $httpdirectory; ?></title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <style type="text/css">
    body {
      background-color: white;
      color: black;
      font-family: monospace;
    }
    
    h1 {
      margin: 0 0 0.5em 0;
      padding: 0;
    }
    
    table {
      border-collapse: collapse;
    }
    
    td, th {
      border: 1px solid black;
      padding: 5px 10px;
    }
    
    th {
      background-color: #CCC;
    }
  </style>
</head>
<body>
  <h1>Index of <?php echo $httpdirectory; ?></h1>
    
  <table>
    <tr><th>Name</th><th>Size</th><th>Author</th><th>Uploader</th><th>License</th></tr>
<?php
  if ($httpdirectory != '/')
    echo "    <tr><td><a href=\"../\">Parent Directory</a></td><td>&lt;DIR&gt;</td><td>N/A</td><td>N/A</td><td>N/A</td></tr>\n";

  $items = Array('directories' => Array(), 'files' => Array());
  if (is_dir($filedirectory))
  {
    $dir = opendir($filedirectory);
    if ($dir)
    {
      while (($item = readdir($dir)) !== false)
      {
        if ($item == '.' || $item == '..')
          continue;
        
        // Only list directories or PNG files.
        if (is_dir($filedirectory.$item))
          $items['directories'][] = $item;
        else if (strtolower(end(explode('.', $item))) == 'png')
          $items['files'][] = $item;
      }
      closedir($dir);
    }
    
    sort($items['directories']);
    sort($items['files']);
    
    if (sizeof($items['directories']) + sizeof($items['files']) > 0)
    {
      foreach($items['directories'] as $item)
        echo "    <tr><td><a href=\"$httpdirectory$item/\">$item</a></td><td>&lt;DIR&gt;</td><td>N/A</td><td>N/A</td><td>N/A</td></tr>\n";
      foreach($items['files'] as $item)
      {
        $file = false;
        foreach($data['files'] as $f)
        {
          if ($f['filename'] == $item)
          {
            $file = $f;
          }
        }
        
        if ($file)
        {
          echo "    <tr><td><a href=\"$httpdirectory$item\">$item</a></td><td>".nicefilesize($filedirectory."/".$item)."</td><td>";
          if (strlen($file['authorname']) > 0)
            echo $file['authorname'];
          else
            echo "(Unknown)";
          
          echo "</td><td>";
          
          if (strlen($file['uploaderfirstname']) > 0 && strlen($file['uploaderlastname']) > 0)
            echo $file['uploaderfirstname']." ".$file['uploaderlastname'];
          else
            echo "(Unknown)";

          echo "</td><td>";
          
          if (strlen(trim($file['licenseurl'])) > 0)
            echo '<a href="'.$file['licenseurl'].'">'.$file['licensename']."</a>";
          else
            echo $file['licensename'];
          echo "</td></tr>\n";
        }
        else
          echo "    <tr><td><a href=\"$httpdirectory$item\">$item</a></td><td>".nicefilesize($filedirectory."/".$item)."</td><td>(Unknown)</td><td>(Unknown)</td><td>(Unknown)</td></tr>\n";
      }
    }
    else
      echo "    <tr><td colspan=\"5\">No files or folders exist in this directory</td></tr>\n";
  }

?>
    </table>
  </body>
</html>
