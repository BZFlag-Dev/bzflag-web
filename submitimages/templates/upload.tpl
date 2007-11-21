<form enctype="multipart/form-data" action="{$config.paths.baseURL}upload.php" method="post" onsubmit="javascript:return validateForm();">
<input type="hidden" name="MAX_FILE_SIZE" value="{$config.upload.maxFileSize}">

<p>Currently only PNG files are supported. There is a limit of {$config.upload.maxFileSize/1024}KB per file, and you may upload {$config.upload.maxFiles} files at a time.</p>
<hr>

{section start=0 loop=$config.upload.maxFiles name=uploads}
{assign var="i" value=$smarty.section.uploads.index}
<h2>File {$i+1}</h2>
<fieldset>
  <legend>File Information</legend>
{if $input.files[$i].file.filename}Filename of this file was: {$input.files[$i].file.filename}<br>{/if}
  <label for="file{$i}">Image File: </label><input{if $messages.errors.files[$i].maximumFileSizeExceeded or $messages.errors.files[$i].miscUploadError or $messages.errors.files[$i].invalidFileFormat} class="invalid"{/if} type="file" id="file{$i}" name="file[{$i}]">
</fieldset>
<fieldset>
  <legend>Author Information</legend>
  
  <label for="authorname{$i}">Author Name: </label><input{if $messages.errors.files[$i].authornameInvalid} class="invalid"{/if} type="text" id="authorname{$i}" name="authorname[{$i}]"><br>
  
  <label for="licenseselector{$i}">Select License: </label>
  <select{if $messages.errors.files[$i].licenseselectorInvalid} class="invalid"{/if} id="licenseselector{$i}" name="licenseselector[{$i}]" onchange="javascript:handleLicenseSelector({$i});">
{foreach from=$config.licenses item=license key=licenseid}
    <option value="{$licenseid}"{if $licenseid == 0} selected="selected"{/if}>{$license.name}</option>
{/foreach}
  </select><input type="button" value="View" onclick="javascript:showLicense({$i});"><br>
  <span id="otherlicense{$i}" class="hide">
    <label for="otherlicensename{$i}">License Name: </label><input type="text" id="otherlicensename{$i}" name="otherlicensename[{$i}]"><br>
    Please specify a URL to the license and/or the license text itself.<br>
    <label for="otherlicenseurl{$i}">License URL: </label><input type="text" id="otherlicenseurl{$i}" name="otherlicenseurl[{$i}]"><br>
    <label for="otherlicensetext{$i}">License Text: </label><br><textarea style="width: 100%;" cols="74" rows="10" id="otherlicensetext{$i}" name="otherlicensetext[{$i}]"></textarea><br>
  </span>
  <input type="checkbox" id="confirm{$i}" name="confirm[{$i}]"><label id="confirmlabel{$i}" for="confirm{$i}"> I confirm that this image does not violate the <a href="{$config.baseURL}tos.php" onclick="javascript:return showTOS();">Terms Of Service</a>.</label>
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
{assign var="i" value=$smarty.section.uploads.index}
{  if $messages.errors.files[$i].maximumFileSizeExceeded}<li>File {$i+1}: The file exceeded the maximum allowed file size.</li>{/if}
{  if $messages.errors.files[$i].miscUploadError}<li>File {$i+1}: An error has occured during file upload. Please try again. If problem persists, please contact an administrator.</li>{/if}
{  if $messages.errors.files[$i].invalidFileFormat}<li>File {$i+1}: The file is not in PNG format.</li>{/if}
{  if $messages.errors.files[$i].authornameInvalid}<li>File {$i+1}: The author name was invalid.</li>{/if}
{  if $messages.errors.files[$i].licenseselectorInvalid}<li>File {$i+1}: The license was not specified or the specified license was invalid.</li>{/if}
{  if $messages.errors.files[$i].otherlicensenameInvalid}<li>File {$i+1}: You must specify a valid license name.</li>{/if}
{  if $messages.errors.files[$i].otherlicenseurlInvalid && $messages.errors.files[$i].otherlicensetextInvalid}<li>File {$i+1}: You must specify a valid license URL or the license text.</li>
{elseif $messages.errors.files[$i].otherlicenseurlInvalid}<li>You must specify a valid license URL.</li>
{elseif $messages.errors.files[$i].otherlicensetextInvalid}<li>You must specify valid license text.</li>{/if}
{  if $messages.errors.files[$i].confirmInvalid}<li>File {$i+1}: You did not confirm that your images follows the <a href="{$config.paths.baseURL}tos.php" onclick="javascript:return showTOS();">Terms Of Service</a></li>{/if}
{/foreach}

</ul>
</fieldset>
</form>
