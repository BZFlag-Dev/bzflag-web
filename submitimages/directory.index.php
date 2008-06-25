<?php

  define('USING_SMARTY', true);

  require('common.php');
  
  // Set up some variables for this session
  $data['filedirectory'] = $_SERVER['DOCUMENT_ROOT'].rtrim(urldecode($_SERVER['REQUEST_URI']), '/')."/";
  $data['httpdirectory'] = $_SERVER['REQUEST_URI'];
  // Not even sure if Apache would handle a request that included .. in it, but
  // better safe than sorry.
  if (strstr('..', $_SERVER['REQUEST_URI'])) exit;
  
  //filesize($filename)
  
  require_once($config['paths']['includes'].'directorydb.class.php');
    
  $dirdb = new DirectoryDB($data['filedirectory'].'data.sqlite3');

  //$data['fileInfo'] = $dirdb->Files_Fetch_All();
  
  
  if (is_dir($data['filedirectory']))
  {
    $dir = opendir($data['filedirectory']);
    if ($dir)
    {
      $data['directories'] = Array();
      $data['files'] = Array();
      
      while (($item = readdir($dir)) !== false)
      {
        // Ignore items starting with a period. This includes the '.' and '..'
        // entries.
        if (substr($item, 0, 1) == '.')
          continue;
        
        // Only list directories or PNG files. Do not list a directory if it
        // contains an .ignore file.
        if (is_dir($data['filedirectory'].$item))
        {
          if (file_exists($data['filedirectory']."/$item/.ignore"))
            continue;
          
          $data['directories'][] = $item;
        }
        else if (strtolower(end(explode('.', $item))) == 'png')
        {
          $data['fileInfo'] = $dirdb->File_Fetch_ByFilename($item);
          if ($data['fileInfo'] && is_array($data['fileInfo']))
          {
            $data['fileInfo']['filename'] = $item;
            $data['fileInfo']['filesize'] = nicefilesize(filesize($data['filedirectory']."/".$item));
            $data['files'][] = $data['fileInfo'];
          }
          else
            $data['files'][] = Array('filename' => $item, 'filesize' => nicefilesize(filesize($data['filedirectory']."/".$item)));
        }
          
      }
      closedir($dir);
      unset($data['fileInfo']);
      
      // Sort the directories and files alphabetically
      sort($data['directories']);
      function sortFiles($a, $b)
      {
        return strcmp($a['filename'], $b['filename']);
      }
      usort($data['files'], 'sortFiles');
      
      //header("Content-Type: text/plain");
      //print_r($data);
      $tpl->display('directory.tpl');
    }
  }

?>
