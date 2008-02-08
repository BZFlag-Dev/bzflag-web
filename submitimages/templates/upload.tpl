<form enctype="multipart/form-data" action="{$config.paths.baseURL}upload.php" method="post" onsubmit="javascript:return validateForm();">
<input type="hidden" name="MAX_FILE_SIZE" value="{$config.upload.maxFileSize}">

<p>Currently only PNG files are supported. There is a limit of {$config.upload.maxFileSize/1024}KB per file, and you may upload {$config.upload.maxFiles} files at a time.</p>

{foreach from=$messages.top item=top name=top}
{  if $smarty.foreach.top.first}
<hr>
<fieldset id="topoutput"><legend>Success</legend>
<ul>
{  /if}
<li>{$top}</li>
{  if $smarty.foreach.top.last}
</ul>
</fieldset>
<hr>
{  /if}
{foreachelse}
<hr>
{/foreach}

{section start=1 loop=$config.upload.maxFiles+1 name=uploads}
{assign var="i" value=$smarty.section.uploads.index}
<h2>File {$i}</h2>
<fieldset>
  <legend>File Information</legend>
{if $input.files[$i].file.filename}Filename of this file was: {$input.files[$i].file.filename}<br>{/if}
  <label for="file{$i}">Image File: </label><input{if $invalid.files[$i].file} class="invalid"{/if} type="file" id="file{$i}" name="file[{$i}]">
</fieldset>
<fieldset>
  <legend>Author Information</legend>
  
  <label for="authorname{$i}">Author Name: </label><input{if $invalid.files[$i].authorname} class="invalid"{/if} type="text" id="authorname{$i}" name="authorname[{$i}]"{if $input.files[$i].authorname} value="{$input.files[$i].authorname|escape:'htmlall'}"{/if}><br>
  
  <label for="licenseselector{$i}">Select License: </label>
  <select{if $invalid.files[$i].licenseselector} class="invalid"{/if} id="licenseselector{$i}" name="licenseselector[{$i}]" onchange="javascript:handleLicenseSelector({$i});">
{foreach from=$config.licenses item=license key=licenseid}
    <option value="{$licenseid}"{if $input.files[$i].licenseselector == $licenseid || (!$input.files[$i].licenseid && $licenseid == 0)} selected="selected"{/if}>{$license.name}</option>
{/foreach}
  </select><input type="button" value="View" onclick="javascript:showLicense({$i});"><br>
  <span id="otherlicense{$i}">
    <label for="otherlicensename{$i}">License Name: </label><input{if $invalid.files[$i].otherlicensename} class="invalid"{/if} type="text" id="otherlicensename{$i}" name="otherlicensename[{$i}]"{if $input.files[$i].otherlicensename} value="{$input.files[$i].otherlicensename|escape:'htmlall'}"{/if}><br>
    Please specify a URL to the license and/or the license text itself.<br>
    <label for="otherlicenseurl{$i}">License URL: </label><input{if $invalid.files[$i].otherlicenseurl} class="invalid"{/if} type="text" id="otherlicenseurl{$i}" name="otherlicenseurl[{$i}]"{if $input.files[$i].otherlicenseurl} value="{$input.files[$i].otherlicenseurl|escape:'htmlall'}"{/if}><br>
    <label for="otherlicensetext{$i}">License Text: </label><br><textarea{if $invalid.files[$i].otherlicensetext} class="invalid"{/if} style="width: 100%;" cols="74" rows="10" id="otherlicensetext{$i}" name="otherlicensetext[{$i}]">{if $input.files[$i].otherlicensetext}{$input.files[$i].otherlicensetext|escape:'htmlall'}{/if}</textarea><br>
  </span>
  <input type="checkbox" id="confirm{$i}" name="confirm[{$i}]"{if $input.files[$i].confirm} checked="checked"{/if}><label{if $invalid.files[$i].confirm} class="invalid"{/if} id="confirmlabel{$i}" for="confirm{$i}"> I confirm that this image does not violate the <a href="{$config.baseURL}tos.php" onclick="javascript:return showTOS();">Terms Of Service</a>.</label>
</fieldset>
<hr>

{/section}

<h2>Other Information</h2>
<fieldset>
  <legend>Uploader Information</legend>
  <label>Username: </label><input type="text" disabled="disabled" value="{$user.username|escape:'htmlall'}"><br>
  <label for="uploaderfirstname">First Name: </label><input{if $invalid.uploaderfirstname} class="invalid" {/if} type="text" id="uploaderfirstname" name="uploaderfirstname"{if $input.uploaderfirstname} value="{$input.uploaderfirstname|escape:'htmlall'}"{/if}><br>
  <label for="uploaderlastname">Last Name: </label><input{if $invalid.uploaderlastname} class="invalid" {/if} type="text" id="uploaderlastname" name="uploaderlastname"{if $input.uploaderlastname} value="{$input.uploaderlastname|escape:'htmlall'}"{/if}><br>
  <input type="checkbox" id="confirmtos" name="confirmtos"{if $input.confirmtos} checked="checked"{/if}><label{if $invalid.confirmtos} class="invalid" {/if} id="confirmtoslabel" for="confirmtos"> I confirm that none of the above images violate the <a href="{$config.baseURL}tos.php" onclick="javascript:return showTOS();">Terms Of Service</a>.</label><br>
  <input type="checkbox" id="confirmaccurate" name="confirmaccurate"{if $input.confirmaccurate} checked="checked"{/if}><label{if $invalid.confirmaccurate} class="invalid" {/if} id="confirmaccuratelabel" for="confirmaccurate"> I confirm that all the information on this form is accurate.</label><br>
  <input type="submit" value="Upload Images">
</fieldset>
{foreach from=$messages.errors item=error name=errors}
{  if $smarty.foreach.errors.first}
<hr id="errorhr">
<fieldset id="erroroutput"><legend>Errors</legend>
<ul>
{  /if}
<li>{$error}</li>
{  if $smarty.foreach.errors.last}
</ul>
</fieldset>
{  /if}
{foreachelse}
<hr id="errorhr" style="display: none;">
<fieldset id="erroroutput" style="display: none;"><legend>Errors</legend>
</fieldset>
{/foreach}
</form>
