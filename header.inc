<? include ('handycode.inc');

if (isset($viewsource)) { $title = "View Source"; }

 ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<? if ($_SERVER['PHP_SELF'] != "/help.php" and $_SERVER['PHP_SELF'] != "/copyright.php" and $_SERVER['PHP_SELF'] != "/link.php" and $_SERVER['PHP_SELF'] != "/rssgenerator.php" and (!isset($viewsource))) {
	if ($refresh == "yes") {
?><meta http-equiv="Refresh" content="50" />
<?
	}
} ?>
<? if ($theme!='none') { ?>
<link rel="stylesheet" type="text/css" href="<?=(isset($test))?'css/test.css':"css/$theme.css"?>?" />
<?=(isset($print))?'<style type="text/css">@import css/print.css</style>':''?>
<link rel="stylesheet" type="text/css" media="print" href="css/print.css?" />
<? } ?>
<title><? echo $_SERVER['SERVER_NAME']; ?> - <?=(isset($title) ? $title : "I'm working on it!")?></title>
</head>

<!-- Theme: <?=$theme?> -->
<!-- Refresh: <?=$refresh?> -->
<body>

<div id="banner">

<div id="menubar"><?

if ($_SERVER['PHP_SELF'] == '/stats.php') {
      ?><a class="active" href="stats.php">Home</a> <?
       $markhome = 'yes';
    } else {
  ?><a href="stats.php">Home</a> <?
    }

if ($_SERVER['PHP_SELF'] == '/currentplayers.php' or
    $_SERVER['PHP_SELF'] == '/playerstats.php' or
    $_SERVER['PHP_SELF'] == '/playersearch.php' or
    $_SERVER['PHP_SELF'] == '/playerinfo.php') {
      ?><a class="active" href="currentplayers.php">Players</a> <?
       $markplayers = 'yes';
    } else {
  ?><a href="currentplayers.php">Players</a> <?
    }

if ($_SERVER['PHP_SELF'] == '/serverlist.php' or
    $_SERVER['PHP_SELF'] == '/serverinfo.php') {
      ?><a class="active" href="serverlist.php">Servers</a> <?
       $markservers = 'yes';
    } else {
  ?><a href="serverlist.php">Servers</a> <?
    }

if ($_SERVER['PHP_SELF'] == '/help.php') {
      ?><a class="active" href="help.php">Help</a> <?
       $markhelp = 'yes';
    } else {
  ?><a href="help.php">Help</a> <?
    }

?></div>

<a name="top"></a>
</div>

<div id="main">

<!--<div id="languages">
Languages: <a href="<?=$_SERVER['PHP_SELF']?>?language=en"><img src="flags/f0-us.gif" alt="en" /></a> | <a href="<?=$_SERVER['PHP_SELF']?>?language=it"><img src="flags/f0-it.gif" alt="it" /></a>
</div>-->

<h1><? echo $_SERVER['SERVER_NAME']; ?> - <?=(isset($title) ? $title : "I'm working on it!")?></h1>

<div id="menu">
<div class="<?=(isset($markhome) ? 'menusectionactive' : 'menusectionhome')?>"><a <?=($_SERVER['PHP_SELF'] == "/stats.php") ? 'class="active" ' : ''?>href="stats.php">Home</a></div>

<div class="<?=(isset($markplayers) ? 'menusectionactive' : 'menusection')?>">
Players
<ul>
<li><a <?=($_SERVER['PHP_SELF'] == "/currentplayers.php") ? 'class="active" ' : ''?>href="currentplayers.php">Current Players</a></li>
<li><a <?=($_SERVER['PHP_SELF'] == "/playerstats.php") ? 'class="active" ' : ''?>href="playerstats.php">Player Stats</a></li>
<li><a <?=($_SERVER['PHP_SELF'] == "/playersearch.php") ? 'class="active" ' : ''?>href="playersearch.php">Player Search</a></li>
</ul>
</div>

