<?
include "../document.php";

$doc = new Document;
$doc->begin("edit news", 1);
if(isset($sessionID)) {
  $substr = explode("|", $sessionID);
  $result = mysql_query("SELECT * FROM passwd WHERE password = '$substr[1]' AND username = '$substr[0]' AND access = '$substr[2]'");
  if(mysql_num_rows($result) == 1 && $substr[2] >= 2) {
    if(!isset($edit)) {
      $result = mysql_query("SELECT * FROM news ORDER BY date DESC");
      while($currow = mysql_fetch_array($result)) {
	printf("<a href=\"editnews.php?edit=2&entry=%s\">%s</a><br>\n", $currow[date], $currow["headline"]);
      }
    } else if($edit == 2) {
      $result = mysql_query("SELECT * FROM news WHERE date = $entry");
      $currow = mysql_fetch_array($result);
      $headline = $currow["headline"];
      $body = $currow["body"];
print <<< end
<form method="GET" action="editnews.php">
<input type="hidden" name="edit" value="3">
<input type="hidden" name="entry" value="$entry">
<b>Headline:</b><br>
<input type="text" name="headline" value="$headline"><br>
<b>Body:</b><br>
<textarea rows="8" cols="45" name="body">
$body
</textarea><br>
<input type="submit" value="Modify">
</form>
end;
    } else {
      $substr = explode("|", $sessionID);
      $user = $substr[0];
      $result = mysql_query("UPDATE news SET headline = '$headline', body = '$body', username = '$user' WHERE date = '$entry'");
      $result = mysql_query("SELECT * FROM news WHERE date = '$entry'");
      $currow = mysql_fetch_array($result);
      printf("<b>%s</b> - %s by %s<br>%s<br><br>\n", $currow["headline"], date("F j Y, g:i A", $currow["date"]), $currow["username"], $currow["body"]);
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
