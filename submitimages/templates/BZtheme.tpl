<html>
<head>
<title>{$title}</title>
<link rel="stylesheet" type="text/css" href="bztheme.css">

{$javascript}

</head>
<body background="std_ground.jpg">

<p align="center">
	{foreach from=$links item=URL key=title}
	<a href="{$URL}">{$title}</a> 
	{/foreach}
</p>

<br><br>

{$text}
</body>
</html>