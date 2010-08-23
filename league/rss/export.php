<?php // $Id: export.php,v 1.3 2006/07/01 01:12:06 dennismp Exp $ vim:sts=2:et:sw=2
// Makes teams/players available for easy parsing (used for ladder currently)

require_once("phplib.php");
require_once('../lib/common.php');

  header("Content-type: text/plain\n\n");

  if(! empty($_GET['q']) ) {
    $clause = " AND name LIKE '%" .  mysql_real_escape_string($_GET['q']) . "%'";
  }
  else {
    $clause = '';
  }

  $sql =  "SELECT id, name, score FROM l_team WHERE status!='deleted' $clause";


  $res = mysql_query($sql);
  while($row = mysql_fetch_array($res))
  {
    print "TE: ". $row[0] . " " . stripslashes($row[1]) . "\n";
    $score[ $row[0] ] = $row[2];
  }

  $sql =  "SELECT team, callsign ".
      "FROM l_player ".
      "WHERE team != 0 ";

  $res = mysql_query($sql);
  while($row = mysql_fetch_array($res))
  {
    if( isset($score[$row[0]]) ) 
    	print "PL: ". $row[0] . " " . stripslashes($row[1]) . "\n";
  }

  $act = teamActivity(null, 45);
  $ids = join(',',array_keys($act)) ;

  $sql = "SELECT team1 as id,
	ifnull(sum(if(score1>score2,1,0)),0) win, 
	ifnull(sum(if(score1=score2,1,0)),0) draw,
	ifnull(sum(if(score1<score2,1,0)),0) loss
	from ". TBL_MATCH ." where team1 IN($ids) GROUP BY 1";
  $res = mysql_query($sql) or die(mysql_error());
  while($row = mysql_fetch_assoc($res)) 
    $stat1[$row['id']] = $row;

  $sql = "SELECT team2 as id,
	ifnull(sum(if(score2>score1,1,0)),0) win,
	    ifnull(sum(if(score2=score1,1,0)),0) draw,
	    ifnull(sum(if(score2<score1,1,0)),0) loss
	from ". TBL_MATCH ." where team2 IN($ids) GROUP BY 1";
  $res = mysql_query($sql) or die(mysql_error());
  while($row = mysql_fetch_assoc($res)) 
    $stat2[$row['id']] = $row;

  $win = $sta1->win + $sta2->win;
  $draw = $sta1->draw + $sta2->draw;
  $loss = $sta1->loss + $sta2->loss;

  foreach($act as $id => $n) {
    if( isset($score[$id]) ) 
      $win = $stat1[$id]['win'] + $stat2[$id]['win'];
      $loss = $stat1[$id]['loss'] + $stat2[$id]['loss'];
      $draw = $stat1[$id]['draw'] + $stat2[$id]['draw'];
      printf("TD: %d ar=%1.2f,score=%d,match=%d,%d,%d\n",$id, $n, $score[$id], $win, $draw, $loss);
  }
?>
