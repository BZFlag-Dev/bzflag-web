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
	 	 $query = "CREATE TABLE motd ( title varchar(80) NOT NULL default '', description varchar(255) NOT NULL default '', author varchar(64) NOT NULL default '', pubDate varchar(64) NOT NULL default '', version varchar(64) NOT NULL default '', id bigint(20) unsigned NOT NULL default 0 ) TYPE=MyISAM COMMENT='the current message';";
		 $result = mysql_query($query);
		 $query = "INSERT INTO motd (title, description, author, pubDate, version, id) values ('Welcome to BZFlag', 'BZFlag is a free multiplayer multiplatform 3D tank battle game. The name stands for Battle Zone capture Flag. It runs on Irix, Linux, BSD, Windows, Mac OS X and other platforms.', 'BZFlag Developers', '', '0.0', '0' )";
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
<table cellspacing=10 cellpadding=0 border=0>
  <tr><td align="right">Author:</td><td align="left"><input type="text" size=40 maxlength=64 name="author" value="BZFlag Developers"></td></tr>
  <tr><td align="right">Publication Date:</td><td align="left">
end;
 echo "<input type=\"text\" size=40 maxlength=64 name=\"pubDate\" value=\"" . gmdate('D, d M Y H:i:s T') . "\"></td></tr>\n";

print <<< end
  <tr><td align="right">Version:</td><td align="left"><input type="text" size=40 maxlength=64 name="version" value="1.12"></td></tr>
  <tr><td align="right">Title:</td><td align="left"><input type="text" size=80 maxlength=80 name="title"></td></tr>
  <tr><td align="right">Message:</td><td align="left"><textarea rows=8 cols=40 name="description"></textarea></td></tr>
  <tr><td align="right"><input type="submit" value="Set MOTD"></td><td align="left">&nbsp;</tr>
</table>
</form>
end;
    } else {
print <<< end
<div align="center"><b>MOTD Updated</b></div><br><br>
end;
      $substr = explode("|", $sessionID);
      $user = $substr[0];
      $id = gmdate('YmdHis');
      $query = "INSERT INTO motd (title, description, author, pubDate, version, id) values ('$title', '$description', '$author', '$pubDate', '$version', $id );";
      $result = mysql_query($query);
      if ($result) {
	$result = mysql_query("SELECT * FROM motd WHERE id='$id'");
	if ($result) {
	  $currow = mysql_fetch_array($result);
	  printf("New MOTD:<b>%s</b><br>Posted on %s by %s for BZFlag %s<br>%s<br><br>\n", $currow["title"], $currow["pubDate"], $currow["author"], $currow["version"], $currow["description"] );
	} else {
	  printf("Unable to get the latest MOTD<br>");
	}
      } else {
	printf("Unable to insert into the database<br><br>Query:<br><code>%s</code>\n<br>", $query);
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
