<?
include "../document.php";

function  table_exist ($table){
	$query = "select * from $table";
	$result = mysql_query($query);
	$num_rows = @mysql_num_rows($result); 
	if ($num_rows){
		return true; 
	}else{
		return false; 
	}
}

$doc = new Document;
$doc->begin("set MOTD", 1);
if(isset($sessionID)) {
  $substr = explode("|", $sessionID);
  $result = mysql_query("SELECT * FROM passwd WHERE password = '$substr[1]' AND username = '$substr[0]' AND access = '$substr[2]'");
  if(mysql_num_rows($result) == 1 && $substr[2] >= 1) {
    if(!isset($add)) {
	// check for the table
	 if( !table_exist('motd')){	
	 	 $query = "CREATE TABLE motd ( message varchar(255) NOT NULL default '') TYPE=MyISAM COMMENT='the current message';";
		 $result = mysql_query($query);
		 $query = "INSERT INTO motd (message) values ('Welcome to BZFlag')";
		 $result = mysql_query($query);
	 }
	$query = "SELECT * FROM motd";
	$result = mysql_query($query) or die("Query failed : " . mysql_error());
	
	$row = mysql_fetch_array($result);
	$currentMOTD = $row[message]; // message
print <<< end
CurrentMOTD: $currentMOTD</br>
<form method="post" action="setMOTD.php">
<input type="hidden" name="add" value="1">
<b>Message:</b><br>
<textarea rows="8" cols="45" name="body"></textarea><br>
<input type="submit" value="Set MOTD"><br>
</form>
end;
    } else {
print <<< end
<div align="center"><b>MOTD Updated</b></div><br><br>
end;
      $substr = explode("|", $sessionID);
      $user = $substr[0];
      $query = "UPDATE motd SET message='$body';";
      $result = mysql_query($query);
      $result = mysql_query("SELECT * FROM motd");
      $currow = mysql_fetch_array($result);
      printf("New MOTD:<b>%s</b><br><br>\n", $currow["message"]);
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
