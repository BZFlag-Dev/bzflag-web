<? include('header.inc') ?>

<h2>Stats for player <?=$callsign?></h2>

<?

#$safecallsign = mysql_escape_string($callsign);
$safecallsign = $callsign;

$player = mysql_query ("SELECT * FROM `playerinfo` where `callsign` = '$safecallsign'");

if ($row = mysql_fetch_array($player, MYSQL_ASSOC)) {
?>
<table>
<caption>Player Info</caption>
<tr class="evenrow"><td><strong>Email String</strong></td><td><?=$row[email]?></td></tr>
<tr class="oddrow"><td><strong>High Score</strong></td><td><?=$row[highscore]?></td></tr>
<tr class="evenrow"><td><strong>Low Score</strong></td><td><?=$row[lowscore]?></td></tr>
<tr class="oddrow"><td><strong>High Strength Index</strong></td><td><?=$row[highstrengthindex]?></td></tr>
<tr class="evenrow"><td><strong>Low Strength Index</strong></td><td><?=$row[lowstrengthindex]?></td></tr>
<tr class="oddrow"><td><strong>High Kill Ratio</strong></td><td><?=$row[highkillratio]?></td></tr>
<tr class="evenrow"><td><strong>Low Kill Ratio</strong></td><td><?=$row[lowkillratio]?></td></tr>
<tr class="oddrow"><td><strong>Most Wins</strong></td><td><?=$row[mostwins]?></td></tr>
<tr class="evenrow"><td><strong>Most Losses</strong></td><td><?=$row[mostlosses]?></td></tr>
<tr class="oddrow"><td><strong>Last Seen</strong></td><td><code><?=date('D M d H:i:s T Y', $row[lastseen])?><br />
			                       <?=gmdate('D M d H:i:s T Y', $row[lastseen])?></code></td></tr>
<tr class="evenrow"><td><strong>Last Server</strong></td><td><?=serverlink($row[lastserver])?> </td></tr>
</table>
<? } else { ?>
<p>No info available for player <strong><?=$callsign?></strong></p>
<? }

include ('footer.inc');

?>
