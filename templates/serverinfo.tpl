<h2>Current info for {if $data.serverinfo}{$data.serverinfo.server|escape}{else}{$input.server|escape}{/if}</h2>

{if $data.serverinfo}
<h3>{$data.serverinfo.description|escape}</h3>
 <table>

    <caption>Server Configuration</caption>

    <tr class="evenrow">
    <td align="right">{$data.serverinfo.players}{if $data.serverinfo.maxplayers != 200}/{$data.serverinfo.maxplayers}{/if}</td>
    <td><strong>Players</strong></td>
    <td><img src="images/check.png" alt="Yes"> <strong>{$data.serverinfo.maxshots} Shot{if $data.serverinfo.maxshots != 1}s{/if}</strong></td>
    <td><img src="images/{if $data.serverinfo.jumping}check.png" alt="Yes"{else}x.png" alt="No"{/if}> <strong>Jumping</strong></td>
    </tr>

    <tr class="oddrow">
    <td align="right">{$data.serverinfo.roguesize}{if $data.serverinfo.roguemax != 200}/{$data.serverinfo.roguemax}{/if}</td>
    <td class="team{$data.teamnames.X|lower}">{$data.teamnames.X}</td>
    <td><img src="images/check.png" alt="Yes"> {if $data.serverinfo.rabbitchase == 'Y'}<span class="teamrabbit">Rabbit Chase</span>{elseif $data.serverinfo.ctf == 'Y'}<span class="teamred">Capture-the-Flag</span>{else}<span class="teamrogue">Free-style</span>{/if}</td>
    <td><img src="images/{if $data.serverinfo.ricochet}check.png" alt="Yes"{else}x.png" alt="No"{/if}> <strong>Ricochet</strong></td>
    </tr>

    <tr class="evenrow">
    <td align="right">{$data.serverinfo.redsize}{if $data.serverinfo.redmax != 200}/{$data.serverinfo.redmax}{/if}</td>
    <td class="team{$data.teamnames.R|lower}">{$data.teamnames.R}</td>
    <td><img src="images/{if $data.serverinfo.superflags}check.png" alt="Yes"{else}x.png" alt="No"{/if}> <strong>Super Flags</strong></td>
    <td><img src="images/{if $data.serverinfo.inertia}check.png" alt="Yes"{else}x.png" alt="No"{/if}> Inertia</td>
    </tr>

    <tr class="oddrow">
    <td align="right">{$data.serverinfo.greensize}{if $data.serverinfo.greenmax != 200}/{$data.serverinfo.greenmax}{/if}</td>
    <td class="team{$data.teamnames.G|lower}">{$data.teamnames.G}</td>
    <td><img src="images/{if $data.serverinfo.antidoteflags}check.png" alt="Yes"{else}x.png" alt="No"{/if}> Antidote Flags</td>
    <td><img src="images/{if $data.serverinfo.handicap}check.png" alt="Yes"{else}x.png" alt="No"{/if}> Handicap</td>
    </tr>

    <tr class="evenrow">
    <td align="right">{$data.serverinfo.bluesize}{if $data.serverinfo.bluemax != 200}/{$data.serverinfo.bluemax}{/if}</td>
    <td class="team{$data.teamnames.B|lower}">{$data.teamnames.B}</td>
    <td>{if $data.serverinfo.shakewins > 0}<img src="images/check.png" alt="Yes"> {$data.serverinfo.shakewins} Win Drops Bad Flag{if $data.serverinfo.shakewins != 1}s{/if}{else}<img src="images/x.png" alt="X"> No Wins Drop Bad Flag{/if}</td>
    <td>{if $data.serverinfo.maxteamscore > 0}<img src="images/check.png" alt="Yes"> Max Team Score: {$data.serverinfo.maxteamscore}{else}<img src="images/x.png" alt="No"> Max Team Score: None{/if}</td>
    </tr>

    <tr class="oddrow">
    <td align="right">{$data.serverinfo.purplesize}{if $data.serverinfo.purplemax != 200}/{$data.serverinfo.purplemax}{/if}</td>
    <td class="team{$data.teamnames.P|lower}">{$data.teamnames.P}</td>
    <td>{if $data.serverinfo.shaketimeout}<img src="images/check.png" alt="Yes"> {$data.serverinfo.shaketimeout|number_format:"%.1f"} secs to Drop Bad Flag{else}<img src="images/x.png" alt="No"> No secs to Drop Bad Flag{/if}</td>
    <td>{if $data.serverinfo.maxplayerscore}<img src="images/check.png" alt="Yes"> Max Player Score: {$data.serverinfo.maxplayerscore}{else}<img src="images/x.png" alt="No"> Max Player Score: None{/if}</td>
    </tr>

    <tr class="evenrow">
    <td align="right">{$data.serverinfo.observersize}{if $data.serverinfo.observermax != 200}/{$data.serverinfo.observermax}{/if}</td>
    <td class="team{$data.teamnames.O|lower}">Observers</td>
    <td>{if $data.serverinfo.maxtime}<img src="images/check.png" alt="Yes"> Time Limit: {$data.serverinfo.nicemaxtime}{else}<img src="images/x.png" alt="X"> Time Limit: None{/if}</td>
    <td></td>

    </table>

{if $data.teaminfo}
 <table>

    <caption>Team Info</caption>

    <tr class="tableheader">
    <td>Team</td><td>Score</td>
    <td>Wins - Losses</td></tr>
{  foreach from=$data.teaminfo item=teaminfo name=teaminfo}
      <tr class="{cycle values="odd,even"}row"><td class="team{$data.teamnames[$teaminfo.team]|lower}">{$data.teamnames[$teaminfo.team]}</td>
      <td class="score">{$teaminfo.score}</td>
      <td>{$teaminfo.won}-{$teaminfo.lost}</td></tr>
{  /foreach}
   </table>

<table>
       <caption>Players</caption>
       <tr class="tableheader">
       <td>Callsign</td><td>Team</td><td>Score</td></tr>
{assign var="haveplayers" value="0"}
{foreach from=$data.currentplayers item=player name=currentplayers}
{  if $player.team != 'O'}
  <tr class="{cycle name="odd,even"}row">
    <td><a href="playerinfo.php?callsign={$player.callsign|escape:"url"}">{$player.callsign|escape:"html"}{if $player.email} ({$player.email|escape}){/if}</a></td>
    <td class="team{$data.teamnames[$player.team]|lower}">{$data.teamnames[$player.team]}</td>
    <td class="score">{$player.score}</td>
  </tr>
{    assign var="haveplayers" value="1"}
{  /if}
{/foreach}
{if !$haveplayers}
  <tr><td colspan="3">No players online</td></tr>
{/if}
</table>


    <table>
       <caption>Observers</caption>
    <tr class="tableheader"><td>Callsign</td></tr>
{assign var="haveobservers" value="0"}
{foreach from=$data.currentplayers item=player name=currentobservers}
{  if $player.team == 'O'}
 <tr class="{cycle value="odd,even"}row">
    <td><a href="playerinfo.php?callsign={$player.callsign|escape:"url"}">{$player.callsign|escape:"html"}{if $player.email} ({$player.email|escape}){/if}</a></td>
    </tr>
{    assign var="haveobservers" value="1"}
{  /if}
{/foreach}
{if !$haveobservers}
  <tr><td colspan="3">No observers online</td></tr>
{/if}
</table>

{  else}
 <p>No player info available for <strong>{$data.serverinfo.server}</strong></p>
{  /if}
{else}
<p>No game info available for <strong>{$input.server}</strong></p>
{/if}
