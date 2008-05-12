<?
include "../document.php";

$doc = new Document;
$doc->begin("delete news", 1);
if(isset($sessionID)) {
  $substr = explode("|", $sessionID);
  $result = mysql_query("SELECT * FROM passwd WHERE password = '$substr[1]' AND username = '$substr[0]' AND access = '$substr[2]'");
  if(mysql_num_rows($result) == 1 && $substr[2] >= 2) {
    if(!isset($delete)) {
      $result = mysql_query("SELECT * FROM news ORDER BY date DESC");
      while($currow = mysql_fetch_array($result)) {
	printf("<a href=\"deletenews.php?delete=2&entry=%s\">%s</a><br>\n", $currow[date], $currow["headline"]);
      }
    } else if($delete == 2) {
      $result = mysql_query("DELETE FROM news WHERE date = $entry");
      print "deleted<br><br><br><br>\n";
    }
  } else {
print <<< end
Access denied.
end;
  }
} else {
print <<< end
Access denied.
end;
}
$doc->end();

?>
