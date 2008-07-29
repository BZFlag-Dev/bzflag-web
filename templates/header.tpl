<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <link href="/favicon.ico" rel="shortcut icon">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
{if $page.refresh}
  <meta http-equiv="Refresh" content="50">
{/if}
{if $page.theme != 'none'}
  <link rel="stylesheet" type="text/css" href="css/{$page.theme}.css">
  <link rel="stylesheet" type="text/css" media="print" href="css/print.css?" />
{/if}
  <title>{$page.servername}{if $page.title} - {$page.title}{/if}</title>
</head>
<body>

<div id="banner">

<div id="menubar">
<a{if $page.area == 'home'} class="active"{/if} href="{$page.baseURL}">Home</a> <a{if $page.area == 'players'} class="active"{/if} href="{$page.baseURL}currentplayers.php">Players</a> <a{if $page.area == 'servers'} class="active"{/if} href="{$page.baseURL}serverlist.php">Servers</a> <a{if $page.area == 'help'} class="active"{/if} href="{$page.baseURL}help.php">Help</a>
</div>

<a name="top"></a>
</div>

<div id="main">

<!--<div id="languages">
Languages: <a href="{$page.baseURL}?language=en"><img src="{$page.baseURL}flags/f0-us.gif" alt="en" /></a> | <a href="{$page.baseURL}?language=it"><img src="{$page.baseURL}flags/f0-it.gif" alt="it" /></a>
</div>-->

<h1>{$page.servername}{if $page.title} - {$page.title}{/if}</h1>

<div id="menu">
<div class="{if $page.area == 'home'}menusectionactive{else}menusectionhome{/if}"><a href="/">Home</a></div>

<div class="{if $page.area == 'players'}menusectionactive{else}menusection{/if}">
Players
<ul>
<li><a href="currentplayers.php">Current Players</a></li>
<li><a href="playerstats.php">Player Stats</a></li>
<li><a href="playersearch.php">Player Search</a></li>
</ul>
</div>

<div class="{if $page.area == 'servers'}menusectionactive{else}menusection{/if}">
Servers
<ul>
<li><a href="serverlist.php">Server List</a></li>
</ul>
</div>

<div class="{if $page.area == 'help'}menusectionactive{else}menusection{/if}"><a href="help.php">Help</a></div>

<div class="menusection">
Other Links
<ul>
<li><a href="http://bzflag.org/">BZFlag.org</a></li>
<li><a href="/bb/">Forums</a></li>
<li><a href="/league/">CTF League</a></li>
<li><a href="http://my.bzflag.org/w">BZFlag Wiki</a></li>
<li><a href="http://my.bzflag.org/w/Download">Download</a></li>
<li><a href="http://my.bzflag.org/w/Other_Links">Other Links</a></li>
<li><em><a href="link.php">Link to us!</a></em></li>
</ul>
</div>

<div class="menusection">
<form action="{$smarty.server.PHP_SELF}" method="get">
<p>Options</p>

<p><select name="theme" style="width: 120px;">
<option value="">Choose a theme</option>
<option value="bluetangerine">Blue Tangerine{if $page.theme == 'bluetangerine'}*{/if}</option>
<option value="industrial">Industrial{if $page.theme == 'industrial'}*{/if}</option>
<option value="test">Test{if $page.theme == 'test'}*{/if}</option>
<option value="none">No theme{if $page.theme == 'none'}*{/if}</option>
</select></p>

<p><input type="checkbox" name="refresh" value="yes"{if $page.refresh} checked="checked"{/if}>
Auto refresh?</p>

<p>
<input type="hidden" name="optionschanged" value="yes">
<input type="submit" value="Save">
</p>
</form>
</div>


<div class="menusection">

<div class="center">
<p><a href="rssgenerator.php"><img src="images/xml.gif" alt="RSS Feed" /></a></p>
<p><a href="link.php"><img src="images/link_{if $theme == 'bluetangerine'}bronze{elseif $theme == 'industrial'}silver{else}blue{/if}.png" alt="got stats?" /></a></p>
<p><a href="http://validator.w3.org/check/referer"><img src="images/valid-xhtml10.png" alt="Valid XHTML 1.0!" /></a></p>
</div>
</div>

</div>

<div id="content">
