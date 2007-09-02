<HTML><HEAD>
</head>
<BODY>

<?php
  $currentPath = dirname ($GLOBALS['HTTP_SERVER_VARS']['SCRIPT_FILENAME']);

// check for 'author.txt' file, and display it
  $author = @file ($currentPath .'/author.txt');
  if ($author){
    echo '<PRE>';
    foreach ($author as $textline)
      echo $textline;
    echo '</pre><HR>';
  }
  echo '<TABLE>';

// display link to all .png files ...
  $subDirs = array();
  if ($dirhand = opendir ($currentPath)){
    $fullURL = 'http://' . $GLOBALS['HTTP_SERVER_VARS']['HTTP_HOST'] . $GLOBALS['HTTP_SERVER_VARS']['REQUEST_URI'];
    while (($filename = readdir ($dirhand)) !== false)
      if (is_dir ($currentpath.$filename))
        $subDirs[] = $filename;
      else if (stristr ($filename, '.png') !== false)
        echo "<TR><TD>$fullURL$filename&nbsp;&nbsp;</td><TD><A HREF='$filename'>view<a></td></tr>"; 
    closedir();
  }  
  echo '</table>';
  
// display links to any sub-dirs ...  
  if ($subDirs){
    echo '<HR>Navigate:<BR>';
    foreach ($subDirs as $sub)
      if ($sub == '..')
        echo '<LI><A HREF="..">(up)</a><BR>';
      else if ($sub != '.')
        echo "<LI><A HREF='$sub'>$sub</a><BR>";
  }
?>

</body></html>



