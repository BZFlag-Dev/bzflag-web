<?php

include('/etc/bzflag/serversettings.php');


function Sanitize($value)
{
  return mysql_real_escape_string(addslashes($value));  
}


if (!isset($_REQUEST['action']) && !isset($_REQUEST['value'])) {
  echo "bzidtools<br/><br/>";
  echo "<form action=''>";
  echo "action: ";
  echo "<select name='action'>";
  echo "<option value='id'>Name-to-ID</option>";
  echo "<option value='name'>ID-to-Name</option>";
  echo "</select>";
  echo "<br/><br/>";
  echo "value: ";
  echo "<input type='text' name='value'>";
  echo "<br/><br/>";
  echo "<input type='submit' value='Submit'>";
  echo "</form>";
  return;
}


header('Content-type: text/plain');

if (!isset($_REQUEST['action']) || !isset($_REQUEST['value'])) {
  echo "ERROR: bad inputs";
  return;
}

$db = mysql_connect($dbhost, $dbuname, $dbpass);
if (!$db) {
  echo "ERROR";
  return;
}
else {
  $result = mysql_select_db($bbdbname);
}
  
if (!$result) {
  echo "ERROR";
  return;
}

$action = $_REQUEST['action'];
$value = Sanitize($_REQUEST['value']);

$query = "";

if ($action == 'name') {
  $query = "SELECT username_clean FROM bzbb3_users WHERE user_id='" . $value. "'";
}
else if ($action == 'id') {
  $query = "SELECT user_id FROM bzbb3_users WHERE username_clean='" . $value . "'";
}
else {
  echo "ERROR: unknown action";
}
  
if ($query) {
  $result = mysql_query($query);
  if ($result && mysql_num_rows($result)) {
    $row = mysql_fetch_row($result);
    echo "SUCCESS: " . $row[0];
  } else {
    echo "ERROR: " . $query . " " . mysql_error();
  }
}

?>
