<? include ('header.inc') ?>

<h2>Information sampled every 5 minutes</h2>

<?

if (isset($order)) {
	if ($order == 'callsign') { $orderby = "`callsign`";
	} elseif ($order == 'callsigndesc') { $orderby = "`callsign` DESC";
	} elseif ($order == 'server') { $orderby = "`server`";
	} elseif ($order == 'serverdesc') { $orderby = "`server` DESC";
	} elseif ($order == 'score') { $orderby = "`score` DESC";
	} elseif ($order == 'scoreasc') { $orderby = "`score`";
	} elseif ($order == 'strengthindex') { $orderby = "`strengthindex` DESC";
	} elseif ($order == 'strengthindexasc') { $orderby = "`strengthindex`";
	} else { $order == 'callsign'; $orderby = "`callsign`";
	}
} else {
	$order = 'callsign';
	$orderby = '`callsign`';
}


if ((num_rows("SELECT * FROM `currentplayers` ORDER BY $orderby")) > 0) {

?>
<table id="currentplayers">
<caption>Players</caption>
<tr class="tableheader">
<td><a href="<?=$PHP_SELF?>?order=<?=($order == 'callsign')?'callsigndesc':'callsign'?>">Callsign</a>
   <?=(($order == 'callsign')?' <img src="images/uparrow.png" alt="^" />':($order == 'callsigndesc' ? ' <img src="images/downarrow.png" alt="v" />':''))?></td>

<td><a href="<?=$PHP_SELF?>?order=<?=($order == 'server')?'serverdesc':'server'?>">Server</a>
   <?=(($order == 'server')?' <img src="images/uparrow.png" alt="^" />':($order == 'serverdesc' ? ' <img src="images/downarrow.png" alt="v" />':''))?></td>

<td><a href="<?=$PHP_SELF?>?order=<?=($order == 'score')?'scoreasc':'score'?>">Score</a>
   <?=(($order == 'scoreasc')?' <img src="images/uparrow.png" alt="^" />':($order == 'score' ? ' <img src="images/downarrow.png" alt="v" />':''))?></td>

<td><a href="<?=$PHP_SELF?>?order=<?=($order == 'strengthindex')?'strengthindexasc':'strengthindex'?>">Strength Index</a> <a class="help" href="help.php#strengthindex">?</a>
   <?=(($order == 'strengthindexasc')?' <img src="images/uparrow.png" alt="^" />':($order == 'strengthindex' ? ' <img src="images/downarrow.png" alt="v" />':''))?></td>

<td>Team</td>
</tr>

<?

	$currentplayers = mysql_query ("SELECT * FROM `currentplayers` ORDER BY $orderby");


	while ($row = mysql_fetch_array($currentplayers, MYSQL_ASSOC)) {

?>
<tr class="oddrow"><td><?=callsignlink($row[callsign])?></td>
<td><?=serverlink($row[server])?></td>
<td align="right"><?=$row[score]?></td>
<td align="right"><?=number_format($row[strengthindex], 3)?></td>
<td><?=teamname($row[team])?></td>
</tr>
<?
		if ($row = mysql_fetch_array($currentplayers, MYSQL_ASSOC)) {
?>
<tr class="evenrow"><td><?=callsignlink($row[callsign])?></td>
<td><?=serverlink($row[server])?></td>
<td align="right"><?=$row[score]?></td>
<td align="right"><?=number_format($row[strengthindex], 3)?></td>
<td><?=teamname($row[team])?></td>
</tr>
<?
		}
	}
} else {

?>

<p>Oh crap! The monkeys ate the list server!</p>
<?

}

?>
</table>

<? include('footer.inc'); ?>
