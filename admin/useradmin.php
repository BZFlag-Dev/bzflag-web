<?

function print_detail($name) {
  $result = mysql_query("SELECT * FROM passwd WHERE username = '$name'");
  $currow = mysql_fetch_array($result);
  $user = $currow["username"];
print <<< end
  <tr>
    <td>Username:</td>
    <td>$user</td>
  </tr>
  <tr>
    <td>Access level:</td>
    <td>
end;
  print_accesslevel($currow["access"]);
print <<< end
  </tr>
end;
}

function print_accesslevel($level) {
  if($level == 1) {
    echo "Level 1: Create news, manage personal releases";
  } else if($level == 2) {
    echo "Level 2: Full news control";
  } else if($level == 3) {
    echo "Level 3: Supah Usah";
  }
}

?>
