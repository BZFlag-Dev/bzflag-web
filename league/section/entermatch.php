<?php
function section_entermatch(&$templ)
{
	require_once('lib/common.php');
	$vars = array('f_ok_x','teama','teamb','scorea','scoreb');
	foreach($vars as $var)
		$$var = isset($_POST[$var]) ? $_POST[$var] : $_GET[$var];

	if($_SESSION['authenticated'] && ($_SESSION['level']== "admin")) {
		if($f_ok_x) {
			if($teama == $teamb) {
				echo 'Teams are the same - press back and try again';
			} else if(($scorea == "")||($scoreb == "")) {
				echo 'You must enter both scores - press back and try again';
			} else {
				$oldA = displayRating($teama);
				$oldB = displayRating($teamb);

				if($scorea == $scoreb) {
					$score = 0;
				} else {
					if($scorea < $scoreb) {
						$score = -1;
					} else {
						$score = 1;
					}
				}

				updateRating($score, $teama, $teamb);
				// Insert data into l_fight table
				$now = gmdate("Y-m-d H:i:s");
				mysql_query("insert into l_fight values($teama, $scorea, $teamb, $scoreb, '$now')");
				$newA = displayRating($teama);
				$newB = displayRating($teamb);
				echo 'Ratings updated<br>
				<table border="0" width="100%" cellspacing="0">
				<tr><td class=motd>Team</td><td class=motd>old rating</td><td class=motd>new rating</td></tr>
				<tr><td>A</td><td>'.$oldA.'</td><td>'.$newA.'</td></tr>
				<tr><td>B</td><td>'.$oldB.'</td><td>'.$newB.'</td></tr>
				</table>';

				$sql = "UPDATE l_team SET active='yes' WHERE id = '$teama' or id = '$teamb'";
				mysql_query($sql);
			}
		} else {
			echo '<center><form method="post">'.SID_FORM;

			$res = mysql_query("select id, name, active='yes' sorter from l_team order by active, name");
			echo 'Team A:<br>';
			echo '<select name="teama">';
			$marked = false;
			while($row = mysql_fetch_array($res)) {
				$id = $row[id];
				$name = $row[name];
				$sorter = $row[sorter];
				if( $sorter == 0 && !$marked )
				{
					$marked = true;
					print "<option value=\"\">----------- inactive teams</option>";
				}
				print "<option value=\"$id\" name=\"$id\">$name</option>";
			}

			echo '</select><br>';
			echo 'Score: <input size=2 name="scorea"><br>';

			$res = mysql_query("select id, name, active='yes' sorter from l_team order by active, name");
			echo '<hr>Team B:<br>';
			echo '<select name="teamb">';
			$marked = false;
			while($row = mysql_fetch_array($res)) {
				$id = $row[id];
				$name = $row[name];
				$sorter = $row[sorter];
				if( $sorter == 0 && !$marked )
				{
					$marked = true;
					print "<option value=\"\">----------- inactive teams</option>";
				}
				print "<option value=\"$id\" name=\"$id\">$name</option>";
			}
			echo '</select><br>';

			echo 'Score: <input size=2 name="scoreb"><br>';
			echo '<center>
				<input type="image" src="images/ok.gif" name="f_ok">
				</center>
				</form></center>';
		}
	}
}
?>
