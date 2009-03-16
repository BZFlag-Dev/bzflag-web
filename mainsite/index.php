<?
include "document.php";

$doc = new Document;
$doc->begin("home");

$result = mysql_query("SELECT * FROM news ORDER BY date DESC");

print <<< end
<table border="0">
<tr>
<td>
<center><b>BZFlag is a free online multiplayer cross-platform open source 3D tank battle game.</b></center>
<p>The name originates from "Battle Zone capture the Flag".  It runs on Irix, Linux, *BSD, Windows, Mac OS X, and many other platforms.  It's one of the most popular games ever on Silicon Graphics machines and continues to be developed and improved to this day.  It's one of the most popular open source games ever.  For more information, check out some of the <a href="reviews.php">reviews</a>.</p>
</td>
</tr>
<tr>
<td align="center">
<br><br>
<a href="http://my.bzflag.org/bb/viewforum.php?f=8" alt="NEWS"><img src="images/bz_news_button.png" width=256 height=128 border=0></a>
<a href="http://bzflag.org/screenshots/" alt="screenshots"><img src="images/bz_screenshots_button.png" width=256 height=128 border=0></a>
<a href="https://sourceforge.net/project/showfiles.php?group_id=3248" alt="downloads"><img src="images/bz_downloads_button.png" width=256 height=128 border=0></a>
<a href="http://my.bzflag.org/bb/" alt="forums"><img src="images/bz_forums_button.png" width=256 height=128 border=0></a>
<a href="http://bzflag.org/getin" alt="contribute"><img src="images/bz_contribute_button.png" width=256 height=128 border=0></a>
<a href="http://my.bzflag.org/w/" alt="wiki"><img src="images/bz_wiki_button.png" width=256 height=128 border=0></a>

</td>
</tr>
</table>

<br><br>

end;

if (0) {

printf("<div align=\"center\"><b>Latest News:</b></div><br>");

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
}

mysql_free_result($result);
}

$doc->end();

?>
