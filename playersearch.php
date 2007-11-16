<? include('header.inc'); ?>

<form method="get" action="<?=$_SERVER['PHP_SELF']?>">
<p>Callsign</p>
<p><input type="text" name="callsign" <?=((isset($callsign)) ? "value=\"" . htmlentities($callsign) . "\" " : '')?>/><input type="submit" /></p>
</form>

<? if (isset($callsign)) {

	$safecallsign = $callsign;
	$safepage = $page;

	$page = (isset($page) ? $page : '1');

//  following query WAY too long (changed by menotume 6/8/2007)
//	$query = "SELECT callsign, lastserver FROM `playerinfo` where `callsign` LIKE '%$safecallsign%' ORDER BY `callsign` ";
	$safecallsign = str_replace ('%', '', $callsign);
	if (!empty($safecallsign)) {
		$query = "SELECT callsign, lastserver FROM `playerinfo` where `callsign` LIKE '$safecallsign%' ORDER BY `callsign` ";
		$numrows = num_rows($query);
	} else {
		$numrows = -1;
	}

	if ($numrows > 0) {
		if ($player = mysql_query ("$query LIMIT ".(($page-1)*100).",100")) {
?>
<p class="pages">Go to: <?=(($page<2)?$i18n[Previous]:"<a href=\"$PHP_SELF?callsign=".urlencode($callsign)."&amp;page=".($page-1)."\">$i18n[Previous]</a>")?> <?

			$numpages = ceil($numrows/100);
			for ($i = 1; $i <= $numpages; $i++) {
				echo (($i == $page) ? "$i " : "<a href=\"$PHP_SELF?callsign=$callsign&amp;page=$i\">$i</a>\n");
			}
?><?=(($page>=$numpages)?$i18n[Next]:"<a href=\"$PHP_SELF?callsign=".urlencode($callsign)."&amp;page=".($page+1)."\">$i18n[Next]</a>")?></p>

<table>
<caption>Players</caption>
<tr class="tableheader"><td>Callsign</td><td>Last Server</td></tr>
<?
			while ($row = mysql_fetch_array($player, MYSQL_ASSOC)) {
?>
<tr class="oddrow"><td><?=callsignlink($row[callsign])?></td><td><?=serverlink($row[lastserver])?></td></tr>
<?
				if ($row = mysql_fetch_array($player, MYSQL_ASSOC)) {
?>
<tr class="evenrow"><td><?=callsignlink($row[callsign])?></td><td><?=serverlink($row[lastserver])?></td></tr>
<?
				}
			}
?>
</table>

<p class="pages">Go to: <?=(($page<2)?$i18n[Previous]:"<a href=\"$PHP_SELF?callsign=".urlencode($callsign)."&amp;page=".($page-1)."\">$i18n[Previous]</a>")?> <?

			$numpages = ceil($numrows/100);
			for ($i = 1; $i <= $numpages; $i++) {
				echo (($i == $page) ? "$i " : "<a href=\"$PHP_SELF?callsign=$callsign&amp;page=$i\">$i</a>\n");
			}
?><?=(($page>=$numpages)?$i18n[Next]:"<a href=\"$PHP_SELF?callsign=".urlencode($callsign)."&amp;page=".($page+1)."\">$i18n[Next]</a>")?></p><?
		}
	} else if ($numrows < 0) {
?>
<p><b style="color:#f00;">Please enter a callsign!</b></p>
<?
	} else {
?>
<p><b>No player by the name '<?=htmlentities($callsign)?>'</b></p>
<?
	}
}

include('footer.inc');

?>
