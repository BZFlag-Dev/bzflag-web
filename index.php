<?
include "document.php";

$doc = new Document;
$doc->begin("home");

$result = mysql_query("SELECT * FROM news ORDER BY date DESC");

print <<< end
BZFlag is a multiplayer 3D tank battle game. Its one of the most popular
games ever on Silicon Graphics machines. For more information, check out
some of the <a href="reviews.php">reviews</a>.<br><br>

<div align="center"><b>Latest News:</b></div><br>
end;

if(mysql_num_rows($result) == 0) {
  print "No news!?!?<br><br><br><br>\n";
} else {
  $i = 0;
  while($currow = mysql_fetch_array($result)) {
    if($i < 5) {
      printf("<b>%s</b> - %s by %s<br>%s<br><br>\n", $currow["headline"], date("F j, Y, g:i A", $currow["date"]), $currow["username"], $currow["body"]);
    }
    $i++;
  }
  if(mysql_num_rows($result) > 5) {
    print "<br><div align=\"center\"><a href=\"/bzflag/news/old.php\">old news</a></div>\n";
  }
}

mysql_free_result($result);

$doc->end();

?>
