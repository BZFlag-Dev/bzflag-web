<?php
function section_bzforums(&$templ)
{
	require_once('lib/common.php');

	// forumid
	// addpost
	// f_cancel_x
	// f_ok_x
	// threadid
	// top
	$POSTSPERPAGE	= 10;
	$link			= 'bzforums';

	$vars = array('top','threadid','addpost','forumid','subject','f_ok_x','f_cancel_x','forummsg');
	foreach($vars as $var)
		$$var = (isset($_POST[$var]) ? $_POST[$var] : $_GET[$var]);

	if(isset($forumid)) {
		// Get and print the forum title
		$row = mysql_fetch_object(mysql_query("select title, status from l_forum where id=$forumid"));
		echo 'Forum:
		<a href="index.php?link='.$link.'&'.SID.'"><font size=+1>BZforums</font></a><font size=+1><i>&nbsp;/&nbsp;</i></font>
		<a href="index.php?link='.$link.'&forumid='.$forumid.'&'.SID.'"><font size=+1>'.$row->title.'</font></a><hr>';
		if( $row->status != 'Open' )
		{
			print "This forum isnt accessible. Sorry";
			return;
		}
	}
	if($_SESSION['authenticated'] && isset($f_cancel_x)) {
		unset($addpost);
	}
	if($_SESSION['authenticated'] && isset($f_ok_x)) {
		unset($addpost);
		// Trim forummsg (remove and whitespaces before/after the text)
		$forummsg = trim($forummsg);
		// Make sure that only 1 or 2 newlines after eachother. If more, strip 'em.
		$forummsg = preg_replace('/(\n|\r|\r\n){3,}/', '$1$1', $forummsg );
		// Insert message

		if(isset($threadid)) {
			// Add reply
			$now = gmdate("Y-m-d H:i:s");
			mysql_query("insert into l_forummsg values(0, $threadid, {$_SESSION['playerid']}, '".$forummsg."', '$now')");
		} else {
			// New topic
			mysql_query("insert into l_forumthread values(0, $forumid, {$_SESSION['playerid']}, '".$subject."')");
			$threadid = mysql_insert_id();
			$now = gmdate("Y-m-d H:i:s");
			mysql_query("insert into l_forummsg values(0, $threadid, {$_SESSION['playerid']}, '".$forummsg."', '$now')");
		}
	}
	if($_SESSION['authenticated'] && isset($addpost)) {
		echo '<form method=post>'.SID_FORM;
		echo '<input type=hidden name=link value="'.$link.'">';
		echo '<input type=hidden name=top value='.$top.'>';
		echo '<table border=0 align=center cellspacing=0 cellpadding=0>';

		if(isset($threadid)) {
			// Reply to a post
			// Get subject
			$row = mysql_fetch_object(mysql_query("select subject from l_forumthread where id=$threadid"));
			echo '<tr class=tablehead><td colspan=2 align=center>Replying to <i>'.$row->subject.'</i></td></tr>';
			echo '<input type=hidden name=threadid value='.$threadid.'>';
		} else {
			// Create a new topic
			echo '<tr class=tablehead><td colspan=2 align=center>New Topic</td></tr>';
			echo '<tr><td>Subject</td><td><input type=text name=subject size=60 maxlength=80></td></tr>';
		}

		echo '<input type=hidden name=forumid value='.$forumid.'>';
		echo '<tr><td colspan=2><textarea cols=50 rows=10 name=forummsg></textarea></td></tr>';

		// Form buttons
		echo '<tr><td colspan=2 align=center>
		<input type=image src="images/ok.gif" name=f_ok>
		&nbsp;
		<input type=image src="images/cancel.gif" name=f_cancel>
		</td></tr>';
		echo '</table></form>';
	}
	if(isset($threadid)) {
		// Display a thread
		// Control buttons
		// 06/28/2002: $POSTSPERPAGE posts/page max.

		if(!isset($top)) {
			$top = 0;
			$newtop = 0;
		}

		if($top == "") {
			$top = 0;
			$newtop = 0;
		}

		if($_SESSION['authenticated'] && (!isset($addpost))) {
			echo '<div align=right>
			<a href="index.php?link='.$link.'&forumid='.$forumid.'&addpost=1&'.SID.'"><img border=0 src="images/newtopic.gif"></a>
			&nbsp;
			<a href="index.php?link='.$link.'&forumid='.$forumid.'&threadid='.$threadid.'&addpost=1&top='.$top.'&'.SID.'"><img border=0 src="images/addreply.gif"></a>
			</div>';
		}
		$temp = mysql_fetch_object(mysql_query("select count(msgid) num from l_forummsg where threadid=$threadid"));
		$numpost = $temp->num;
		$numpages = floor(($numpost + $POSTSPERPAGE - 1) / $POSTSPERPAGE);
		$curpage = $top / $POSTSPERPAGE + 1;
		$res = mysql_query("select msgid, msg, datesent, l_player.id pid, l_player.callsign
			from l_forummsg, l_player
			where threadid=$threadid
			and fromid = l_player.id
			order by datesent
			limit $top, $POSTSPERPAGE");

		// Table header
		$sub = mysql_fetch_object(mysql_query("select subject from l_forumthread where id=$threadid"));
		echo '<table align=center border=1 cellspacing=0 cellpadding=1>';
		echo '<tr class=tablehead><td colspan=3><font size=+1><i>'.smileys($sub->subject).'</i></font>';

		// Display pages if multiple pages and not adding a post
		if(($numpages > 1) && (!isset($addpost))) {
			echo '<div align=right>';
			for($i = 1; $i <= $numpages; $i++) {
					if($i == $curpage) {
							echo $i.'&nbsp;';
					} else {
							$newtop = ($i - 1) * $POSTSPERPAGE;
							echo '<a href="index.php?link='.$link.'&forumid='.$forumid.'&threadid='.$threadid.'&top='.$newtop.'&'.SID.'">'.$i.'</a>&nbsp;';
					}
			}
			echo '</div>';
		}
		echo '</td></tr>';

		if( $_SESSION['level'] == 'admin' )
			echo '<tr class=tablehead><td>id</td><td>Author</td><td>Comment</td></tr>';
		else
			echo '<tr class=tablehead><td>Author</td><td>Comment</td></tr>';

		$cf = 1;
		while($row = mysql_fetch_object($res)) {
			echo '<tr class=forum'.$cf.'>';

			if( $_SESSION['level'] == 'admin' )
				echo '<td align="right">' . $row->msgid . '</td>';

			echo '
			<td valign=top><a href="index.php?link=playerinfo&id='.$row->pid.'&'.SID.'">'.$row->callsign.'</a><br><font color=#0000e0 size=-3>('.$row->datesent.')</font></td>
			<td valign=top>'.smileys(nl2br(htmlspecialchars($row->msg))).'</td>
			</tr>';
			$cf = 3 - $cf;
		}
		echo '</table>';
	} else {
		if(isset($forumid)&&(!isset($addpost))) {
			// Display specific forum
			// Control buttons
			if($_SESSION['authenticated']) {
				echo '<div align=right>
				<a href="index.php?link='.$link.'&forumid='.$forumid.'&addpost=1&'.SID.'"><img border=0 src="images/newtopic.gif"></a>
				</div>';
			}
			$res = mysql_query("select l_forumthread.id,l_forumthread.subject, l_player.id pid, l_player.callsign, max(l_forummsg.datesent) ds, unix_timestamp(max(l_forummsg.datesent)) datesent_ts
				from l_forumthread, l_forummsg, l_player, l_player l_player2
				where l_forumthread.forumid=$forumid
				and l_forumthread.creatorid = l_player.id
				and l_forumthread.id = l_forummsg.threadid
				and l_player2.id = l_forummsg.fromid
				group by l_forumthread.id, l_forumthread.subject, l_player.id, l_player.callsign
				order by ds desc");
			// Table header
			echo '<table align=center border=1 cellspacing=0 cellpadding=1>';
			echo '<tr class=tablehead><td width=50%>Topic</td><td align=center>Replies</td><td align=center>Last Comment</td><td align=center>Started by</td></tr>';

			while($row = mysql_fetch_object($res)) {
				// Get the last author for this topic
				$la = mysql_fetch_object(mysql_query("select l_player.id pid, l_player.callsign
					from l_player, l_forummsg
					where threadid = $row->id
					and fromid = l_player.id
					order by datesent desc
					limit 0, 1"));

				$nr = mysql_fetch_object(mysql_query("select count(*)-1 num
					from l_forummsg
					where threadid = ".$row->id));
				echo '<tr><td><a href="index.php?link='.$link.'&forumid='.$forumid.'&threadid='.$row->id.'&'.SID.'">';

				if( empty($row->subject) )
					$row->subject = '(no subject)';

				if( isset($_SESSION['last_login']) && ($_SESSION['last_login'] < $row->datesent_ts ))
					echo '<font color="red"><b>' . smileys($row->subject).'</b></font></a>';
				else
					echo smileys($row->subject).'</a>';

				$numpages = floor(($nr->num + 1 + $POSTSPERPAGE - 1) / $POSTSPERPAGE);

				if($numpages > 1) {
					echo '&nbsp;(<img src="images/multipage.gif">&nbsp;';
					for($i = 2; $i <= $numpages; $i++) {
						$t = ($i - 1) * 10;
						echo '<a href="index.php?link='.$link.'&top='.$t.'&forumid='.$forumid.'&threadid='.$row->id.'&'.SID.'">'.$i.'</a>&nbsp;';
					}
					echo ')';
				}
				echo '</td>
				<td align=center>'.$nr->num;
				echo '</td>
				<td align=center>'.$row->ds.'<br>by <a href="index.php?link=playerinfo&id='.$la->pid.'&'.SID.'">'.$la->callsign.'</a></td>
				<td align=center><a href="index.php?link=playerinfo&id='.$row->pid.'&'.SID.'">'.$row->callsign.'</a></td>
				</tr>';
			}
			echo '</table>';
		} elseif (!isset($addpost)) {
			// Display forums list
			$res = mysql_query("select l_forum.id, l_forum.title, ifnull(max(l_forummsg.datesent),'n/a') md, count(l_forummsg.msgid) num,
					unix_timestamp(max(l_forummsg.datesent)) as datesent_ts
					from l_forum
					left join l_forumthread on l_forum.id = l_forumthread.forumid
					left join l_forummsg on l_forumthread.id = l_forummsg.threadid
					where status = 'open'
					group by l_forum.id, l_forum.title
					order by title");
			echo '<table align=center border=0 cellspacing=0 cellpadding=1>
			<tr><td class=tablehead>Forums</td><td align=center class=tablehead># Posts</td><td align=center class=tablehead>Last Comment</td></tr>';
			while($row = mysql_fetch_object($res)) {
				echo '<tr><td><a href="index.php?link='.$link.'&forumid='.$row->id.'&'.SID.'">';

				if( isset($_SESSION['last_login']) && ($_SESSION['last_login'] < $row->datesent_ts ))
					echo "<font color=\"red\">" . $row->title . "</font>";
				else
					echo $row->title;

				echo '</a></td><td align=center>'.$row->num.'</td><td align=center>'.$row->md.'</td></tr>';
				echo '<tr><td align=center colspan=3><hr></td></tr>';
			}
			echo '</table>';
		}
	}
}
?>
