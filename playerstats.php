<? include('header.inc') ?>

<ul>
<li><a href="#halloffame">Hall of Fame</a>

<ul>
<li><a href="#highscores">High Scores</a></li>
<li><a href="#highstrengthindexes">High Strength Indexes</a> <a class="help" href="help.php#strengthindex">?</a></li>
</ul>
</li>

<li><a href="#hallofshame">Hall of Shame</a>

<ul>
<li><a href="#lowscores">Low Scores</a></li>
<li><a href="#lowstrengthindexes">Low Strength Indexes</a> <a class="help" href="help.php#strengthindex">?</a></li>
</ul>
</li>

</ul>

<a id="halloffame"></a>
<h2>Hall of Fame</h2>

<a id="highscores"></a>

<table>
<caption>High Scores</caption>
<tr class="tableheader">
<td>Callsign</td><td>Score</td><td>Strength Index <a class="help" href="help.php#strengthindex">?</a></td></tr>
<?

$highscore = mysql_query ("SELECT callsign,highscore,highstrengthindex FROM `playerinfo` ORDER BY `highscore` DESC LIMIT 20");

while ($row = mysql_fetch_array($highscore, MYSQL_ASSOC)) {
?>
<tr class="oddrow"><td><?=callsignlink($row[callsign])?> </td>
<td class="score"><?=$row[highscore]?>     </td>
<td class="strengthindex"><?=number_format($row[highstrengthindex], 3)?> </td></tr>

<? 
	if ($row = mysql_fetch_array($highscore, MYSQL_ASSOC)) {
?>
<tr class="evenrow"><td><?=callsignlink($row[callsign])?> </td>
<td class="score"><?=$row[highscore]?>     </td>
<td class="strengthindex"><?=number_format($row[highstrengthindex], 3)?> </td></tr>

<? 
	}
}
?>
</table>

<a id="highstrengthindexes"></a>

<table>
<caption>High Strength Indexes <a class="help" href="help.php#strengthindex">?</a></caption>
<tr class="tableheader">
<td>Callsign</td><td>Score</td><td>Strength Index <a class="help" href="help.php#strengthindex">?</a></td></tr>
<?

$highstrengthindex = mysql_query ("SELECT callsign,highscore,highstrengthindex FROM `playerinfo` ORDER BY `highstrengthindex` DESC LIMIT 20");

while ($row = mysql_fetch_array($highstrengthindex, MYSQL_ASSOC)) {
?>
<tr class="oddrow"><td><?=callsignlink($row[callsign])?> </td>
<td class="score"><?=$row[highscore]?>     </td>
<td class="strengthindex"><?=number_format($row[highstrengthindex], 3)?> </td></tr>

<? 
	if ($row = mysql_fetch_array($highstrengthindex, MYSQL_ASSOC)) {
?>
<tr class="evenrow"><td><?=callsignlink($row[callsign])?> </td>
<td class="score"><?=$row[highscore]?>     </td>
<td class="strengthindex"><?=number_format($row[highstrengthindex], 3)?> </td></tr>

<? 
	}
}
?>
</table>

<a id="hallofshame"></a>
<h2>Hall of Shame</h2>

<a id="lowscores"></a>

<table>
<caption>Low Scores</caption>
<tr class="tableheader">
<td>Callsign</td><td>Score</td><td>Strength Index <a class="help" href="help.php#strengthindex">?</a></td></tr>
<?

$lowscore = mysql_query ("SELECT callsign,lowscore,lowstrengthindex FROM `playerinfo` ORDER BY `lowscore` LIMIT 20");

while ($row = mysql_fetch_array($lowscore, MYSQL_ASSOC)) {
?>
<tr class="oddrow"><td><?=callsignlink($row[callsign])?> </td>
<td class="score"><?=$row[lowscore]?>     </td>
<td class="strengthindex"><?=number_format($row[lowstrengthindex], 3)?> </td></tr>

<? 
	if ($row = mysql_fetch_array($lowscore, MYSQL_ASSOC)) {
?>
<tr class="evenrow"><td><?=callsignlink($row[callsign])?> </td>
<td class="score"><?=$row[lowscore]?>     </td>
<td class="strengthindex"><?=number_format($row[lowstrengthindex], 3)?> </td></tr>

<? 
	}
}
?>
</table>

<a id="lowstrengthindexes"></a>

<table>
<caption>Low Strength Indexes <a class="help" href="help.php#strengthindex">?</a></caption>
<tr class="tableheader">
<td>Callsign</td><td>Score</td><td>Strength Index <a class="help" href="help.php#strengthindex">?</a></td></tr>
<?

$lowstrengthindex = mysql_query ("SELECT callsign,lowscore,lowstrengthindex FROM `playerinfo` ORDER BY `lowstrengthindex` LIMIT 20");

while ($row = mysql_fetch_array($lowstrengthindex, MYSQL_ASSOC)) {
?>
<tr class="oddrow"><td><?=callsignlink($row[callsign])?> </td>
<td class="score"><?=$row[lowscore]?>     </td>
<td class="strengthindex"><?=number_format($row[lowstrengthindex], 3)?> </td></tr>

<? 
	if ($row = mysql_fetch_array($lowstrengthindex, MYSQL_ASSOC)) {
?>
<tr class="evenrow"><td><?=callsignlink($row[callsign])?> </td>
<td class="score"><?=$row[lowscore]?>     </td>
<td class="strengthindex"><?=number_format($row[lowstrengthindex], 3)?> </td></tr>

<? 
	}
}
?>
</table>



<? include('footer.inc'); ?>
