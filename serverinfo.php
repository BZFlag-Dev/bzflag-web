<? include('header.inc');

function yn_img ($yn = 'N') {
  if ($yn == 'Y') {
    return '<img src="images/check.png" alt="*" />';
  } else {
    return '<img src="images/x.png" alt="X" />';
  }
}

?>

<h2>Current info for <?=$server?></h2>

<?

$safeserver = mysql_escape_string($server);

$serverinfo = mysql_query ("SELECT * FROM `serverinfo` WHERE `server` = '$safeserver' LIMIT 1");
if ($row = mysql_fetch_array($serverinfo, MYSQL_ASSOC)) {

  ?>
 <table>

    <caption>Server Configuration</caption>

    <tr class="evenrow">
    <td align="right"><?=$row['players']?><?=($row['maxplayers'] == 200) ? '' : "/$row[maxplayers]" ?></td>
    <td><strong>Players</strong></td>
    <td><img src="images/check.png" alt="*" /> <strong><?=$row[maxshots]?> <?=($row['maxshots'] == 1 ? "Shot" : "Shots")?></strong></td>
    <td><?=yn_img($row[jumping])?> <strong>Jumping</strong></td>
    </tr>

    <tr class="oddrow">
    <td align="right"><?=$row['roguesize']?><?=($row['roguemax'] == 200) ? '' : "/$row[roguemax]" ?></td>
    <td><?=teamname('X')?></td>
    <td><img src="images/check.png" alt="*" /> <?=($row['rabbitchase'] == 'Y' ? '<span class="teamrabbit">Rabbit Chase</span>' : ($row['ctf'] == 'Y' ? '<span class="teamred">Capture-the-Flag</span>' : '<span class="teamrogue">Free-style</span>'))?></td>
    <td><?=yn_img($row[ricochet])?> <strong>Ricochet</strong></td>
    </tr>

    <tr class="evenrow">
    <td align="right"><?=$row['redsize']?><?=($row['redmax'] == 200) ? '' : "/$row[redmax]" ?></td>
    <td><?=teamname('R')?></td>
    <td><?=yn_img($row[superflags])?> <strong>Super Flags</strong></td>
    <td><?=yn_img($row[inertia])?> Inertia</td>
    </tr>

    <tr class="oddrow">
    <td align="right"><?=$row['greensize']?><?=($row['greenmax'] == 200) ? '' : "/$row[greenmax]" ?></td>
    <td><?=teamname('G')?></td>
    <td><?=yn_img($row[antidoteflags])?> Antidote Flags</td>
    <td><?=yn_img($row[timesync])?> Time Sync</td>
    </tr>

    <tr class="evenrow">
    <td align="right"><?=$row['bluesize']?><?=($row['bluemax'] == 200) ? '' : "/$row[bluemax]" ?></td>
    <td><?=teamname('B')?></td>
    <td><?=($row[shakewins] == 1 ? '<img src="images/check.png" alt="*" /> 1 Win Drops Bad Flag' : ($row[shakewins] == 0 ? '<img src="images/x.png" alt="X" /> No Wins Drop Bad Flag' : "<img src=\"images/check.png\" alt=\"*\" /> $row[shakewins] Drop Bad Flag"))?></td>
    <td><?=($row[maxteamscore] == 0 ? '<img src="images/x.png" alt="X" /> Max Team Score: None' : "<img src=\"images/check.png\" alt=\"*\" /> Max Team Score: $row[maxteamscore]")?></td>
    </tr>

    <tr class="oddrow">
    <td align="right"><?=$row['purplesize']?><?=($row['purplemax'] == 200) ? '' : "/$row[purplemax]" ?></td>
    <td><?=teamname('P')?></td>
    <td><?=($row[shaketimeout] == 0 ? '<img src="images/x.png" alt="X" /> No secs to Drop Bad Flag' : sprintf('<img src="images/check.png" alt="*" /> %.1f secs to Drop Bad Flag', $row[shaketimeout]))?></td>
    <td><?=($row[maxplayerscore] == 0 ? '<img src="images/x.png" alt="X" /> Max Player Score: None' : "<img src=\"images/check.png\" alt=\"*\" /> Max Player Score: $row[maxplayerscore]")?></td>
    </tr>

    <tr class="evenrow">
    <td align="right"><?=$row['observersize']?><?=($row['observermax'] == 200) ? '' : "/$row[observermax]" ?></td>

    <td><strong>Observers</strong></td>
    <td><?=($row[maxtime] == 0 ? '<img src="images/x.png" alt="X" /> Time Limit: None' : sprintf('<img src="images/check.png" alt="*" /> Time Limit: %d:%2d', floor($row[maxtime]/60), fmod($row[maxtime], 60)))?></td>
    <td></td>

    </table>

    <?


$teams = mysql_query ("SELECT * FROM `teaminfo` WHERE `server` = '$safeserver' AND `team` != 'X' ORDER BY `score` DESC");

if (mysql_num_rows($teams) > 0) {
  ?>
 <table>

    <caption>Team Info</caption>

    <tr class="tableheader">
    <td>Team</td><td>Score</td>
    <td>Wins - Losses</td></tr>
    <?
    while ($row = mysql_fetch_array($teams, MYSQL_ASSOC)) {
      ?>
      <tr class="oddrow"><td><?=teamname($row[team])?> </td>
      <td class="score"><?=$row[score]?>    </td>
      <td><?=$row[won]?>-<?=$row[lost]?>     </td></tr>

      <?
      if ($row = mysql_fetch_array($teams, MYSQL_ASSOC)) {
	?>
	<tr class="evenrow"><td><?=teamname($row[team])?> </td>
	<td class="score"><?=$row[score]?>    </td>
	<td><?=$row[won]?>-<?=$row[lost]?>     </td></tr>

	<?
      }
    }

 mysql_free_result($teams);

    ?>
   </table>

       <table>

       <caption>Players</caption>
       <tr class="tableheader">
       <td>Callsign</td><td>Team</td><td>Score</td></tr>
       <?

       $safeserver = mysql_escape_string($server);

 $players = mysql_query ("SELECT * FROM `currentplayers` WHERE `server` = '$safeserver' AND `team` != 'O' ORDER BY `score` DESC");

 while ($row = mysql_fetch_array($players, MYSQL_ASSOC)) {

  ?>
 <tr class="oddrow">
    <td><?=callsignlink($row[callsign], $row[email])?></td>
    <td><?=teamname($row[team])?> </td>
    <td class="score"><?=$row[score]?>    </td>
    </tr>

    <?
    if ($row = mysql_fetch_array($players, MYSQL_ASSOC)) {
      ?>
      <tr class="evenrow">
      <td><?=callsignlink($row[callsign], $row[email])?></td>
      <td><?=teamname($row[team])?> </td>
      <td class="score"><?=$row[score]?>    </td>
      </tr>

      <?
    }
 }
    ?></table>

	  <?
	  $query = "SELECT * FROM `currentplayers` WHERE `server` = '$safeserver' AND `team` = 'O' ORDER BY `callsign`";

 if (num_rows($query) > 0) {
  ?>
    <table>
       <caption>Observers</caption>
    <tr class="tableheader"><td>Callsign</td><td>Team</td></tr>
    <?

    $players = mysql_query ($query);

 while ($row = mysql_fetch_array($players, MYSQL_ASSOC)) {

  ?>
 <tr class="oddrow">
    <td><?=callsignlink($row[callsign], $row[email])?></td>
    <td><?=teamname($row[team])?> </td>
    </tr>

    <?
    if ($row = mysql_fetch_array($players, MYSQL_ASSOC)) {
      ?>
      <tr class="evenrow">
      <td><?=callsignlink($row[callsign], $row[email])?></td>
      <td><?=teamname($row[team])?> </td>
      </tr>

      <?
    }
 }
    ?></table><?
	  }

} else {
  ?>
 <p>No player info available for <strong><?=$server?></strong></p>
					  <? }

				  } else {
  ?><p>No game info available for <strong><?=$server?></strong></p><?
					   }

include('footer.inc'); ?>
