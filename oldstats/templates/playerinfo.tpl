<h2>Stats for player {if $data.player.callsign}{$data.player.callsign|escape}{else}{$input.callsign|escape}{/if}</h2>

{if $data.player}
<table>
<caption>Player Info</caption>
<tr class="evenrow"><td><strong>Email String</strong></td><td>{$data.player.email|escape}</td></tr>
<tr class="oddrow"><td><strong>High Score</strong></td><td>{$data.player.highscore}</td></tr>
<tr class="evenrow"><td><strong>Low Score</strong></td><td>{$data.player.lowscore}</td></tr>
<tr class="oddrow"><td><strong>High Strength Index</strong></td><td>{$data.player.highstrengthindex|string_format:"%.3f"}</td></tr>
<tr class="evenrow"><td><strong>Low Strength Index</strong></td><td>{$data.player.lowstrengthindex|string_format:"%.3f"}</td></tr>
<tr class="oddrow"><td><strong>High Kill Ratio</strong></td><td>{$data.player.highkillratio|string_format:"%.3f"}</td></tr>
<tr class="evenrow"><td><strong>Low Kill Ratio</strong></td><td>{$data.player.lowkillratio|string_format:"%.3f"}</td></tr>
<tr class="oddrow"><td><strong>Most Wins</strong></td><td>{$data.player.mostwins}</td></tr>
<tr class="evenrow"><td><strong>Most Losses</strong></td><td>{$data.player.mostlosses}</td></tr>
<tr class="oddrow"><td><strong>Last Seen</strong></td><td>{$data.player.lastseen|date_format:"%a %b %d %H:%M:%S %Z %Y"}</td></tr>
<tr class="evenrow"><td><strong>Last Server</strong></td><td><a href="{$page.baseURL}serverinfo.php?server={$data.player.lastserver|escape:"url"}">{$data.player.lastserver}</a></td></tr>
</table>
{else}
<p>No info available for player <strong>{$input.callsign|escape}</strong></p>
{/if}
