<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01//EN' 'http://www.w3.org/TR/html4/strict.dtd'>
<html>
<head>
<title>{$title}</title>
<link rel="stylesheet" type="text/css" href="bztheme.css">

{$javascript}

</head>
<body>

<p align="center">
	{foreach from=$links item=URL key=title}
	<a href="{$URL}">{$title}</a> 
	{/foreach}
</p>

<br><br>

{$text}
</body>
</html>