<div class="<?=(isset($markservers) ? 'menusectionactive' : 'menusection')?>">
Servers
<ul>
<li><a <?=($_SERVER['PHP_SELF'] == "/serverlist.php") ? 'class="active" ' : ''?>href="serverlist.php">Server List</a></li>
</ul>
</div>

<div class="<?=(isset($markhelp) ? 'menusectionactive' : 'menusection')?>"><a <?=($_SERVER['PHP_SELF'] == "/help.php") ? 'class="active" ' : ''?>href="help.php">Help</a></div>

<div class="menusection">
Other Links
<ul>
<li><a href="http://bzflag.org/">BZFlag.org</a></li>
<li><a href="/irc/irc.cgi">IRC Chat</a></li>
<li><a href="http://bzbb.bzflag.org/">BZBB</a></li>
<li><a href="http://bzflag.org/league/">CTF League</a></li>
<li><a href="http://bzflag.org/cvs/">Nightly Snapshots</a></li>
<li><a href="http://files.noodleroni.com/pub/bzflag/nightlies/">Nightly RPMs</a></li>
<li><em><a href="link.php">Link to us!</a></em></li>
<li><a href="bzfls.php">list server script</a></li>
<li><a href="http://bzflag.org/wiki/OtherLinks">OtherLinks</a></li>
</ul>
</div>

<div class="menusection">
<form action="<?=$_SERVER['PHP_SELF']?>" method="get">
<p>Options</p>

<p><select name="theme" style="width: 120px;">
<option value="">Choose a theme</option>
<option value="bluetangerine">Blue Tangerine<?=($theme == 'bluetangerine')?' *':''?></option>
<option value="industrial">Industrial<?=($theme == 'industrial')?' *':''?></option>
<option value="test">Test<?=($theme == 'test')?' *':''?></option>
<option value="none">No theme<?=($theme == 'none')?' *':''?></option>
</select></p>

<p><input type="checkbox" name="refresh" value="yes" <?=($refresh=='yes')?'checked="checked" ':''?>/>
Auto refresh?</p>

<p>
<input type="hidden" name="optionschanged" value="yes" />
<input type="submit" value="Save" />
</p>
</form>
</div>


<div class="menusection">

<div class="center">
<p><a href="rssgenerator.php"><img src="images/xml.gif" alt="RSS Feed" /></a></p>
<p><a href="link.php"><img src="images/link_<?=($theme == 'bluetangerine')?'bronze':(($theme == 'industrial')?'silver':'blue')?>.png" alt="got stats?" /></a></p>
<p><a href="http://validator.w3.org/check/referer"><img src="images/valid-xhtml10.png" alt="Valid XHTML 1.0!" /></a></p>
</div>
</div>

</div>

<div id="content">

<?

$ossmessage = "<p>This is an open source website! We have nothing to
hide, so take a look at the code, and maybe even use some of it on
your own project. Be sure to look at the <a
href=\"copyright.php\">copyright page</a>. Also, if you find anything
that might be a security concern, please <a
href=\"http://www.noodleroni.com/contact.php\">notify me</a>
immediately!</p>";

if (isset($_GET['viewsource'])) {
	$viewsource = $_GET['viewsource'];
} else {
	$viewsource = '';
}

if ($viewsource == 1) {
	echo $ossmessage;
	highlight_file("./" . $_SERVER['PHP_SELF']);
	include ('footer.inc');
	exit;
}

if ($viewsource == 'header') {
	echo $ossmessage;
	highlight_file("./header.inc");
	include ('footer.inc');
	exit;
}

if ($viewsource == 'footer') {
	echo $ossmessage;
	highlight_file("./footer.inc");
	include ('footer.inc');
	exit;
}

if ($viewsource == 'rss') {
	echo $ossmessage;
	highlight_file("./rss.php");
	include ('footer.inc');
	exit;
}

?>

<!-- End header.inc, begin <?=$_SERVER['PHP_SELF']?> -->
