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
  <select id="licenseselector{$smarty.section.uploads.index}" name="licenseselector[{$smarty.section.uploads.index}]" onchange="javascript:handleLicenseSelector(this);">
    <option value="0" selected="selected">Other OSI-Approved</option>
    <option value="1">LGPL</option>
    <option value="2">Public Domain</option>
  </select><br>
  <span id="otherlicense{$smarty.section.uploads.index}">
  <label for="licensename{$smarty.section.uploads.index}">License Name: </label><input type="text" id="licensename{$smarty.section.uploads.index}" name="licensename[{$smarty.section.uploads.index}]"><br>
  <label for="licenseurl{$smarty.section.uploads.index}">License URL: </label><input type="text" id="licenseurl{$smarty.section.uploads.index}" name="licenseurl[{$smarty.section.uploads.index}]"><br>
  <label for="licensetext{$smarty.section.uploads.index}">License: </label><br><textarea style="width: 100%;" cols="74" rows="10" id="licensetext{$smarty.section.uploads.index}" name="licensetext[{$smarty.section.uploads.index}]"></textarea><br>
  <input type="checkbox" id="agree{$smarty.section.uploads.index}" name="agree[{$smarty.section.uploads.index}]"><label for="agree{$smarty.section.uploads.index}"> I agree that this image does not violate the Terms Of Service.</label>
  </span>
</fieldset>
<hr>

{/section}

<fieldset>
  <legend>Uploader Information</legend>
  <label>Username: </label><input type="text" disabled="disabled" value="{$user.username|escape:'htmlall'}"><br>
  <label for="uploaderfirstname">First Name: </label><input type="text" id="uploaderfirstname" name="uploaderfirstname"><br>
  <label for="uploaderfirstname">Last Name: </label><input type="text" id="uploaderlastname" name="uploaderlastname"><br>
  <input type="checkbox" id="agreeall" name="agreeall"><label for="agreeall"> I agree that all of the above images do not violate the Terms Of Service.</label><br>
  <input type="submit" value="Upload Images">
</fieldset>

</form>
