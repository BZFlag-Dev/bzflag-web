<h2>Hall of Fame</h2>

<table>
<tr>
  <th colspan="2">High Scores</th>
  <th colspan="2">High Strength Indexes <a class="help" href="help.php#strengthindex">?</a></th>
</tr>
<tr class="tableheader">
  <td>Callsign</td>
  <td>Score</td>
  <td>Callsign</td>
  <td>Strength Index</td>
</tr>

{section name=halloffame start=0 loop=20 step=1}
<tr class="{cycle values="odd,even"}row">
{  if $data.playershighscore[$smarty.section.halloffame.index]}
  <td><a href="playerinfo.php?callsign={$data.playershighscore[$smarty.section.halloffame.index].callsign|escape:"url"}">{$data.playershighscore[$smarty.section.halloffame.index].callsign|escape}</a></td>
  <td class="score">{$data.playershighscore[$smarty.section.halloffame.index].highscore}</td>
{  else}
  <td>&nbsp;</td>
  <td>&nbsp;</td>
{  /if}

{  if $data.playershighstrength[$smarty.section.halloffame.index]}
  <td><a href="playerinfo.php?callsign={$data.playershighstrength[$smarty.section.halloffame.index].callsign|escape:"url"}">{$data.playershighstrength[$smarty.section.halloffame.index].callsign|escape}</a></td>
  <td class="strengthindex">{$data.playershighstrength[$smarty.section.halloffame.index].highstrengthindex|string_format:"%.3f"}</td>
{  else}
  <td>&nbsp;</td>
  <td>&nbsp;</td>
{  /if}
</tr>
{/section}
</table>

<h2>Hall of Shame</h2>

<table>
<tr>
  <th colspan="2">Low Scores</th>
  <th colspan="2">Low Strength Indexes <a class="help" href="help.php#strengthindex">?</a></th>
</tr>
<tr class="tableheader">
  <td>Callsign</td>
  <td>Score</td>
  <td>Callsign</td>
  <td>Strength Index</td>
</tr>

{section name=hallofshame start=0 loop=20 step=1}
<tr class="{cycle values="odd,even"}row">
{  if $data.playerslowscore[$smarty.section.hallofshame.index]}
  <td><a href="playerinfo.php?callsign={$data.playerslowscore[$smarty.section.hallofshame.index].callsign|escape:"url"}">{$data.playerslowscore[$smarty.section.hallofshame.index].callsign|escape}</a></td>
  <td class="score">{$data.playerslowscore[$smarty.section.hallofshame.index].lowscore}</td>
{  else}
  <td>&nbsp;</td>
  <td>&nbsp;</td>
{  /if}

{  if $data.playerslowstrength[$smarty.section.hallofshame.index]}
  <td><a href="playerinfo.php?callsign={$data.playerslowstrength[$smarty.section.hallofshame.index].callsign|escape:"url"}">{$data.playerslowstrength[$smarty.section.hallofshame.index].callsign|escape}</a></td>
  <td class="strengthindex">{$data.playerslowstrength[$smarty.section.hallofshame.index].lowstrengthindex|string_format:"%.3f"}</td>
{  else}
  <td>&nbsp;</td>
  <td>&nbsp;</td>
{  /if}
</tr>
{/section}
</table>
