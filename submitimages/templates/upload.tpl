<form enctype="multipart/form-data" action="{$config.paths.baseURL}upload.php" method="post" onsubmit="javascript:return validateForm();">
<input type="hidden" name="MAX_FILE_SIZE" value="{$config.upload.maxFileSize}">

<p>Currently only PNG files are supported. There is a limit of {$config.upload.maxFileSize/1024}KB per file, and you may upload {$config.upload.maxFiles} files at a time.</p>
<hr>

{section start=0 loop=$config.upload.maxFiles name=uploads}
<h2>File {$smarty.section.uploads.index+1}</h2>
<fieldset>
  <legend>File Information</legend>
  <label for="file{$smarty.section.uploads.index}">Image File: </label><input type="file" id="file{$smarty.section.uploads.index}" name="file[{$smarty.section.uploads.index}]">
</fieldset>
<fieldset>
  <legend>Author Information</legend>
  
  <label for="authorname{$smarty.section.uploads.index}">Author Name: </label><input type="text" id="authorname{$smarty.section.uploads.index}" name="authorname[{$smarty.section.uploads.index}]"><br>
  
  <label for="licenseselector{$smarty.section.uploads.index}">Select License: </label>
  <select id="licenseselector{$smarty.section.uploads.index}" name="licenseselector[{$smarty.section.uploads.index}]" onchange="javascript:handleLicenseSelector({$smarty.section.uploads.index});">
{foreach from=$config.licenses item=license key=licenseid}
    <option value="{$licenseid}"{if $licenseid == 0} selected="selected"{/if}>{$license.name}</option>
{/foreach}
  </select><input type="button" value="View" onclick="javascript:showLicense({$smarty.section.uploads.index});"><br>
  <span id="otherlicense{$smarty.section.uploads.index}" class="hide">
    <label for="otherlicensename{$smarty.section.uploads.index}">License Name: </label><input type="text" id="otherlicensename{$smarty.section.uploads.index}" name="otherlicensename[{$smarty.section.uploads.index}]"><br>
    Please specify a URL to the license and/or the license text itself.<br>
    <label for="otherlicenseurl{$smarty.section.uploads.index}">License URL: </label><input type="text" id="otherlicenseurl{$smarty.section.uploads.index}" name="otherlicenseurl[{$smarty.section.uploads.index}]"><br>
    <label for="otherlicensetext{$smarty.section.uploads.index}">License Text: </label><br><textarea style="width: 100%;" cols="74" rows="10" id="otherlicensetext{$smarty.section.uploads.index}" name="otherlicensetext[{$smarty.section.uploads.index}]"></textarea><br>
  </span>
  <input type="checkbox" id="confirm{$smarty.section.uploads.index}" name="confirm[{$smarty.section.uploads.index}]"><label id="confirmlabel{$smarty.section.uploads.index}" for="confirm{$smarty.section.uploads.index}"> I confirm that this image does not violate the <a href="{$config.baseURL}tos.php" onclick="javascript:return showTOS();">Terms Of Service</a>.</label>
</fieldset>
<hr>

{/section}

<h2>Other Information</h2>
<fieldset>
  <legend>Uploader Information</legend>
  <label>Username: </label><input type="text" disabled="disabled" value="{$user.username|escape:'htmlall'}"><br>
  <label for="uploaderfirstname">First Name: </label><input{if $messages.errors.uploaderfirstnameInvalid} class="invalid" {/if} type="text" id="uploaderfirstname" name="uploaderfirstname"><br>
  <label for="uploaderlastname">Last Name: </label><input{if $messages.errors.uploaderlastnameInvalid} class="invalid" {/if} type="text" id="uploaderlastname" name="uploaderlastname"><br>
  <input type="checkbox" id="confirmtos" name="confirmtos"><label{if $messages.errors.confirmtosInvalid} class="invalid" {/if} id="confirmtoslabel" for="confirmtos"> I confirm that none of the above images violate the <a href="{$config.baseURL}tos.php" onclick="javascript:return showTOS();">Terms Of Service</a>.</label><br>
  <input type="checkbox" id="confirmaccurate" name="confirmaccurate"><label{if $messages.errors.confirmaccurateInvalid} class="invalid" {/if} id="confirmaccuratelabel" for="confirmaccurate"> I confirm that all the information on this form is accurate.</label><br>
  <input type="submit" value="Upload Images">
</fieldset>
<hr id="errorhr"{if $messages.errors.count == 0} style="display: none;"{/if}>
<fieldset id="erroroutput"{if $messages.errors.count == 0} style="display: none;"{/if}><legend>Errors</legend>
<ul>
{if $messages.errors.uploaderfirstnameInvalid}<li>The uploader first name was not valid.</li>{/if}
{if $messages.errors.uploaderlastnameInvalid}<li>The uploader last name was not valid.</li>{/if}
{if $messages.errors.confirmtosInvalid}<li>You did not confirm that your images follows the <a href="{$config.paths.baseURL}tos.php" onclick="javascript:return showTOS();">Terms Of Service</a></li>{/if}
{if $messages.errors.confirmaccurateInvalid}<li>You did not confirm the accuracy of the information on this form.</li>{/if}

{foreach from=$messages.errors.files item=file name=files}
{  if $messages.errors.files[$smarty.foreach.files.index].maximumFileSizeExceeded}<li>File {$smarty.foreach.files.index+1}: The file exceeded the maximum allowed file size.</li>{/if}
{  if $messages.errors.files[$smarty.foreach.files.index].miscUploadError}<li>File {$smarty.foreach.files.index+1}: An error has occured during file upload. Please try again. If problem persists, please contact an administrator.</li>{/if}
{  if $messages.errors.files[$smarty.foreach.files.index].invalidFileFormat}<li>File {$smarty.foreach.files.index+1}: The file is not in PNG format.</li>{/if}
{  if $messages.errors.files[$smarty.foreach.files.index].authornameInvalid}<li>File {$smarty.foreach.files.index+1}: The author name was invalid.</li>{/if}
{  if $messages.errors.files[$smarty.foreach.files.index].licenseselectorInvalid}<li>File {$smarty.foreach.files.index+1}: The license was not specified or the specified license was invalid.</li>{/if}
{  if $messages.errors.files[$smarty.foreach.files.index].otherlicensenameInvalid}<li>File {$smarty.foreach.files.index+1}: You must specify a valid license name.</li>{/if}
{  if $messages.errors.files[$smarty.foreach.files.index].otherlicenseurlInvalid && $messages.errors.files[$smarty.foreach.files.index].otherlicensetextInvalid}<li>File {$smarty.foreach.files.index+1}: You must specify a valid license URL or the license text.</li>
{elseif $messages.errors.files[$smarty.foreach.files.index].otherlicenseurlInvalid}<li>You must specify a valid license URL.</li>
{elseif $messages.errors.files[$smarty.foreach.files.index].otherlicensetextInvalid}<li>You must specify valid license text.</li>{/if}
{  if $messages.errors.files[$smarty.foreach.files.index].confirmInvalid}<li>File {$smarty.foreach.files.index+1}: You did not confirm that your images follows the <a href="{$config.paths.baseURL}tos.php" onclick="javascript:return showTOS();">Terms Of Service</a></li>{/if}
{/foreach}

</ul>
</fieldset>
</form>
