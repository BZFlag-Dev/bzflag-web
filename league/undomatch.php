<?php

require_once("lib/db.php");
require_once("lib/session.php");

session_start();

echo '<HTML><CENTER>';



/********** the big test ....
$res = mysql_query("select  l_fight.*,  l_team1.score as rating1, l_team2.score as rating2
													from l_fight
													left join l_team l_team1 on l_team1.id = team1
													left join l_team l_team2 on l_team2.id = team2 
													order by tstamp");
while ($row = mysql_fetch_object ($res)){
	test ($row->score1, $row->score2, $row->rating1, $row->rating2 );
}
exit;
*********/



if ( !isset($_SESSION['authenticated']) 
||   !$_SESSION['authenticated']
||	$_SESSION['level'] != 'admin'){
	echo 'You are not authorized for this. Shame on you.<BR>';
	exit;
}   

echo '<font size=+2><B>DELETE LAST ENTERED MATCH</b></font><BR>';
$Callsign = $_SESSION['callsign'];
echo "<font size=-1>(Hello, $Callsign)</font><HR><BR>";


if ($_POST['action'] == 'DELETE')
	undoLastMatch ();
else
	presentForm ($_POST['action']);






function presentForm ($action){
	$row = mysql_fetch_object(mysql_query("select l_fight.*, 
													l_team1.name as name1, l_team2.name as name2, 
													l_team1.score as rating1, l_team2.score as rating2
													from l_fight 
													left join l_team l_team1 on l_team1.id = team1
													left join l_team l_team2 on l_team2.id = team2 
													order by tstamp desc limit 1"));
	if (!$row){
		echo "SQL error - can't do this right now, sorry!<BR>";
		exit;
	}													

	echo "<BR><TABLE bgcolor=e0e0e0><TR align=center><TD colspan=3>Currently, the last entered match is:<HR></td></tr>
			<TR><TD width=200 align=right><nobr>$row->name1:</nobr></td><TD width=5></td><TD width=200>$row->score1</td></tr>
			<TR><TD align=right><nobr>$row->name2:</nobr></td><td></td><TD>$row->score2</td></tr>
			<TR><TD align=right>ON:</td><td></td><TD>$row->tstamp</td></tr></table>";


	backoutRatings ($row->score1, $row->score2, $row->rating1, $row->rating2, $new1, $new2);
	echo "<BR><TABLE align=center><TR>
				<TD align=right><b>$row->name1</b></td><TD> rating will change from $row->rating1 to $new1 </td><tr>
				<TD align=right><b>$row->name2</b></td><TD> rating will change from $row->rating2 to $new2 </td><tr>
				</table>";
				
	echo '<font size=+1 color=660000><BR>Are you absolutely sure that you want to <b>DELETE this match</b> ?</font><BR>';

	echo "<FORM method=post>
				<input type=hidden name='matchtstamp'  value='$row->tstamp'>
				<input type=hidden name='matcht1'  value='$row->team1'>
				<input type=hidden name='matcht2'  value='$row->team2'>
				<input type=hidden name='matchs1'  value='$row->score1'>
				<input type=hidden name='matchs2'  value='$row->score2'>
			 	<BR><TABLE><TR><TD><INPUT type=submit name=action value=DELETE></td><TD width=15></td></form>
				<TD><FORM action='./index.php'><INPUT type=submit value=CANCEL></td></tr></table></form>";
}



function backToMain (){
	echo '<BR><FORM action="./index.php"><INPUT type=submit value="Back to Main Site"></form>';
}



function undoLastMatch (){
	$row = mysql_fetch_object(mysql_query("select l_fight.*, 
													l_team1.name as name1, l_team2.name as name2, 
													l_team1.score as rating1, l_team2.score as rating2
													from l_fight 
													left join l_team l_team1 on l_team1.id = team1
													left join l_team l_team2 on l_team2.id = team2 
													order by tstamp desc limit 1"));
	if (!$row){
		echo "SQL error - can't do this right now, sorry!<BR>";
		backToMain ();
		exit;
	}													

	if ($row->tstamp != $_POST['matchtstamp'] 
	||  $row->team1 != $_POST['matcht1'] 
	||  $row->team2 != $_POST['matcht2'] 
	||  $row->score1 != $_POST['matchs1'] 
	||  $row->score2 != $_POST['matchs2']	){
		echo "WHOAH! Someone just entered a newer match - cannot honor your request<BR>";
		backToMain ();
		exit;
	}

	backoutRatings ($row->score1, $row->score2, $row->rating1, $row->rating2, $new1, $new2);
	mysql_query ("delete from l_fight where tstamp='$row->tstamp' AND team1=$row->team1
	           AND team2=$row->team2 AND score1=$row->score1 AND score2=$row->score2");
	mysql_query ("update l_team set score=$new1 where id=$row->team1");
	mysql_query ("update l_team set score=$new2 where id=$row->team2");
	echo "<BR><font size=+1 color=660000>Match deleted!</font>";
	echo "<BR><BR><TABLE align=center><TR>
				<TD align=right><b>$row->name1's</b></td><TD> rating has been changed from $row->rating1 to $new1 </td><tr>
				<TD align=right><b>$row->name2's</b></td><TD> rating has been changed from $row->rating2 to $new2 </td><tr>
				</table>";

	backToMain ();
}




function backoutRatings ($scoreA, $scoreB, $newA, $newB, &$oldA, &$oldB){
	if ($scoreA==$scoreB && $newA==$newB){
		$oldA = $newA;
		$oldB = $newB;
		return;
	}
	$low  = -50;
	$high =  50;
	while (($high-$low) > 0.05 && ++$tries<50) {
		$try = $low + ($high-$low)/2;
		calculateRatings ($scoreA, $scoreB, $newA-$try, $newB+$try, $tryA, $tryB);	
		if ($tryA < $newA)
			$high = $try;
		else	
			$low = $try;
	}
	if ($tries>49){
		echo "<BR><BR>internal error. (endless loop break)<BR>";
		exit;
	}
	$oldA = floor ($newA-$try + 0.5);
	$oldB = floor ($newB+$try + 0.5);
}




function calculateRatings ($scoreA, $scoreB, $oldA, $oldB, &$newA, &$newB){
	if ($scoreA > $scoreB)
		$score = 1;
	elseif ($scoreA < $scoreB)
		$score = -1;
	else $score = 0;

	$factor = 50.0;
	$S = 1.0 / (1 + pow(10.0, ($oldB - $oldA) / 400.0));
	$Sc = ($score + 1) / 2.0;
	$diff = abs($factor * ($Sc - $S));
	if($diff < 1)
		$diff = 1;
	$d = floor($diff + 0.5);
	if($Sc - $S < 0)
		$d = -$d;
	if($Sc - $S == 0)
		$d = 0;
	$newA = $oldA + $d;
	$newB = $oldB - $d;
}




function test ($score1, $score2, $rate1, $rate2){
	backoutRatings ($score1, $score2, $rate1, $rate2, $A, $B);
	calculateRatings ($score1, $score2, $A, $B, $reA, &$reB);
	$colA = $rate1 == $reA ? '' : 'bgcolor=ff8888';
	$colB = $rate2 == $reB ? '' : 'bgcolor=ff8888';
	echo "<TABLE bgcolor=ffff88><TR align=right>
			<TD></td><TD>Match<BR>score</td><TD>&nbsp;&nbsp;Rating<BR>after</td>
			<TD>&nbsp;&nbsp;UNDO<BR>rating</td><TD>&nbsp;&nbsp;Recalc<BR>rating</td></tr><TR><td colspan=5><HR></td></tr>
		<TR align=right><TD>Team A</td> <TD>$score1</td> <TD>$rate1</td> <TD>$A</td> <TD $colA>$reA</td></tr>
		<TR align=right><TD>Team B</td> <TD>$score2</td> <TD>$rate2</td> <TD>$B</td> <TD $colB>$reB</td></tr>
		</table><BR><BR>";


	if ($rate1!=$reA || $rate2!=$reB  ){
		echo "ERROR!<BR>\n";
		exit;
	}
}

?>
