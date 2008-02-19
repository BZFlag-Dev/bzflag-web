BZFlag Image Submission System

One or more images have been uploaded to the image submission system. As a
moderator, you have received this email. To approve or reject these images,
visit the URL below and login:
{$config.paths.baseURL}

--------------------
Uploader Information
--------------------
Username: {$user.username}
Real Name: {$input.uploaderfirstname} {$input.uploaderlastname}
Email: {$input.uploaderemail}

--------------------
File Information
--------------------

{foreach from=$input.files item=file name=files}
{  if !$file.invalid}
Filename: {$file.file.filename}
Author Name: {$file.authorname}
{    if $file.licenseselector == 255}
License: {$file.otherlicensename}
{if $file.otherlicenseurl}License URL: {$file.otherlicenseurl}{/if}
{if $file.otherlicensebody}License Text:
{$file.otherlicensebody}{/if}
{    else}
License: {$config.licenses[$file.licenseselector].name}
License URL: {$config.licenses[$file.licenseselector].url}
{    /if}

{    if !$smarty.foreach.files.last}
--------------------
{    /if}

{  /if}
{/foreach}
