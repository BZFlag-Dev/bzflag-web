<?
include "../document.php";

$doc = new Document;
$doc->begin("delete user", 1);
if(isset($sessionID)) {
  $substr = explode("|", $sessionID);
  $result = mysql_query("SELECT * FROM passwd WHERE password = '$substr[1]' AND username = '$substr[0]' AND access = '$substr[2]'");
  if(mysql_num_rows($result) == 1 && $substr[2] >= 3) {
    if(!isset($delete)) {
      $result = mysql_query("SELECT * FROM passwd");
      while($currow = mysql_fetch_array($result)) {
	printf("<a href=\"deleteuser.php?delete=2&user=%s\">%s</a><br>\n", $currow["username"], $currow["username"]);
      }
    } else if($delete == 2) {
print <<< end
Are you sure you want to delete $user?<br><br>
<a href="deleteuser.php?delete=3&user=$user">Confirm deletion</a>
end;
    } else if($delete == 3) {
      mysql_query("DELETE FROM passwd WHERE username = '$user'");
print <<< end
User deleted.
end;
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
