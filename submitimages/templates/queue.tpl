{foreach from=$data.queue item=item name=queue}
{if $smarty.foreach.queue.first}<div>The following items are pending approval. Please keep in mind the <a href="{$config.paths.baseURL}tos.php">Terms Of Service</a> when approving images.</div>{/if}

<div style="border: 1px solid black; float: left; margin: 0.5em;">
  <a href="{$config.paths.baseURL}getimage.php?filename={$item.bzid}_{$item.filename}"><img src="{$config.paths.baseURL}getthumb.php?filename={$item.bzid}_{$item.filename}" alt="{$item.filename} by {$item.authorname}"></a><br>
</div>

{if $smarty.foreach.queue.last}<div style="clear:both;"></div>{/if}

{foreachelse}
  <div>There are currently no images pending approval.</div>
{/foreach}
