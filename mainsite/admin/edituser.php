<?
include "../document.php";

$doc = new Document;
if(isset($sessionID)) {
  $substr = explode("|", $sessionID);
  $result = mysql_query("SELECT * FROM passwd WHERE password = '$substr[1]' AND username = '$substr[0]' AND access = '$substr[2]'");
  if(mysql_num_rows($result) == 1 && $substr[2] >= 3) {
    if(!isset($edit)) {
      $doc->begin("edit user", 1);
      $result = mysql_query("SELECT * FROM passwd");
      while($currow = mysql_fetch_array($result)) {
	printf("<a href=\"edituser.php?edit=2&user=%s\">%s</a><br>\n", $currow["username"], $currow["username"]);
      }
    } else if($edit == 2) {
      $doc->begin("edit user", 1);
      $result = mysql_query("SELECT * FROM passwd WHERE username = '$user'");
      $row = mysql_fetch_array($result);
      $user = $row["username"];
      $access = $row["access"];
print <<< end
<form method="GET" action="edituser.php">
<input type="hidden" name="edit" value="3">
<input type="hidden" name="user" value="$user">
<table border="0">
  <tr>
    <td>Username:</td>
    <td><input type="text" maxlength="32" name="username" value="$user">
  </tr>
  <tr>
    <td>Password:</td>
    <td><input type="password" name="pass"></td>
  </tr>
  <tr>
    <td>Confirm password:</td>
    <td><input type="password" name="passconf"></td>
  </tr>
  <tr>
    <td>Access level:</td>
    <td>
      <select name="access">
end;
      echo "        <option value=\"1\"";
      if($access == 1)
	echo " selected";
      echo ">Level 1: Create news, manage personal releases</option>\n";
      echo "        <option value=\"2\"";
      if($access == 2)
	echo " selected";
      echo ">Level 2: Full news control</option>\n";
      echo "        <option value=\"3\"";
      if($access == 3)
	echo " selected";
      echo ">Level 3: Supah Usah</option>\n";
print <<< end
      </select>
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="Modify"></td>
  </tr>
</table>
</form>
end;
    } else if($edit == 3) {
      $doc->begin("edit user", 1);
      if($username == "") {
print <<< end
username cannot be blank.
end;
      } else {
        if($pass == "" && $passconf == "") {
	  $result = mysql_query("UPDATE passwd SET username = '$username', access = '$access' WHERE username = '$user'");
print <<< end
User updated.
end;
        } else if($pass == $passconf) {
	  $hash = md5($pass);
	  $result = mysql_query("UPDATE passwd SET username = '$username', password = '$hash', access = '$access' WHERE username = '$user'");
print <<< end
User updated.
end;
        } else {
print <<< end
Passwords don't match
end;
        }
      }
    }
  } else {
    $doc->begin("edit user", 1);
print <<< end
Access denied.
end;
  }
} else {
  $doc->begin("edit user", 1);
print <<< end
Access denied.
end;
}
$doc->end();

?>
