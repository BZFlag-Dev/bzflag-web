<? include ('header.inc'); ?>

<h2>News:</h2>

<p><strong>1/19/2004:</strong> Check out the <a href="http://files.noodleroni.com/pub/bzflag/nightlies/">nightly RPM builds</a> of the BZFlag code in CVS.</p>

<p><strong>1/16/2004:</strong> I've finally put up the backend script that is used to refresh the database. Download it <a href="http://files.noodleroni.com/pub/bzflag/bzupdate.pl">here</a>.</p>

<p><strong>1/11/2004:</strong> A happy new layout for the happy New Year!</p>

<p><strong>12/22/2003:</strong> Check out the new server info pages and the new <a href="http://<? echo $_SERVER['SERVER_NAME']; ?>/serverlist.php">server list</a> page. <? echo $_SERVER['SERVER_NAME']; ?> - More Useful, Every Day!&trade;</p>

<p><strong>12/15/2003:</strong> I've updated <a
href="http://search.cpan.org/~tuckerm/BZFlag-Info/Info.pm">BZFlag::Info</a>
to support 1.10 servers! It also reports more fun junk, for all you
fun junk junkies out there. <? echo $_SERVER['SERVER_NAME']; ?> also is now only querying the 1.10
servers.</p>

<!-- <p><strong>11/29/2003:</strong> <? echo $_SERVER['SERVER_NAME']; ?> now supports multiple
languages! And, thanks to <a
href="http://tupone.dyndns.org/">Tupone</a>, we now have an Italian
translation as well! We are also looking for more people who are
willing to do a translation in theur mother tongue. Contact me if you
would like to contribute.</p>-->

<p><strong>11/21/2003:</strong> <a
href="http://search.cpan.org/~tuckerm/BZFlag-Info/">BZFlag::Info</a>
is now available through <a
href="http://search.cpan.org/">CPAN</a>!</p>

<p><strong>11/13/2003:</strong> I've packaged up the code for
retrieving information about servers and players into a Perl module
called <a
href="http://search.cpan.org/~tuckerm/BZFlag-Info/Info.pm">BZFlag::Info</a>,
which you can download from <a
href="http://files.noodleroni.com/pub/bzflag/BZFlag-Info-1.7.1.tar.gz">this
server</a> or soon from <a
href="http://search.cpan.org/">CPAN</a>!</p>

<p><strong>11/01/2003:</strong> Due to popular demand (well, just one
request, for now :-) ) the <a href="maps/">maps</a> are again
available for download.</p>

<p><strong>10/31/2003:</strong> <? echo $_SERVER['SERVER_NAME']; ?> is back! It has been
completely redesigned, and should now work much smoother and look more
consistent. <a href="http://www.noodleroni.com/contact.php">Drop me a
line</a>, and give me some feedback.</p>

<?=$separator?>

<h2>Commonly queried servers:</h2>

<ul>
<li><?=serverlink('bzflag.hashmark.net:5154')?></li>
<li><?=serverlink('dragon.org:5255')?></li>
<li><?=serverlink('ducati.bzflag.org:5156')?></li>
<!--<?=serverlink('quol.bzflag.org:8083')?><br />-->
<li><?=serverlink('xmission.bzflag.org:5154')?></li>
</ul>

<?=$separator?>

<h2>Current facts: (sampled every 2 minutes)</h2>

<table>
<tr class="evenrow"><td>Most popular server</td><td><?

$currentplayers = mysql_query ("SELECT server,players FROM `serverinfo` ORDER BY `players` DESC LIMIT 1");

if ($row = mysql_fetch_array($currentplayers, MYSQL_ASSOC)) {
?><?=serverlink($row[server])?> with <strong><?=$row[players]?></strong> players<?
} else {
?>No info available<?
}
?></td></tr>

<tr class="oddrow"><td>Player with highest score</td><td><?
$currentplayers = mysql_query ("SELECT callsign,score FROM `currentplayers` ORDER BY `score` DESC LIMIT 1");

if ($row = mysql_fetch_array($currentplayers, MYSQL_ASSOC)) {
?><?=callsignlink($row[callsign])?> with a score of <strong><?=$row[score]?></strong><?
} else {
?>No info available<?
}

?></td></tr>


<tr class="evenrow"><td>Player with highest Strength Index <a class="help" href="help.php#strengthindex">?</a></td><td><?
$currentplayers = mysql_query ("SELECT callsign,strengthindex FROM `currentplayers` ORDER BY `strengthindex` DESC LIMIT 1");

if ($row = mysql_fetch_array($currentplayers, MYSQL_ASSOC)) {
?><?=callsignlink($row[callsign])?> with a Strength Index of <strong><?=number_format($row[strengthindex], 3)?></strong><?
} else {
?>No info available<?
}

?></td></tr>


<tr class="oddrow"><td>Player with lowest score</td><td><?
$currentplayers = mysql_query ("SELECT callsign,score FROM `currentplayers` ORDER BY `score` LIMIT 1");

if ($row = mysql_fetch_array($currentplayers, MYSQL_ASSOC)) {
?><?=callsignlink($row[callsign])?> with a score of <strong><?=$row[score]?></strong><?
} else {
?>No info available<?
}

?></td></tr>


<tr class="evenrow"><td>Player with lowest Strength Index <a class="help" href="help.php#strengthindex">?</a></td><td><?
$currentplayers = mysql_query ("SELECT callsign,strengthindex FROM `currentplayers` ORDER BY `strengthindex` LIMIT 1");

if ($row = mysql_fetch_array($currentplayers, MYSQL_ASSOC)) {
?><?=callsignlink($row[callsign])?> with a Strength Index of <strong><?=number_format($row[strengthindex], 3)?></strong><?
} else {
?>No info available<?
}

?></td></tr>


<tr class="oddrow"><td>Player that is the rabbit on <?=serverlink('dragon.org:5255')?></td><td><?
$currentplayers = mysql_query ("SELECT callsign,score FROM `currentplayers` WHERE team = 'H' ORDER BY `score` DESC LIMIT 1");

if ($row = mysql_fetch_array($currentplayers, MYSQL_ASSOC)) {
?><?=callsignlink($row[callsign])?> with a score of <strong><?=$row[score]?></strong><?
} else {
?>No info available<?
}

?></td></tr>


<tr class="evenrow"><td>Team on <?=serverlink('ducati.bzflag.org:5156')?> that is kicking butt</td><td><?
$currentplayers = mysql_query ("SELECT server,team,score FROM `teaminfo` WHERE `server` = 'ducati.bzflag.org:5156' ORDER BY score DESC");

if ($row = mysql_fetch_array($currentplayers, MYSQL_ASSOC)) {
?><?=teamname($row[team])?> with <strong><?=$row[score]?></strong><?
} else {
?><strong>Nobody</strong><?
}

?></td></tr>

<tr class="oddrow"><td>Team on <?=serverlink('xmission.bzflag.org:5154')?> that is doing the same</td><td><?
$currentplayers = mysql_query ("SELECT server,team,score FROM `teaminfo` WHERE `server` = 'xmission.bzflag.org:5154' ORDER BY score DESC");

if ($row = mysql_fetch_array($currentplayers, MYSQL_ASSOC)) {
?><?=teamname($row[team])?> with <strong><?=$row[score]?></strong><?
} else {
?><strong>Nobody</strong><?
}

?></td></tr>

</table>

<? include ('footer.inc'); ?>
