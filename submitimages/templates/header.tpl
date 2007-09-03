<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01//EN' 'http://www.w3.org/TR/html4/strict.dtd'>
<html>
<head>
<title>{$title}</title>
<link rel="stylesheet" type="text/css" href="bztheme.css">
{if $redirect}
<meta http-equiv="refresh" content="{$redirect.delay};URL={$redirect.url}">
{/if}
<meta http-equiv="content-type" content="text/html; charset=iso-8859-2">
<script type="text/javascript">
  var upload_maxFiles = {$config.upload.maxFiles};
</script>
{foreach from=$javascripts item=javascript}
<script type="text/javascript" src="{$javascript}"></script>
{/foreach}

</head>
<body>

  <div id="navigation">Navigation: 
    <ul>
{if !$user}
    <li><a href="http://my.bzflag.org/weblogin.php?action=weblogin&amp;url={$config.paths.baseURL|escape:'url'}login.php%3Ftoken%3D%25TOKEN%25%26callsign%3D%25USERNAME%25">Log In</a></li>
{else}
    <li><a href="{$config.paths.baseURL}">Home</a></li>
    <li><a href="{$config.paths.baseURL}upload.php">Upload Images</a></li>
{  if $user.moderator}
    <li><a href="{$config.paths.baseURL}queue.php">Moderation Queue</a></li>
{  /if}
    <li><a href="{$config.paths.baseURL}logout.php">Logout</a></li>
{/if}
    </ul>
  </div>
    
  <div id="content">

