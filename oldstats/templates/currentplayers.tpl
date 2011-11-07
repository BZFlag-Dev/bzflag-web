<h2>Information sampled every 5 minutes</h2>

{if $data.players}
<table id="currentplayers">
<caption>Players</caption>
<tr class="tableheader">
<td><a href="{$page.baseURL}currentplayers.php?order={if $input.order == 'callsign'}callsigndesc{else}callsign{/if}">Callsign</a>
   {if $input.order == 'callsign'} <img src="images/uparrow.png" alt="^">{elseif $input.order == 'callsigndesc'} <img src="images/downarrow.png" alt="v">{/if}</td>

<td><a href="{$page.baseURL}currentplayers.php?order={if $input.order == 'server'}serverdesc{else}server{/if}">Server</a>
   {if $input.order == 'server'} <img src="images/uparrow.png" alt="^" />{elseif order == 'serverdesc'} <img src="images/downarrow.png" alt="v" />{/if}</td>

<td><a href="{$page.baseURL}currentplayers.php?order={if $input.order == 'score'}scoreasc{else}score{/if}">Score</a>
   {if $input.order == 'scoreasc'} <img src="images/uparrow.png" alt="^" />{elseif $input.order == 'score'} <img src="images/downarrow.png" alt="v" />{/if}</td>

<td><a href="{$page.baseURL}currentplayers.php?order={if $input.order == 'strengthindex'}strengthindexasc{else}strengthindex{/if}">Strength Index</a> <a class="help" href="{$page.baseURL}help.php#strengthindex">?</a>
   {if $input.order == 'strengthindexasc'} <img src="images/uparrow.png" alt="^" />{elseif $input.order == 'strengthindex'} <img src="images/downarrow.png" alt="v" />{/if}</td>

<td>Team</td>
</tr>

{foreach from=$data.players item=player name=players}
<tr class="{cycle values="odd,even"}row"><td><a href="{$page.baseURL}playerinfo.php?callsign={$player.callsign|escape:"url"}">{$player.callsign|escape}</a></td>
<td><a href="{$page.baseURL}serverinfo.php?server={$player.server|escape:"url"}">{$player.server}</a></td>
<td align="right">{$player.score}</td>
<td align="right">{$player.strengthindex|string_format:"%.3f"}</td>
<td class="team{$data.teamnames[$player.team]|lower}">{$data.teamnames[$player.team]}</td>
</tr>
{/foreach}
</table>
{else}
<p>Oh crap! The monkeys ate the list server!</p>
{/if}
