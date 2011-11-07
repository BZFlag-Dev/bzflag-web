<h2>Information sampled every 5 minutes</h2>

{if $data.servers}
<table id="currentplayers">
  <caption>Servers</caption>
  <tr class="tableheader">
    <td>Server</td>
    <td>Players</td>
    <td>Game Style</td>
    <td>Description</td>
  </tr>

{foreach from=$data.servers item=server name=servers}
  <tr class="{cycle values="odd,even"}row">
    <td><a href="{$page.baseURL}serverinfo.php?server={$server.server|escape:"url"}">{$server.server}</a></td>
    <td align="right">{if $server.players > 0}<strong>{$server.players}</strong>{else}0{/if}</td>
    <td>{if $server.rabbitchase == 'Y'}<span class="teamrabbit">Rabbit Chase</span>{elseif $server.ctf == 'Y'}<span class="teamred">Capture-the-Flag</span>{else}<span class="teamrogue">Free-style</span>{/if}</td>
    <td>{$server.description|escape}</td>
  </tr>
{/foreach}
</table>
{else}
<p>The list server could not be contacted on our last query. We have no data to display.</p>
{/if}
