<?php

define("DATEFORMAT_FULL", "Y-m-d H:i:s");

function smileys($input) {
  $res = mysql_query("select * from l_smiley");
  while($row = mysql_fetch_object($res)) {
	$input = str_replace(htmlspecialchars($row->code), '<img border=0 src="images/'.$row->image.'">', $input);
  }
  return $input;
}

function displayRating($id) {
	$res = mysql_query("select * from l_team where id = '$id'");
	$row = mysql_fetch_array($res);
	$rating = $row[score];
	$text = "<img src=\"images/blocks/";
	if($rating < 1000) {
		$text .= "gray";
	} else if($rating >= 1000 && $rating < 1100) {
		$text .= "green";
	} else if($rating >= 1100 && $rating < 1200) {
		$text .= "blue";
	} else if($rating >= 1200 && $rating < 1300) {
		$text .= "purple";
	} else if($rating >= 1300 && $rating < 1400) {
		$text .= "orange";
	} else if($rating >= 1400) {
		$text .= "red";
	}
	$text .= ".gif\">&nbsp;".$rating;
	return $text;
}

/* sendMessage:
				Send a message to somebody
*/
function sendMessage($fromid, $toid, $subject, $msg, $toteam = 'no') {
				$now = gmdate("Y-m-d H:i:s");
				mysql_query("insert into l_message values(0, $toid, $fromid, '$now',
					\"".(strip_tags(addslashes($subject)))."\",
					\"".(addslashes($msg))."\", 'new', '$toteam')");
}

/* sendTeamMessage:
				Send a message to a team
*/
function sendTeamMessage($fromid, $toteamid, $subject, $msg) {
		$res = mysql_query("select id from l_player where team=$toteamid");
		while($obj = mysql_fetch_object($res)) {
				sendMessage($fromid, $obj->id, $subject, $msg, 'yes');
		}
}

/* updateRating:
				Updates two teams' ratings after a match
*/
function updateRating($score, $teama, $teamb) {
	// score meanings:
	//		1: team A won
	//		0: draw
	// -1: team B won
	$res = mysql_query("select score from l_team where id = '$teama'");
	$obj = mysql_fetch_object($res);
	$oldA = $obj->score;
	$res = mysql_query("select score from l_team where id = '$teamb'");
	$obj = mysql_fetch_object($res);
	$oldB = $obj->score;
	$factor = 50.0;
	$S = 1.0 / (1 + pow(10.0, ($oldB - $oldA) / 400.0));
	$Sc = ($score + 1) / 2.0;
	$diff = abs($factor * ($Sc - $S));
	if($diff < 1)
					$diff = 1;
	$d = floor($diff + 0.5);
	if($Sc - $S < 0)
					$d = -$d;
	if($Sc - $S == 0) {
			$d = 0;
	}
	$newA = $oldA + $d;
	$newB = $oldB - $d;
	mysql_query("update l_team set score = '$newA' where id = '$teama'");
	mysql_query("update l_team set score = '$newB' where id = '$teamb'");
}

?>
