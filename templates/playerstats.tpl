<h2>Hall of Fame</h2>

<table>
<tr>
  <th colspan="2">High Scores</th>
  <th colspan="2">High Strength Indexes <a class="help" href="{$page.baseURL}help.php#strengthindex">?</a></th>
</tr>
<tr class="tableheader">
  <td>Callsign</td>
  <td>Score</td>
  <td>Callsign</td>
  <td>Strength Index</td>
</tr>

{section name=halloffame start=0 loop=20 step=1}
{assign var=playerhighscore value=$data.playershighscore[$smarty.section.halloffame.index]}
<tr class="{cycle values="odd,even"}row">
{  if $playerhighscore}
  <td><a href="{$page.baseURL}playerinfo.php?callsign={$playerhighscore.callsign|escape:"url"}">{$playerhighscore.callsign|escape}</a></td>
  <td class="score">{$playerhighscore.highscore}</td>
{  else}
  <td>&nbsp;</td>
  <td>&nbsp;</td>
{  /if}

{assign var=playerhighstrength value=$data.playershighstrength[$smarty.section.halloffame.index]}
{  if $playerhighstrength}
  <td><a href="{$page.baseURL}playerinfo.php?callsign={$playerhighstrength.callsign|escape:"url"}">{$playerhighstrength.callsign|escape}</a></td>
  <td class="strengthindex">{$playerhighstrength.highstrengthindex|string_format:"%.3f"}</td>
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
  <th colspan="2">Low Strength Indexes <a class="help" href="{$page.baseURL}help.php#strengthindex">?</a></th>
</tr>
<tr class="tableheader">
  <td>Callsign</td>
  <td>Score</td>
  <td>Callsign</td>
  <td>Strength Index</td>
</tr>

{section name=hallofshame start=0 loop=20 step=1}
{assign var=playerlowscore value=$data.playerslowscore[$smarty.section.hallofshame.index]}
<tr class="{cycle values="odd,even"}row">
{  if $playerlowscore}
  <td><a href="{$page.baseURL}playerinfo.php?callsign={$playerlowscore.callsign|escape:"url"}">{$playerlowscore.callsign|escape}</a></td>
  <td class="score">{$playerlowscore.lowscore}</td>
{  else}
  <td>&nbsp;</td>
  <td>&nbsp;</td>
{  /if}

{assign var=playerlowstrength value=$data.playerslowstrength[$smarty.section.hallofshame.index]}
{  if $data.playerslowstrength[$smarty.section.hallofshame.index]}
  <td><a href="{$page.baseURL}playerinfo.php?callsign={$playerlowstrength.callsign|escape:"url"}">{$playerlowstrength.callsign|escape}</a></td>
  <td class="strengthindex">{$playerlowstrength.lowstrengthindex|string_format:"%.3f"}</td>
{  else}
  <td>&nbsp;</td>
  <td>&nbsp;</td>
{  /if}
</tr>
{/section}
</table>
