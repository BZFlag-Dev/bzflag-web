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
License: {$file.licensename}
{if $file.licenseurl}License URL: {$file.licenseurl}{/if}
{if $file.licensebody}License Text:
{$file.licensebody}{/if}

{    if !$smarty.foreach.files.last}
--------------------
{    /if}

{  /if}
{/foreach}
