<form enctype="multipart/form-data" action="{$config.paths.baseURL}upload.php" method="post" onsubmit="javascript:return validateForm();">
<input type="hidden" name="MAX_FILE_SIZE" value="{$config.upload.maxFileSize}">

<p>{$lang.uploadsupport}</p>

{foreach from=$messages.top item=top name=top}
{  if $smarty.foreach.top.first}
<hr>
<fieldset id="topoutput"><legend>{$lang.success}</legend>
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
  <legend>{$lang.fileinformation}</legend>
{if $input.files[$i].file.filename}{$lang.filenamewas}: {$input.files[$i].file.filename}<br>{/if}
  <label for="file{$i}">{$lang.imagefile}: </label><input{if $invalid.files[$i].file} class="invalid"{/if} type="file" id="file{$i}" name="file[{$i}]">
</fieldset>
<fieldset>
  <legend>{$lang.authorinformation}</legend>
  
  <label for="authorname{$i}">{$lang.authorname}: </label><input{if $invalid.files[$i].authorname} class="invalid"{/if} type="text" id="authorname{$i}" name="authorname[{$i}]"{if $input.files[$i].authorname} value="{$input.files[$i].authorname|escape:'htmlall'}"{/if}><br>
  
  <label for="licenseselector{$i}">{$lang.selectalicense}: </label>
  <select{if $invalid.files[$i].licenseselector} class="invalid"{/if} id="licenseselector{$i}" name="licenseselector[{$i}]" onchange="javascript:handleLicenseSelector({$i});">
{foreach from=$config.licenses item=license key=licenseid}
    <option value="{$licenseid}"{if $input.files[$i].licenseselector == $licenseid || (!$input.files[$i].licenseid && $licenseid == 0)} selected="selected"{/if}>{$license.name}</option>
{/foreach}
  </select><input type="button" value="{$lang.view}" onclick="javascript:showLicense({$i});"><br>
  <span id="otherlicense{$i}">
    <label for="otherlicensename{$i}">{$lang.licensename}: </label><input{if $invalid.files[$i].otherlicensename} class="invalid"{/if} type="text" id="otherlicensename{$i}" name="otherlicensename[{$i}]"{if $input.files[$i].otherlicensename} value="{$input.files[$i].otherlicensename|escape:'htmlall'}"{/if}><br>
    {$lang.specifylicenseurlortext}<br>
    <label for="otherlicenseurl{$i}">{$lang.licenseurl}: </label><input{if $invalid.files[$i].otherlicenseurl} class="invalid"{/if} type="text" id="otherlicenseurl{$i}" name="otherlicenseurl[{$i}]"{if $input.files[$i].otherlicenseurl} value="{$input.files[$i].otherlicenseurl|escape:'htmlall'}"{/if}><br>
    <label for="otherlicensetext{$i}">{$lang.licensetext}: </label><br><textarea{if $invalid.files[$i].otherlicensetext} class="invalid"{/if} style="width: 100%;" cols="74" rows="10" id="otherlicensetext{$i}" name="otherlicensetext[{$i}]">{if $input.files[$i].otherlicensetext}{$input.files[$i].otherlicensetext|escape:'htmlall'}{/if}</textarea><br>
  </span>
  <input type="checkbox" id="confirm{$i}" name="confirm[{$i}]"{if $input.files[$i].confirm} checked="checked"{/if}><label{if $invalid.files[$i].confirm} class="invalid"{/if} id="confirmlabel{$i}" for="confirm{$i}"> {$lang.thisimagetos}</label>
</fieldset>
<hr>

{/section}

<h2>{$lang.otherinformation}</h2>
<fieldset>
  <legend>{$lang.uploaderinformation}</legend>
  <label>Username: </label><input type="text" disabled="disabled" value="{$user.username|escape:'htmlall'}"><br>
  <label for="uploaderfirstname">{$lang.firstname}: </label><input{if $invalid.uploaderfirstname} class="invalid" {/if} type="text" id="uploaderfirstname" name="uploaderfirstname"{if $input.uploaderfirstname} value="{$input.uploaderfirstname|escape:'htmlall'}"{/if}><br>
  <label for="uploaderlastname">{$lang.lastname}: </label><input{if $invalid.uploaderlastname} class="invalid" {/if} type="text" id="uploaderlastname" name="uploaderlastname"{if $input.uploaderlastname} value="{$input.uploaderlastname|escape:'htmlall'}"{/if}><br>
  <label for="uploaderemail">{$lang.emailaddress}: </label><input{if $invalid.uploaderemail} class="invalid" {/if} type="text" id="uploaderemail" name="uploaderemail"{if $input.uploaderemail} value="{$input.uploaderemail|escape:'htmlall'}"{/if}><br>
  <input type="checkbox" id="confirmtos" name="confirmtos"{if $input.confirmtos} checked="checked"{/if}><label{if $invalid.confirmtos} class="invalid" {/if} id="confirmtoslabel" for="confirmtos"> {$lang.allimagetos}</label><br>
  <input type="checkbox" id="confirmaccurate" name="confirmaccurate"{if $input.confirmaccurate} checked="checked"{/if}><label{if $invalid.confirmaccurate} class="invalid" {/if} id="confirmaccuratelabel" for="confirmaccurate"> {$lang.confirmaccurate}</label><br>
  <input type="submit" value="{$lang.uploadimages}">
</fieldset>
{foreach from=$messages.errors item=error name=errors}
{  if $smarty.foreach.errors.first}
<hr id="errorhr">
<fieldset id="erroroutput"><legend>{$lang.errors}</legend>
<ul>
{  /if}
<li>{$error}</li>
{  if $smarty.foreach.errors.last}
</ul>
</fieldset>
{  /if}
{foreachelse}
<hr id="errorhr" style="display: none;">
<fieldset id="erroroutput" style="display: none;"><legend>{$lang.errors}</legend>
<ul>
  <li>{$lang.currentlynoerrors}</li>
</ul>
</fieldset>
{/foreach}
</form>
