<?
include "../document.php";

$doc = new Document;
if(isset($sessionID)) {
  $substr = explode("|", $sessionID);
  $result = mysql_query("SELECT * FROM passwd WHERE password = '$substr[1]' AND username = '$substr[0]' AND access = '$substr[2]'");
  if(mysql_num_rows($result) == 1) {
    if(!(isset($pass) || isset($passconf))) {
      $doc->begin("change password", 1);
print <<< end
<br>
<form action="chpass.php" method="POST">
  <table border="0">
    <tr>
      <td>Password:</td>
      <td><input type="password" name="pass"></td>
    </tr>
    <tr>
      <td>Confirm password:</td>
      <td><input type="password" name="passconf"></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><input type="submit" value="Change password"></td>
    </tr>
  </table>
</form>
end;
    } else if($pass != $passconf) {
      $doc->begin("change password", 1);
print <<< end
Passwords don't match.
<br><br>
<form action="chpass.php" method="POST">
  <table border="0">
    <tr>
      <td>Password:</td>
      <td><input type="password" name="pass"></td>
    </tr>
    <tr>
      <td>Confirm password:</td>
      <td><input type="password" name="passconf"></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><input type="submit" value="Change password"></td>
    </tr>
  </table>
</form>
end;
    } else {
      $hash = md5($pass);
      $result = mysql_query("UPDATE passwd SET password = '$hash' WHERE username = '$substr[0]'");
      $cookie = "$substr[0]|$hash|$substr[2]";
      setcookie("sessionID", $cookie, 0);
      $doc->begin("changed", 1);
print <<< end
Password changed.
end;
    }
  } else {
    $doc->begin("change password", 1);
print <<< end
Access denied.
end;
  }
} else {
    $doc->begin("change password", 1);
print <<< end
Access denied.
end;
}
$doc->end();

?>
