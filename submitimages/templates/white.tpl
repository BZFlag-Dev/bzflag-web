<html>
<head>
<title>{$title}</title>
{$javascript}
</head>
<body>
<h1>{$title}</h1>

{foreach from=$links item=URL key=title}
<a href="{$URL}">{$title}</a> 
{/foreach}
<br><br>

{$text}
</body>
</html>