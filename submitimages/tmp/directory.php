<?php
  // Set up some variables for this session
  $filedirectory = dirname($_SERVER['SCRIPT_FILENAME']);
  $httpdirectory = str_replace("\\", "/", dirname($_SERVER['SCRIPT_NAME']));
  
  if ($httpdirectory != '/')
    $httpdirectory .= '/';
  
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
  
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <title>images.bzflag.org - Index of <?php echo $httpdirectory; ?></title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <style type="text/css">
    body {
      background-color: black;
      color: black;
      font-family: monospace;
      margin: 0;
      padding: 0;
    }
    
    #main {
      margin: 25px;
      padding: 5px;
      background-color: #FFE666;
      color: black;
    }
    
    h1 {
      margin: 0 0 0.5em 0;
      padding: 0;
    }
    
    td, th {
      border: 1px solid black;
      padding: 5px 10px;
    }
  </style>
</head>
<body>
  <div id="main">
    <h1>Index of <?php echo $httpdirectory; ?></h1>
    
    <table>
      <tr><th>Name</th><th>Size</th><th>Author</th><th>Uploader</th><th>License</th></tr>
<?php
  if ($httpdirectory != '/')
    echo "      <tr><td><a href=\"../\">Parent Directory</a></td><td>&lt;DIR&gt;</td><td>N/A</td><td>N/A</td><td>N/A</td></tr>\n";

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
        if (is_dir($item))
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
        echo "      <tr><td><a href=\"$httpdirectory$item/\">$item</a></td><td>&lt;DIR&gt;</td><td>N/A</td><td>N/A</td><td>N/A</td></tr>\n";
      foreach($items['files'] as $item)
        echo "      <tr><td><a href=\"$httpdirectory$item\">$item</a></td><td>".nicefilesize($filedirectory."/".$item)."</td><td>(Unknown)</td><td>(Unknown)</td><td>(Unknown)</td></tr>\n";
    }
    else
      echo "      <tr><td colspan=\"5\">No files or folders exist in this directory</td></tr>\n";
  }

?>
      </table>
    </div>
  </body>
</html>
