<?
include "../document.php";

$doc = new Document;
$doc->begin("add news", 1);
if(isset($sessionID)) {
  $substr = explode("|", $sessionID);
  $result = mysql_query("SELECT * FROM passwd WHERE password = '$substr[1]' AND username = '$substr[0]' AND access = '$substr[2]'");
  if(mysql_num_rows($result) == 1 && $substr[2] >= 1) {
    if(!isset($add)) {
print <<< end
<form method="post" action="addnews.php">
<input type="hidden" name="add" value="1">
<b>Headline:</b><br>
<input name="headline" type="text"><br>
<b>Body:</b><br>
<textarea rows="8" cols="45" name="body"></textarea><br>
<input type="submit" value="Add news"><br>
</form>
end;
    } else {
print <<< end
<div align="center"><b>News Added</b></div><br><br>
end;
      $substr = explode("|", $sessionID);
      $user = $substr[0];
      $query = "INSERT INTO news (headline, body, date, username) values ('$headline', '$body', '" . time() . "', '$user')";
      $result = mysql_query($query);
      $result = mysql_query("SELECT * FROM news ORDER BY date DESC");
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
