<?php // $Id: dispchangelog.php,v 1.3 2005/03/24 13:27:14 dennismp Exp $ vim:sts=2:et:sw=2

function section_dispchangelog(){

  if (!isFuncAllowed ('disp_changelog'))
    return errorPage ('You are not allowed to do this!');   

  echo '<BR><TABLE align=center cellpadding=5 border=1><TR><TD><font size=+1><b>Currently running version: '. CODE_VERSION 
      .'</b></font></td></tr></table><p>'  ;

  $first = true;
  $txt = file ('support/ChangeLog.txt');
  foreach ($txt as $line){
    $line = trim($line);
    if ($line{0} == '[' && $line{strlen($line)-1}==']'){
      if (!$first)
        echo '</ul><HR>';
      $first = false;
      echo '<font size=+1><B>'. substr ($line, 1, strlen($line)-2) .':</b></font><UL>';
    } else if (strncmp ($line, '*)', 2)==0){
      echo '<LI>' . substr ($line, 3);
    } else {
      echo ' '. $line .'<BR>';
    }
  }
  echo '</ul>';
}

function section_dispchangelog_permissions() {
  return array(
    'disp_changelog' => 'Display Changelog'
  );
}
?>
