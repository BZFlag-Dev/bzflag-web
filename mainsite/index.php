<?
include "document.php";

$doc = new Document;
$doc->begin("home");

$result = mysql_query("SELECT * FROM news ORDER BY date DESC");

print <<< end
<table border="0">
<tr>
<td>
	<div align="center"><b>What is BZFlag:</b></div><br>
</td>
</tr>
<tr>
<td>
BZFlag is a free online multiplayer cross-platform open source 3D tank battle game.  The name originates from "Battle Zone capture the Flag".  It runs on Irix, Linux, *BSD, Windows, Mac OS X, and many other platforms.  It's one of the most popular games ever on Silicon Graphics machines and continues to be developed and improved to this day.  It's one of the most popular open source games ever.  For more information, check out some of the <a href="reviews.php">reviews</a>.
</td>
</tr>
</table>
<br><br>
<table bgcolor="#EEEEEE" border="0" class="sidebar_border">
<tr>
<td>
<div align="center"><b>Latest News:</b></div><br>
end;

if(mysql_num_rows($result) == 0) {
  print "No news!?!?<br><br><br><br>\n";
} else {
  $i = 0;
  while($currow = mysql_fetch_array($result)) {
    if($i < 5) {
      printf("<table cellspacing=\"0\" cellpadding=\"0\"><tr><td><table width=\"100%%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" ><tr><td><b>%s</b> - %s by %s</td></td></tr></table></tr><tr><td><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\"><tr><td>%s</td></tr></table></td></tr></table><br>\n", $currow["headline"], date("F j, Y, g:i A", $currow["date"]), $currow["username"], $currow["body"]);
    }
    $i++;
  }
/*
  if(mysql_num_rows($result) > 5) {
    print "<br><div align=\"center\"><a href=\"/bzflag/news/old.php\">old news</a></div>\n</td></tr></table>";
  }
*/
}

mysql_free_result($result);

$doc->end();

?>
