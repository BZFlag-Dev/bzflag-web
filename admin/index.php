<?
include "../document.php";

$doc = new Document;

if(isset($sessionID) && !isset($function)) {
  $substr = explode("|", $sessionID);
  $query = "SELECT * FROM passwd WHERE password = '$substr[1]' AND username = '$substr[0]' AND access = '$substr[2]'";
  $result = mysql_query($query);
  if(mysql_num_rows($result) == 1) {
    if($substr[2] >= 1) {
      $doc->begin("admin", 1);
print <<< end
<br>
<b>News:</b><br>
<a href="/admin/addnews.php">Add news</a><br>
end;
    }
    if($substr[2] >= 2) {
print <<< end
<a href="/admin/editnews.php">Edit news</a><br>
<a href="/admin/deletenews.php">Delete news</a><br>
end;
    }
	 if($substr[2] >= 1) {
print <<< end
<br>
<b>MOTD:</b><br>
<a href="/admin/setMOTD.php">Set MOTD</a><br>
end;
    }
    if($substr[2] >= 3) {
print <<< end
<br>
<b>Users:</b><br>
<a href="/admin/userlist.php">User list</a><br>
<a href="/admin/createuser.php">Create a user</a><br>
<a href="/admin/edituser.php">Edit a user</a><br>
<a href="/admin/deleteuser.php">Delete a user</a><br>
end;
    }
    if($substr[2] >= 1) {
print <<< end
<br>
<b>Control:</b><br>
<a href="/admin/index.php?function=logout">Logout</a><br>
<a href="/admin/chpass.php">Change Password</a><br>
<br>
end;
    }
    $doc->end();
  } else {
    $doc->begin("admin", 1);
print <<< end
<h3>Error</h3>
An error has occurred. Please contact Tim Riker at <a href="mailto:tim@rikers.org">tim@rikers.org</a>.
end;
    $doc->end();
  }
  mysql_free_result($result);
} else if(isset($function) && $function == "logout") {
  setcookie("sessionID", "", time() - 10);
  $doc->begin("admin", 1);
print <<< end
<br>
Logged out!
<p>Enter username and password to log in again:
<form action="index.php" method="POST">
<table border="0">
  <tr>
    <td>Username:</td>
    <td><input type="text" name="username"></td>
  </tr>
  <tr>
    <td>Password:</td>
    <td><input type="password" name="password"></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="Login"></td>
  </tr>
</table>
</form>
end;
  $doc->end();
} else {
  if(isset($username) && isset($password)) {
    $hash = md5($password);
    $result = mysql_query("SELECT * FROM passwd WHERE username = '$username' AND password = '$hash'");
    if(mysql_num_rows($result) == 1) {
      $currow = mysql_fetch_array($result);
      $cookie = sprintf("%s|%s|%s", $currow["username"], $currow["password"], $currow["access"]);
      setcookie("sessionID", $cookie, 0);
      $access = $currow["access"];
      $doc->begin("admin", 1);
      if($access >= 1) {
print <<< end
<br>
<b>News:</b><br>
<a href="/admin/addnews.php">Add news</a><br>
end;
      }
      if($access >= 2) {
print <<< end
<a href="/admin/editnews.php">Edit news</a><br>
<a href="/admin/deletenews.php">Delete news</a><br>
end;
      }
      if($access >= 3) {
print <<< end
<br>
<b>Users:</b><br>
<a href="/admin/userlist.php">User list</a><br>
<a href="/admin/createuser.php">Create a user</a><br>
<a href="/admin/edituser.php">Edit a user</a><br>
<a href="/admin/deleteuser.php">Delete a user</a><br>
end;
      }
      if($access >= 1) {
print <<< end
<br>
<b>Control:</b><br>
<a href="/admin/index.php?function=logout">Logout</a><br>
<a href="/admin/chpass.php">Change Password</a><br>
<br>
end;
      }
      $doc->end();
    } else {
      $doc->begin("admin", 1);
print <<< end
Login failed. Either your username or password was incorrect.<br>
<form action="index.php" method="POST">
<table border="0">
  <tr>
    <td>Username:</td>
    <td><input type="text" name="username"></td>
  </tr>
  <tr>
    <td>Password:</td>
    <td><input type="password" name="password"></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="Login"></td>
  </tr>
</table>
</form>
end;
      $doc->end();
    }
    mysql_free_result($result);
  } else {
    $doc->begin("admin", 1);
print <<< end
<br>
<form action="index.php" method="POST">
<table border="0">
  <tr>
    <td>Username:</td>
    <td><input type="text" name="username"></td>
  </tr>
  <tr>
    <td>Password:</td>
    <td><input type="password" name="password"></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="Login"></td>
  </tr>
</table>
</form>
end;
    $doc->end();
  }
}


?>
