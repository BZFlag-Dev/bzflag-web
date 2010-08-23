<?php // $Id: sql.php,v 1.10 2006/08/29 00:29:49 dennismp Exp $ vim:sts=2:et:sw=2

define (TBL_MATCH,  'bzl_match');
define (TBL_FREEZE, 'bzl_freezeranks');
define (TBL_VISITS, 'bzl_visit');
define (TBL_LINKS,  'bzl_links');
define (TBL_NEWS,   'bzl_news');

define (TBL_TEAM,   'l_team');
define (TBL_PLAYER, 'l_player');
define (TBL_SESSION, 'l_session');

define (MYSQL_PERSISTENT, false);


function sqlQueryMsg ($q, &$msg){
  $res = @mysql_query ($q);

  if (!$res){
    $msg = '<Table width=100%><TR><TD><pre>'. mysql_error() ."\n\n$q</code></td></tr></pre>";
//    sqlQueryDisplay($q);
//    sqlQueryDisplay (mysql_error() );
//    $q = str_replace (">", "&gt", $q);
//    $q = str_replace ("<", "&lt", $q);
//    echo "<Table width=100%><TR><TD><pre>$q</code></td></tr></pre>";

  }
  return $res;
}




function sqlQueryDisplay ($q){
  $q = str_replace (">", "&gt", $q);
  $q = str_replace ("<", "&lt", $q);
  echo "<Table width=100%><TR><TD><pre>$q</code></td></tr></pre>";
}


function sqlQuery ($q, $disp=null){
  $res = mysql_query ($q);
  if (!$res || $disp){
    sqlQueryDisplay($q);
    sqlQueryDisplay (mysql_error() );
  }
  return $res;
}



function sqlQuerySingle ($q, $disp=null){
  $res = sqlQuery ($q, $disp);
  if (mysql_num_rows($res) == 0)  
    return null;
  return mysql_fetch_object ($res);
}

function sqlQueryExists($q, $disp=null){
  $res = sqlQuerySingle($q, $disp);
  return $res?true:false;
}

$databaseName = "";

function sqlConnect ($confLoc){
  global $databaseName;
  $nv = array();
  simpleNVparser ($confLoc, &$nv);
  $databaseName = $nv['SQLdatabase'];
  if (MYSQL_PERSISTENT === true)
    $link=mysql_pconnect($nv['SQLhost'], $nv['SQLuser'], $nv['SQLpass']);
  else
    $link=mysql_connect($nv['SQLhost'], $nv['SQLuser'], $nv['SQLpass']);
  mysql_select_db($nv['SQLdatabase'], $link);
  if($link === false){
    echo "<BR><BR><HR>There seems to be a problem with the database, 
    please try again later.<BR>
    If the problem persists, please contact the site administrator.";
    exit;
  }
}



function queryGetTeamName ($id){
  $q = "SELECT name from ". TBL_TEAM ." WHERE id='$id'";
  $res = sqlQuery ($q);
  $row = mysql_fetch_object ($res);
  if ($row)
    return substr ($row->name, 0, 36);
  return "[UNKNOWN]";
}



function queryGetTeam ($id){
  $q = "SELECT * from ". TBL_TEAM ." WHERE id='$id'";
  $res = sqlQuery ($q);
  return mysql_fetch_object ($res);
}




function sqlResultDump ($res, $title=null){
  echo '<TABLE><TR>';
  $numCols = mysql_num_fields ($res);
  if ($title)
    echo '<TR><TD align=center colspan='. $numCols*2 ."><Font size=+1><B>$title</font></td></tr>\n";

  for ($x=0; $x<$numCols; $x++){
    $name = mysql_field_name ($res, $x);
    echo "<TD><b>$name</td><TD width=5></td>";
  }
  echo '</tr>';
  if ($res)
    while ($row = mysql_fetch_row($res)){
      echo '<TR>';
      for ($x=0; $x<$numCols; $x++)
        echo "<TD>$row[$x]</td><TD></td>";
      echo "</tr>\n";
    }
  else
    echo '<TR><TD align=center colspan='. numCols*2 .">Empty result set</td></tr>\n";

  echo '</table>';
}

?>
