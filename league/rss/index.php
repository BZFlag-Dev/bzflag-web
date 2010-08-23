<?php // $Id: index.php,v 1.5 2006/08/30 00:48:19 dennismp Exp $ vim:sts=2:et:sw=2

require_once("phplib.php");

header ('Content-type: text/xml');

$refreshed = gmdate ('Y-m-d\TH:i:s+00:00');
$toplink = 'http://my.bzflag.org/league';
$title = 'BZLeague';

$feed = $_GET['feed'];
$numMatches = $_GET['num'];
if (!isset ($feed))
  $feed = 'matches';
if (!isset ($numMatches))
  $numMatches = 5;
if ($numMatches < 1)
  $numMatches=1;
if ($numMatches > 20)
  $numMatches=20;



// make publish date = last match time
$row = mysql_fetch_object (sqlQuery('SELECT tsactual from ' .TBL_MATCH. ' order by tsactual desc limit 1'));
$pubDate = timestampRFC2822(strtotime($row->tsactual));

$res = sqlQuery("SELECT matchtab.id, tsactual, team1, l_team1.name name1, score1, team2, 
    l_team2.name name2, score2
  FROM " . TBL_MATCH . " matchtab
  left join l_team l_team1 on l_team1.id = team1
  left join l_team l_team2 on l_team2.id = team2
  ORDER BY tsactual desc limit $numMatches");


echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<rss version=\"2.0\"
  xmlns:dc=\"http://purl.org/dc/elements/1.1/\"
  xmlns:sy=\"http://purl.org/rss/1.0/modules/syndication/\"
  xmlns:bzl=\"$toplink/rss\">
  <channel>

    <title>$title - Last $numMatches Match Results</title>
    <link>$toplink/index.php?link=fights</link>
      <description>Herein lies the last $numMatches match results entered on the BZflag 'Classic CTF' league site.</description>
      <language>en</language>
      <copyright>GPL</copyright>
      <lastBuildDate>$pubDate</lastBuildDate>
      <generator>$toplink/rss</generator>
      <webMaster>$toplink/index.php?link=contact</webMaster>
      <ttl>60</ttl>
      <dc:language>en</dc:language>
      <dc:creator>$toplink</dc:creator>
      <dc:rights>GPL</dc:rights>
      <dc:date>$pubDate</dc:date>
      <sy:updatePeriod>hourly</sy:updatePeriod>
      <sy:updateFrequency>60</sy:updateFrequency>
      <sy:updateBase>2003-09-01T12:00+00:00</sy:updateBase>
      <image>
          <url>$toplink/rss/bzlicon.gif</url>
          <link>$toplink</link>
          <title>BZflag leagues</title>
          <height>30</height>
          <width>80</width>
      </image>";
        

if (0){
  echo "<item>
        <title>No info available</title>
        <link>$toplink</link>
        <description>There is no information in this feed.</description>
        <pubDate>$refreshed</pubDate>
        </item>";
} else {
  while ( $row = mysql_fetch_object ($res) ){
    if ($row->score1 > $row->score2){
      $t1 = $row->name1;
      $t2 = $row->name2;
      $s1 = $row->score1;   
      $s2 = $row->score2;   
    } else {
      $t1 = $row->name2;
      $t2 = $row->name1;
      $s1 = $row->score2;   
      $s2 = $row->score1;   
    } 
    matchResult ($row->id, $row->tsactual, $t1, $t2, $s1, $s2);
  }
}

echo '</channel></rss> ';


function matchResult ($id,$tsGMTstr, $teamA, $teamB, $scoreA, $scoreB){
  global $toplink;
  $tsU = strtotime($tsGMTstr);
  $tsRFC = timestampRFC2822 ($tsU);
  $tsDisp = date ('M d H:i', $tsU);
  echo "<item>
    <title>$tsDisp => $teamA:$scoreA, $teamB:$scoreB</title>
    <link>$toplink/index.php?link=fights#$id</link>
    <description>$tsDisp => $teamA:$scoreA, $teamB:$scoreB</description>
    <pubDate>$tsRFC</pubDate>
  </item>";
}



function timestampRFC2822 ($tsUnix){
  return date ('Y-m-d\TH:i:s+00:00', $tsUnix);
}



?>
