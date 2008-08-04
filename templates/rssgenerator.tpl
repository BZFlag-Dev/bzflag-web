{if $rssfeed}
<pre>
{$page.baseURL}rss.php?feed=players{$rssfeed}
</pre>
{/if}

{if $rsserror}
<p><strong>{$rsserror}</strong></p>
{/if}

<p>Fill out the form, and submit it. You will then be given a URL for an RSS feed with the settings you specified.</p>

<form action="{$page.baseURL}rssgenerator.php" method="get">

<table>

<caption>Server</caption>

<tr class="evenrow">
<td colspan="2"><input type="radio" name="fullplayerlist" value="yes"{if $input.fullplayerlist == 'yes' || !$input.fullplayerlist} checked="checked"{/if}> Show full player list</td>
</tr>
<tr>
<td><input type="radio" name="fullplayerlist" value="no"{if $input.fullplayerlist == 'no'} checked="checked"{/if}> Show only players on this server: <input name="server"{if $input.server} value="{$input.server|escape}"{/if}></td>
</tr>

</table>

<table>

<caption>Display Order</caption>

<tr class="evenrow">
<td><input type="radio" name="order" value="callsign"{if $input.order == 'callsign' || !$input.order} checked="checked"{/if}> Callsign Ascending</td>
<td><input type="radio" name="order" value="callsigndesc"{if $input.order == 'callsigndesc'} checked="checked"{/if}> Callsign Descending</td>
</tr>

<tr class="oddrow">
<td><input type="radio" name="order" value="server" {if $input.order == 'server'} checked="checked"{/if}> Server Ascending</td>
<td><input type="radio" name="order" value="serverdesc" {if $input.order == 'serverdesc'} checked="checked"{/if}> Server Descending</td>
</tr>

<tr class="evenrow">
<td><input type="radio" name="order" value="score" {if $input.order == 'score'} checked="checked"{/if}> Score Descending</td>
<td><input type="radio" name="order" value="scoreasc" {if $input.order == 'scoreasc'} checked="checked"{/if}> Score Ascending</td>
</tr>

<tr class="oddrow">
<td><input type="radio" name="order" value="strengthindex" {if $input.order == 'strengthindex'} checked="checked"{/if}> Strength Index Descending</td>
<td><input type="radio" name="order" value="strengthindexasc" {if $input.order == 'strengthindexasc'} checked="checked"{/if}> Strength Index Ascending</td>
</tr>

<tr class="evenrow">
<td colspan="2"><input type="submit"></td>
</tr>

</table>

</form>
