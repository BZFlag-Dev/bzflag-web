<?
include "../document.php";
include "useradmin.php";

$doc = new Document;
$doc->begin("create a user", 1);

if(isset($sessionID)) {
  $substr = explode("|", $sessionID);
  $result = mysql_query("SELECT * FROM passwd WHERE password = '$substr[1]' AND username = '$substr[0]' AND access = '$substr[2]'");
  if(mysql_num_rows($result) == 1 && $substr[2] >= 3) {
    if(!isset($add)) {
print <<< end
<form method="POST" action="createuser.php">
<input type="hidden" name="add" value="2">
<table border="0">
  <tr>
    <td>Username:</td>
    <td><input type="text" maxlength="32" name="username"></td>
  </tr>
  <tr>
    <td>Password:</td>
    <td><input type="password" name="password"></td>
  </tr>
  <tr>
    <td>Confirm Password:</td>
    <td><input type="password" name="pwconf"></td>
  </tr>
  <tr>
    <td>Access level:</td>
    <td>
      <select name="access">
        <option value="1">Level 1: Create news, manage personal releases</option>
        <option value="2">Level 2: Full news control</option>
        <option value="3">Level 3: Supah Usah</option>
      </select>
    </td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="Create"></td>
  </tr>
</table>
</form>
end;
    } else if($add == 2) {
      $result = mysql_query("SELECT * FROM passwd WHERE username = '$username'");
      if(mysql_num_rows($result) != 0) {
print <<< end
User already exists.
end;
      } else {
        if($password != $pwconf) {
print <<< end
Passwords don't match.
end;
        } else {
	  $hash = md5($password);
	  $query = "INSERT INTO passwd (username, password, access) values ('$username', '$hash', '$access')";
	  $result = mysql_query($query);
	  echo "user added.<br><br><br><br>";
        }
      }
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
