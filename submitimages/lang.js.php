<?php
  // We don't want any errors corrupting the JavaScript
  @error_reporting(0);
  
  // Send out a proper Content-Type header
  header("Content-Type: text/javascript");
  
  // Pull in the configuration file.
  include('config.php');
  
  // Pull the requested language from 
  $input['lang'] = $_GET['lang'];
  // Verify that the value passed in was only made up of a to z, or a hyphen.
  // Also verify that the language specified actually exists. If either fails,
  // fall back to using the English language file.
  if (empty($input['lang']) || !ereg('a-z\-', $input['lang']) || !file_exists($config['paths']['language'].$input['lang'].".php"))
    include($config['paths']['language']."en.php");
  else
    include($config['paths']['language'].$input['lang'].".php");
  
  // Write out the JavaScript language file
  echo "var lang = new Array();\n";
  foreach ($lang as $key => $item)
    echo "lang['$key'] = \"".str_replace('"', '\"', $item)."\"\n";

?>
  
