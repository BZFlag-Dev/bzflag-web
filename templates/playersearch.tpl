<form method="get" action="{$page.baseURL}playersearch.php">
<p>Callsign:</p>
<p><input type="text" name="callsign"{if $input.callsign} value="{$input.callsign|escape}"{/if}><input type="submit" value="Search"></p>
</form>

{if $input.callsign}

{if $data.playersearch.pages}
<p class="pages">Go to: {if $input.page < 2}Previous{else}<a href="{$page.baseURL}playersearch.php?callsign={$input.callsign|escape:"url"}&amp;page={$input.page-1}">Previous</a>{/if}
{  section name=foo start=1 loop=$data.playersearch.pages+1 step=1}
{    if $smarty.section.foo.index == $input.page}
  {$smarty.section.foo.index}
{    else}
  <a href="{$page.baseURL}playersearch.php?callsign={$input.callsign|escape:"url"}&amp;page={$smarty.section.foo.index}">{$smarty.section.foo.index}</a>
{    /if}
{  /section}
{if $input.page >= $data.playersearch.pages}Next{else}<a href="{$page.baseURL}playersearch.php?callsign={$input.callsign|escape:"url"}&amp;page={$input.page+1}">Next</a></p>{/if}
{/if}


{foreach from=$data.playersearch.data item=player name=playersearch}
{  if $smarty.foreach.playersearch.first}
<table>
<caption>Players</caption>
<tr class="tableheader"><td>Callsign</td><td>Last Server</td></tr>
{  /if}
<tr class="{cycle values="odd,even"}row"><td><a href="{$page.baseURL}playerinfo.php?callsign={$player.callsign|escape:"url"}">{$player.callsign|escape}</a></td><td><a href="{$page.baseURL}serverinfo.php?server={$player.lastserver|escape:"url"}">{$player.lastserver}</a></td></tr>
{  if $smarty.foreach.playersearch.last}
</table>
{  /if}
{foreachelse}
<p><b>No player by the name '{$input.callsign|escape}'</b></p>
{/foreach}

{/if}
