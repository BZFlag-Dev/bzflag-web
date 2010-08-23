<?php

require_once ('phplib.php');

$set = sqlQuery ('show tables');
while ($row = mysql_fetch_array ($set)){
	$tabname = $row[0];
	echo "Dropping table: $tabname\n";
	sqlQuery ("drop table $tabname");
}

?>
