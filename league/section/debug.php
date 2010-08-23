<?php // $Id: debug.php,v 1.5 2005/04/17 20:03:03 menotume Exp $ vim:sts=2:et:sw=2

$func = $_GET['func'];
if ($func=='genimage'){
  section_debug_generateImage();
  return;
}


function section_debug (){
  if (!isFuncAllowed('debug'))
    return errorPage ('not authorized');

  $func = $_GET['func'];

  echo '<TABLE><TR>'
    .'<TD>'.htmlURLbutSmall ('flags', 'debug', 'func=flags')
    .'</td><TD>'.htmlURLbutSmall ('sessions', 'debug', 'func=sessions')
    .'</td><TD>'.htmlURLbutSmall ('sqlstats', 'debug', 'func=sqlstats')
    .'</td><TD>'.htmlURLbutSmall ('libraries', 'debug', 'func=libs')
    .'</td><TD>'.htmlURLbutSmall ('image', 'debug', 'func=imgtest');
    if (DEBUG_LEVEL > 0)
      echo '</td><TD>'.htmlURLbutSmall ('globals', 'debug', 'func=globals');
    if (DEBUG_LEVEL > 1)
      echo '</td><TD>'.htmlURLbutSmall ('phpinfo', 'debug', 'func=phpinfo');
    echo '</td></tr></table>';

  if ($func == 'globals')
    section_debug_doGlobals();
  else if ($func == 'flags')
    section_debug_doFlags();
  else if ($func == 'libs')
    section_debug_doLibs();
  else if ($func == 'sqlstats')
    section_debug_doSqlStats();
  else if ($func == 'phpinfo')
    section_debug_doPhpInfo();
  else if ($func == 'imgtest')
    section_debug_doImageTest();
  else if ($func == 'sessions')
    section_debug_doSessionList();
}

function section_debug_permissions() {
  return array(
    'debug'     => 'Required, if user wants to access debug pages',
    'sql_stats' => 'View SQL Table statistics' 
  );
}


function section_debug_doSqlStats(){
  global $databaseName;
  
  if(!isFuncAllowed('sql_stats'))
    return errorPage ('You are not authorized for this function.');
  $tot = 0;
  
  $ver = mysql_fetch_object(sqlQuery("select text from bzl_siteconfig where name='tablever'"));
  echo '<BR><TABLE align=center><TR><TD align=right>CODE Version:&nbsp;&nbsp</td><TD>' .CODE_VERSION. '</td></tr>
        <TR><TD align=right>SQL Tables:&nbsp;&nbsp</td><TD>' .$ver->text. '</td></tr>
        <TR><TD align=right>Database Name:&nbsp;&nbsp</td><TD>' .$databaseName. '</td></tr></table>';

  echo '<BR><CENTER><div class=conttitle>SQL TABLE STATISTICS:</div><BR>';

  echo '<TABLE border=0 cellspacing=0><TR class=tabhead valign=bottom>
    <TD align=right>Table name</td><TD align=right>Data<BR>size</td>
    <TD align=right>Index<BR>size</td><TD align=right>Total<BR>size</td>
    <TD align=right>#<BR>rows</td></tr>';
  $res = sqlQuery ('Show table status');
  while ( $tab = mysql_fetch_object($res)){
    $both = $tab->Data_length + $tab->Index_length;
    $tot += $both;
    echo "<TR><TD>$tab->Name</td>
        <TD align=right>&nbsp;$tab->Data_length</td>
        <TD align=right>&nbsp;$tab->Index_length</td>
        <TD align=right>&nbsp;$both</td>
        <TD align=right>&nbsp;$tab->Rows</td></tr>";
  }
  
  echo '<TR><TD colspan=5 align=center><BR>TOTAL USAGE: &nbsp;'
  . number_format ($tot) .' bytes</td></tr></table>';
  
}




function section_debug_strSecs ($unixSecs){
  if ($unixSecs < 0){
    $sign = "-";
    $unixSecs = 0 - $unixSecs;
  } else
    $sign = ' ';
  return sprintf ('%s%0d:%02d:%02d', $sign, $unixSecs/3600, ($unixSecs%3600)/60, $unixSecs%60);
}


