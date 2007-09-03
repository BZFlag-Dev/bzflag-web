<form enctype="application/x-www-form-urlencoded " action="{$config.paths.baseURL}upload.php" method="post">

<p>Currently only PNG files are supported. The maximum filesize per file is {$config.upload.maxFileSize} bytes, and you may upload {$config.upload.maxFiles} files at a time.</p>
<hr>
{section start=0 loop=$config.upload.maxFiles name=uploads}
<fieldset>
  <legend>File Information</legend>
  <label for="file{$smarty.section.uploads.index}">Image File: </label><input type="file" accept="image/png" id="file{$smarty.section.uploads.index}" name="file[{$smarty.section.uploads.index}]">
</fieldset>
<fieldset>
  <legend>Author Information</legend>
  
  <label for="authorname{$smarty.section.uploads.index}">Author Name: </label><input type="text" id="authorname{$smarty.section.uploads.index}" name="authorname[{$smarty.section.uploads.index}]"><br>
  
  <label for="licenseselector{$smarty.section.uploads.index}">Select License: </label>
  <select id="licenseselector$smarty.section.uploads.index" name="licenseselector[$smarty.section.uploads.index]">
    <option value="1">LGPL</option>
    <option value="2">Public Domain</option>
    <option value="0">Other OSI-Approved</option>
  </select><br>
  
  <label for="licensename{$smarty.section.uploads.index}">License Name: </label><input type="text" id="otherlicense{$smarty.section.uploads.index}" name="otherlicense[{$smarty.section.uploads.index}]"><br>
  
  <label for="license{$smarty.section.uploads.index}">License: </label><input type="text" id="{$smarty.section.uploads.index}" name="[{$smarty.section.uploads.index}]"><br>
  <input type="checkbox" id="agree{$smarty.section.uploads.index}" name="agree[{$smarty.section.uploads.index}]"><label for="agree{$smarty.section.uploads.index}"> I agree that this image does not violate the Terms Of Service.</label>
</fieldset>
<hr>
{/section}

</form>
