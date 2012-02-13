The BZFlag Image Uploader is a tool for BZFlag map authors.  It is to allow these map authors to upload textures for use in their maps. Using this service has quite a few benefits:
<ul>
  <li>BZFlag 2.0.x only allows textures to be downloaded from a limited numbers of hosts, one of these being images.bzflag.org</li>
  <li>BZFlag 2.4.x <strong>ONLY</strong> allows textures from images.bzflag.org. Some images that had been on various <em>username</em>.bzflag.bz hostnames have been migrated to <a href="http://images.bzflag.org">images.bzflag.org/</a><em>username</em> to ease migration to 2.4.  Maps will need to be updated, however.</li>
</ul>
Therefore, by hosting your images here, you can be sure that players on your map can always view it the way it was intended, textures and all. They won't have to edit their downloadaccess.txt file to enable your textures.
<hr>

{if $data.pending}
{foreach from=$data.pending item=item name=pending}
{if $smarty.foreach.pending.first}Here are your images that are currently pending moderation:<br>
<table border="1">
  <tr><th>Thumbnail</th><th>Author Information</th></tr>
{/if}

  <tr>
    <td><img src="{$config.paths.baseURL}getthumb.php?filename={$item.bzid}_{$item.filename}" alt="{$item.filename} by {$item.authorname}"></td>
    <td>
      <strong>Filename:</strong> {$item.filename}<br>
      <strong>Author Name:</strong> {$item.authorname}<br>
      <strong>License:</i></strong> {$item.licensename}<br>
      {if $item.licenseurl}<strong>License URL:</strong> <a href="{$item.licenseurl}" onclick="javascript:return doPopup(this.href, 'license');">View License</a><br>{/if}
      {if $item.licensetext}<strong>License Text:</strong>{$item.licensetext|nl2br}{/if}
    </td>
  </tr>

{if $smarty.foreach.queue.last}</table>{/if}
{/foreach}
{else}
You currently have no images pending moderation.
{/if}
