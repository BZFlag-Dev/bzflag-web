<?php

include('../document.php');

require_once('TableEditor.php');

if(isset($bzID)) {
  $substr = explode("|", $bzID);
  $result = mysql_query("SELECT * FROM passwd WHERE password = '$substr[1]' AND username = '$substr[0]' AND access = '$substr[2]'");
  if(mysql_num_rows($result) == 1 && $substr[2] >= 1) {

	$editor = new TableEditor($db, 'motd');

	$editor->setDefaultOrderby('title');
	$editor->setConfig('allowCopy', false);
	$editor->setConfig('allowCSV', false);

	$editor->noDisplay('id');

	$editor->display();

  } else {
    $doc = new Document();
    $doc->begin("Edit MOTD", 1);
    print "Unable to find any any MOTD results";
    $doc->end();
  }
} else {
  $doc = new Document();
  $doc->accessDenied();
}

?>
