<? include ('header.inc') ?>

<h2>Information sampled every 5 minutes</h2>

<?

$query = "SELECT `server`, `players`, `ctf`, `rabbitchase`, `description` FROM `serverinfo` ORDER BY `players` DESC";

if (num_rows($query) > 0) {

?>
<table id="currentplayers">
<caption>Servers</caption>
<tr class="tableheader">
<td>Server</td>
<td>Players</td>
<td>Game Style</td>
<td>Description</td>
</tr>

<?

	$serverinfo = mysql_query ($query);


	while ($row = mysql_fetch_array($serverinfo, MYSQL_ASSOC)) {

?>
<tr class="oddrow"><td><?=serverlink($row[server])?></td>
<td align="right"><?=($row[players] > 0 ? "<strong>$row[players]</strong>" : $row[players])?></td>
   <td><?=($row['rabbitchase'] == 'Y' ? '<span class="teamrabbit">Rabbit Chase</span>' : ($row['ctf'] == 'Y' ? '<span class="teamred">Capture-the-Flag</span>' : '<span class="teamrogue">Free-style</span>'))?>
<td><?=preg_replace('/;/', '; ', $row[description])?></td>
</tr>
<?
		if ($row = mysql_fetch_array($serverinfo, MYSQL_ASSOC)) {
?>
<tr class="evenrow"><td><?=serverlink($row[server])?></td>
<td align="right"><?=($row[players] > 0 ? "<strong>$row[players]</strong>" : $row[players])?></td>
   <td><?=($row['rabbitchase'] == 'Y' ? '<span class="teamrabbit">Rabbit Chase</span>' : ($row['ctf'] == 'Y' ? '<span class="teamred">Capture-the-Flag</span>' : '<span class="teamrogue">Free-style</span>'))?>
<td><?=preg_replace('/;/', '; ', $row[description])?></td>
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






