{foreach from=$data.queue item=item name=queue}
{if $smarty.foreach.queue.first}<div>The following items are pending approval. Please keep in mind the <a href="{$config.paths.baseURL}tos.php">Terms Of Service</a> when approving images.</div>{/if}

<div style="border: 1px solid black; float: left; margin: 0.5em; padding: 0.5em; width: {$config.thumbnail.maxWidth*2}px; height: {$config.thumbnail.maxHeight*2.5}px; overflow: auto;">
  <a href="{$config.paths.baseURL}getimage.php?filename={$item.bzid}_{$item.filename}"><img src="{$config.paths.baseURL}getthumb.php?filename={$item.bzid}_{$item.filename}" alt="{$item.filename} by {$item.authorname}"></a><br>
  <strong>Author Name:</strong> {$item.authorname}<br>
{if $item.licenseid != 255}
  <strong>License:</strong> {$config.licenses[$item.licenseid].name}<br>
{else}
  <strong><i>Custom License:</i></strong> {$item.licensename}<br>
  <strong><i>License URL:</i></strong> {if $item.licenseurl}<a href="{$item.licenseurl}">{$item.licenseurl}</a>{else}Not specified{/if}<br>
  <strong><i>License Text:</i></strong> {if $item.licensebody}{$item.licensebody|nl2br}{else}Not specified{/if}<br>
{/if}

<strong>Uploader Real Name:</strong> {$item.uploaderfirstname} {$item.uploaderlastname}<br>
<strong>Uploader Username:</strong> {$item.username}<br>
<form action="{$config.paths.baseURL}queue.php" method="POST">
<fieldset><legend>Approve/Reject</legend>
<input type="hidden" name="queueid" value="{$item.queueid}">
<strong>Message to user:</strong><br>
<textarea name="message" style="width: 100%;" cols="24" rows="5"></textarea><br>
<input type="submit" style="background-color: green;" name="action" value="Accept">
<input type="submit" style="background-color: red;" name="action" value="Reject">
</fieldset>
</form>
</div>

{if $smarty.foreach.queue.last}<div style="clear:both;"></div>{/if}

{foreachelse}
  <div>There are currently no images pending approval.</div>
{/foreach}
