<?php
function section_messages(&$templ)
{
	$vars = array('del','delbulk','checknum','read','link','read');
	foreach($vars as $var)
		$$var = isset($_POST[$var]) ? $_POST[$var] : $_GET[$var];

	if($_SESSION['authenticated'] ) {
		if(isset($del)) {
			mysql_query("delete from l_message
							where msgid=$del
							and toid={$_SESSION['playerid']}");
		}

		if(isset($delbulk)) {
			$numdel = 0;
			for($i = 0; $i < $checknum; $i++) {
				$delid = $_POST["del".$i];
				if(isset($delid)) {
					$numdel++;
					mysql_query("delete from l_message where msgid=$delid");
				}
			}
			if($numdel != 1) {
				$esse = 's';
			} else {
				$esse = '';
			}
			echo "<center>Deleted $numdel message$esse.</center>";
		}

		if(isset($read)) {
			// Display one message
			$res = mysql_query("select l_player.callsign sender, fromid, datesent, subject, msg, l_message.team
				from l_message
				left join l_player
				on id = fromid
				where toid={$_SESSION['playerid']}
				and msgid=$read");

			if(mysql_num_rows($res) == 0) {
				// Someone is playing with the headers
				echo "<center>Hey! Please stop playing with you browser!</center>";
			} else {
				// Display the message
				$msg = mysql_fetch_object($res);
				echo '<table align=center border=0 cellspacing=0 cellpadding=1>
				<tr><td class=tablehead>Date sent: '.$msg->datesent.'</td></tr>';

				if($msg->sender == '') {
								// Administrative message
								echo '<tr><td class=tablehead>From: <b>CTF League System</b></td></tr>';
				} else {
								echo '<tr><td class=tablehead>From: <a href="index.php?link=playerinfo&id='.$msg->fromid.'&'.SID.'">'.$msg->sender.'</a></td></tr>';
				}

				if($msg->subject == '') {
						$subject = 'No subject';
				} else {
						$subject = stripslashes($msg->subject);
				}

				echo '<tr><td class=tablehead>Subject:</td></tr>
						<tr><td>'.wordwrap(htmlentities($subject), 40, '<br>').'<hr>
						</td></tr>
						<tr><td class=tablehead>Content:</td></tr>';

				echo '<tr><td>';
				echo nl2br(htmlentities($msg->msg)).'<hr>';
				echo '</td></tr>
						</table>';
				// Set message as read
				mysql_query("update l_message set status='read' where msgid=$read");
				// Display buttons: delete goback reply
				echo '<br><center>';

				// Can't reply to administrative messages
				if($msg->sender != '') {
								echo '<form method=post action="index.php">'.SID_FORM.'<input type=hidden name=link value=sendmessage>
								<input type=hidden name=pid value='.$msg->fromid.'>
								<input type=hidden name=toteam value="'.$msg->team.'">
								<input type=hidden name=reply value=1>
								<input type=hidden name=mid value='.$read.'>
								<input type=image src="images/reply.gif">
								</form>';
				}
				echo '<a href="index.php?link=messages&'.SID.'"><img border=0 src="images/goback.gif"></a>&nbsp;
				<a href="index.php?link=messages&del='.$read.'&'.SID.'"><img border=0 src="images/delete.gif"></a></center>';
			}
		} else {
			// Display all messages
			$res = mysql_query("select msgid, l_player.callsign sender, fromid, datesent, subject, l_message.status, l_message.team
											from l_message
											left join l_player
											on id = fromid
											where toid={$_SESSION['playerid']} order by datesent desc");

			if(mysql_num_rows($res) == 0) {
				echo "<center>You don't have any message to read.</center>";
			} else {
				echo '<form method=post>'.SID_FORM.'<table border=0 align=center cellspacing=0 cellpadding=1>
					<tr class=tablehead><td>Status</td><td>Date sent</td><td>Subject</td><td>From</td></tr>';
				$checknum = 0;

				while($msg = mysql_fetch_object($res)) {

					echo '<tr><td>';

					// Display checkbox for deleting message
					echo '<input type=checkbox name=del'.$checknum.' value='.$msg->msgid.'>';

					$checknum++;

					// New messages are bold, so we prepare some stuff
					$bb = '';
					$be = '';

					switch($msg->status) {
						case 'new':
							echo '<img src="images/msgnew.gif">';
							$bb = '<b>';
							$be = '</b>';
							break;
						case 'read':
							echo '<img src="images/msgread.gif">';
							break;
						case 'replied':
							echo '<img src="images/msgreplied.gif">';
							break;
					}

					if($msg->team == 'yes') {
							echo '<img src="images/team.gif">';
					}
					echo '</td><td align=center><font size=-2 color=#3333CC>'.$bb.$msg->datesent.$be.'</font>&nbsp;&nbsp;</td>';
					if($msg->subject == '') {
							$subject = 'No subject';
					} else {
							$subject = stripslashes($msg->subject);
					}
					echo '<td><a href="index.php?link=messages&read='.$msg->msgid.'&'.SID.'">'.wordwrap($subject, 40, '<br>').'</a></td>';
					if($msg->sender == '') {
									// Administrative message
									echo '<td><b>CTF League System</b></td></tr>';
					} else {
									echo '<td><a href="index.php?link=playerinfo&id='.$msg->fromid.'&'.SID.'">'.$msg->sender.'</a></td></tr>';
					}
				}
			echo '</table><br><center>
					<input type=submit name=delbulk value="Delete Checked">
					<input type=hidden name=link value="messages">
					<input type=hidden name=checknum value='.$checknum.'
					</center></form>';
			}
		}
	} else {
		echo "You are not allowed to view the messages, please stop playing with the headers.";
	}
}
?>
