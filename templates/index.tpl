<h2>News:</h2>

<p><strong>3/29/2007:</strong> The BZFlag developers have changed the source control system from the project, from the previous CVS to Subversion (SVN). Check out the details on <a href="http://my.bzflag.org/w/BZFlag_SVN"> the wiki</a>.</p><!--donny_baker-->

<p><strong>3/26/2007:</strong> We are happy to announce the arrival of a new and improved wiki system, located at <a href="http://my.bzflag.org/w">http://my.bzflag.org/w</a>. This new wiki is full of information about the game, created by the developers and users alike. Please feel free to expand and enhance it for all.</p><!--donny_baker-->

<!--<p><strong>1/19/2004:</strong> Check out the nightly RPM builds of the BZFlag code in CVS.</p>-->

<!--<p><strong>1/16/2004:</strong> I've finally put up the backend script that is used to refresh the database.</p>-->


<p><strong>1/11/2004:</strong> A happy new layout for the happy New Year!</p>

<p><strong>12/22/2003:</strong> Check out the new server info pages and the new <a href="http://{$page.servername}/serverlist.php">server list</a> page. {$page.servername} - More Useful, Every Day!&trade;</p>

<p><strong>12/15/2003:</strong> I've updated <a
href="http://search.cpan.org/~tuckerm/BZFlag-Info/Info.pm">BZFlag::Info</a>
to support 1.10 servers! It also reports more fun junk, for all you
fun junk junkies out there. {$page.servername} also is now only querying the 1.10
servers.</p>

<!-- <p><strong>11/29/2003:</strong> {$page.servername} now supports multiple
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
which you can download from href="http://search.cpan.org/">CPAN</a>!</p>

<!--<p><strong>11/01/2003:</strong> Due to popular demand (well, just one
request, for now :-) ) the <a href="maps/">maps</a> are again
available for download.</p>-->

<p><strong>10/31/2003:</strong> {$page.servername} is back! It has been completely redesigned, and should now work much smoother and look more
consistent. Drop me a line</a>, and give me some feedback.</p>

{$page.separator}

<h2>Commonly queried servers:</h2>

<!-- This list should be generated statistically -->
<ul>
{foreach from=$data.commonservers item=server name=commonservers}
<li><a href="serverinfo.php?server={$server|escape:'url'}">{$server}</a></li>
{/foreach}
</ul>

{$page.separator}

<h2>Current facts: (sampled every 5 minutes)</h2>

<table>
<tr class="evenrow"><td>Most popular server</td><td>
{if $data.mostpopularserver}
<a href="serverinfo.php?server={$data.mostpopularserver.server|escape:'url'}">{$data.mostpopularserver.server}</a> with <strong>{$data.mostpopularserver.players}</strong> players
{else}
No info available
{/if}
</td></tr>

<tr class="oddrow"><td>Player with highest score</td><td>
{if $data.playerhighestscore}
<a href="playerinfo.php?callsign={$data.playerhighestscore.callsign|escape:"url"}">{$data.playerhighestscore.callsign|escape:"hexentity"}</a> on <a href="serverinfo.php?server={$data.playerhighestscore.server|escape:'url'}">{$data.playerhighestscore.server}</a> with a score of <strong>{$data.playerhighestscore.score}</strong>
{else}
No info available
{/if}
</td></tr>


<tr class="evenrow"><td>Player with highest Strength Index <a class="help" href="help.php#strengthindex">?</a></td><td>
{if $data.playerhigheststrength}
<a href="playerinfo.php?callsign={$data.playerhigheststrength.callsign|escape:"url"}">{$data.playerhigheststrength.callsign|escape:"hexentity"}</a> on <a href="serverinfo.php?server={$data.playerhigheststrength.server|escape:'url'}">{$data.playerhigheststrength.server}</a> with a Strength Index of <strong>{$data.playerhigheststrength.strengthindex|string_format:"%.3f"}</strong>
{else}
No info available
{/if}
</td></tr>


<tr class="oddrow"><td>Player with lowest score</td><td>
{if $data.playerlowestscore}
<a href="playerinfo.php?callsign={$data.playerlowestscore.callsign|escape:"url"}">{$data.playerlowestscore.callsign|escape:"hexentity"}</a> on <a href="serverinfo.php?server={$data.playerlowestscore.server|escape:'url'}">{$data.playerlowestscore.server}</a> with a score of <strong>{$data.playerlowestscore.score}</strong>
{else}
No info available
{/if}
</td></tr>


<tr class="evenrow"><td>Player with lowest Strength Index <a class="help" href="help.php#strengthindex">?</a></td><td>
{if $data.playerloweststrength}
<a href="playerinfo.php?callsign={$data.playerloweststrength.callsign|escape:"url"}">{$data.playerloweststrength.callsign|escape:"hexentity"}</a> on <a href="serverinfo.php?server={$data.playerloweststrength.server|escape:'url'}">{$data.playerloweststrength.server}</a> with a Strength Index of <strong>{$data.playerloweststrength.strengthindex|string_format:"%.3f"}</strong>
{else}
No info available
{/if}
</td></tr>

</table>
