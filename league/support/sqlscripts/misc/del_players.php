<?php

require_once ('phplib.php');




delPlayersLike ('{cigar');
delPlayersLike ('[url=');
delPlayersLike ('http://');

function delPlayersLike ($name){
	$set = sqlQuery ("SELECT callsign, id FROM l_player WHERE callsign LIKE '$name%'");
	while (  $row =  mysql_fetch_object ($set) ){
		$id = $row->id;
		sqlQuery ("DELETE FROM l_message WHERE fromid=$id");
		sqlQuery ("DELETE FROM l_forummsg WHERE fromid=$id");
		sqlQuery ("DELETE FROM bzl_visit WHERE pid=$id");
		sqlQuery ("DELETE FROM l_player WHERE id=$id");
echo "$row->callsign\n";
	}
}

?>
