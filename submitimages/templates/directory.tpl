<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <title>images.bzflag.org - Index of {$data.httpdirectory}</title>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <style type="text/css">{literal}
    body {
      background-color: white;
      color: black;
      font-family: monospace;
    }
    
    h1 {
      margin: 0 0 0.5em 0;
      padding: 0;
    }
    
    table {
      border-collapse: collapse;
    }
    
    td, th {
      border: 1px solid black;
      padding: 5px 10px;
    }
    
    th {
      background-color: #CCC;
    }
  {/literal}</style>
</head>
<body>
  <a href="{$config.paths.baseURL}">Submit Images</a><hr>

  <h1>Index of {$data.httpdirectory}</h1>
    
  <table>
    <tr><th>Name</th><th>Size</th><th>Author</th><th>Uploader</th><th>License</th></tr>
{if $data.httpdirectory != '/'}
    <tr><td><a href="../">Parent Directory</a></td><td>&lt;DIR&gt;</td><td>N/A</td><td>N/A</td><td>N/A</td></tr>
{/if}
{foreach from=$data.directories item=directoryname name=directories}
    <tr><td><a href="{$data.httpdirectory}{$directoryname}/">{$directoryname}</a></td><td>&lt;DIR&gt;</td><td>N/A</td><td>N/A</td><td>N/A</td></tr>
{/foreach}
{foreach from=$data.files item=file name=files}
    <tr>
      <td><a href="{$data.httpdirectory}{$file.filename}">{$file.filename}</a></td>
      <td>{$file.filesize}</td>
      <td>{if $file.authorname}{$file.authorname}{else}(Unknown){/if}</td>
      <td>{if $file.uploaderfirstname && $file.uploaderlastname}{$file.uploaderfirstname} {$file.uploaderlastname}{else}(Unknown){/if}</td>
      <td>{if $file.licensename}{if $file.licenseurl}<a href="{$file.licenseurl}">{$file.licensename}</a>{else}{$file.licensename}{/if}{else}(Unknown){/if}</td>
    </tr>
{/foreach}
{if !$data.directories && !$data.files}
    <tr><td colspan="5">No files or folders exist in this directory</td></tr>
{/if}
  </table>
</body>
</html>