function section_debug_doSessionList (){

  $res = sqlQuery ('select UNIX_TIMESTAMP(expire) as expire, ip, 
      UNIX_TIMESTAMP(expire)-UNIX_TIMESTAMP() as idle, 
      callsign from ' .TBL_SESSION. ' ORDER BY playerid,expire');
  echo '<BR><TABLE><TR><TD align=center colspan=7><font size=+1><b>ALL SESSIONS:</b></font></td></tr>
      <TR align=center><TD>Callsign</td><TD width=10></td><TD>session expire</td><TD width=10></td>
      <TD>Idle</td><TD width=5></td><TD>IP</td></tr><TR><td colspan=7><HR></td></tr>';
  while ($row = mysql_fetch_object($res)){
    $exp = date ('Y-M-d H:i:s', $row->expire);
    $idle = section_debug_strSecs(SESSION_LIFETIME - $row->idle);
    echo "<TR><TD>$row->callsign</td><TD></td><TD>$exp</td><TD></td>
                <TD>$idle</td><TD></td><TD>$row->ip</td><TR>";
  }
  echo '</table>';
}




function section_debug_doGlobals (){
  htmlPrint_r ($GLOBALS);
}



function section_debug__listFlags ($set){
  echo '<TABLE cellpadding=1 cellspacing=0>';
  while ($row=mysql_fetch_object($set)){
    echo "<TR><TD>$row->iso</td><TD width=10></td><TD>$row->numcode</td><TD width=10></td><TD>$row->name</td>
        <TD width=10></td><TD>$row->flagname</td>";
    if ( $row->flagname  )
      echo '<TD width=5></td><TD><img border=1 width=30 height=18 src="'. FLAG_DIR 
          ."cs-$row->flagname.gif\"></td>";

    echo '</tr>';
  }
  echo '</table>';
}



function section_debug_doFlags (){
  $set = sqlQuery ("SELECT callsign, id, country, bzl_countries.name as countryname, bzl_countries.iso from 
      l_player, bzl_countries
      where l_player.country = bzl_countries.numcode
      and bzl_countries.flagname is null order by bzl_countries.name"); 
  if (mysql_num_rows($set) > 0){
    echo '<BR><font size=+1>Players without flags:</font><TABLE>';
    while ( $row = mysql_fetch_object ($set) ){
      echo '<TR><TD width=20></td><TD>' .playerLink ($row->id, $row->callsign) 
          ."</td><TD width=10></td><TD>$row->iso
          </td><TD width=10></td><TD>$row->countryname</td><TD>&nbsp;($row->country)</td>";
    }
    echo '</table>';
  }

  echo '<HR>';
  $set = sqlQuery ("SELECT numcode, name, flagname, iso FROM bzl_countries 
        WHERE flagname is not NULL order by name");
  section_debug__listFlags ($set);
  echo '<HR>';
  $set = sqlQuery ("SELECT numcode, name, flagname, iso FROM bzl_countries 
        WHERE flagname is NULL order by name");
  section_debug__listFlags ($set);
}


function section_debug_doLibs (){
  echo '<BR>GD Library (images):<BR>';
  if (!function_exists('gd_info')){
    echo "[none]<BR>";
  } else {
    htmlPrint_r (gd_info());
  }
}


function section_debug_doPhpInfo (){
  echo '<BR>Php Info:<BR><TABLE align=left><TR align=left><TD aligh=left>';
  phpinfo();
  echo '</td></tr></table>';
}


function section_debug_doImageTest (){
  echo '<BR>Generated Image: <BR>
      <font size=+1>League Activity: Matches played per week (random data)<BR>';
  echo '<IMG SRC="section/debug.php?func=genimage">';
}





function section_debug_testData (){
  $a = array();
  $dt = gmmktime ( 0,0,0,6,1,2002 );
  $lastdate = time();
  while ($dt < $lastdate){
    $a[] = array ($dt, rand (2,10));
    $dt += (7*24*60*60);
  }
  return $a;
}



function section_debug_generateImage (){
  $dat = section_debug_testData();
  $w = 600;
  $h = 250;
  $axX = $h-40;
  $axY = 20;
  $marginTop = 20;  
  $marginRight = 10;  
  
  // create a 100*30 image
  $im = imagecreate($w, $h);
  // white background and blue text
  $bg = imagecolorallocate($im, 202, 202, 202);
  $textcolor = imagecolorallocate($im, 0, 0, 102);
  $gridcolor = imagecolorallocate($im, 0,0,0);
  $barcolor =  imagecolorallocate($im, 255,0,0);
  // write the string at the top left
//  imagestring($im, 2, 0, 0, "Hello world!", $textcolor);


  $numdat = count($dat);

  // find maxvalue
  for ($i=0; $i<$numdat; $i++){
    if ($dat[$i][1] >= $maxVal)
      $maxVal = $dat[$i][1];
  }

  $useW = $w - ($axY + $marginRight);
  $useH = $axX - $marginTop;
  $barW = $useW / $numdat;
  $barInc = (int)($useH / $maxVal);
    

imagestring($im, 2, 100, 0, "BW: $barW", $textcolor);
imagestring($im, 2, 100, 12, "MV: $maxVal", $textcolor);

  // axis ...
  imageline ($im, $axY, $axX+1, $w-$marginRight, $axX+1, $gridcolor);
  imageline ($im, $axY-1, $marginTop, $axY-1, $axX, $gridcolor);

  $x=$axY; 
  // draw bars ...
  for ($i=0; $i<$numdat; $i++){
    $h = $dat[$i][1] * $barInc; 
    imagefilledrectangle ($im, $x, $axX-$h, $x+(int)$barW, $axX, $barcolor ); 
    $x += $barW;  
  }
 
  // output the image
  header("Content-type: image/jpg");
  imagejpeg($im, '', 90);
}


?>
