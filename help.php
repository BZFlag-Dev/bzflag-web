<? include('header.inc') ?>

<p>I'm still writing the help page. If you think there should be
something here that isn't, <a
href="http://www.noodleroni.com/contact.php">email me</a>.</p>

<p>
<a href="#outdated">Why doesn't <? echo $_SERVER['SERVER_NAME']; ?> show someone online that I know is online?</a><br />
<a href="#strengthindex">What is the Strength Index?</a>
</p>

<h2>
<a name="outdated"></a>
Why doesn't <? echo $_SERVER['SERVER_NAME']; ?> show someone online that I know is online?
</h2>

<p>Several things can be happening to cause this:</p>

<ul>
<li>The database hasn't been refreshed recently enough.

<ul>
<li>The database is set to refresh every 5 minutes. We try to keep a
balance of keeping the database current and not annoying the admins of
the servers. If you are on for at least 5 minutes, you will be in the
database.</li>
</ul></li>

<li>You might have requested a page right when the database is being refreshed.

<ul>
<li>Every 5 minutes, the backend scripts go through the public servers
one by one, download the information, clear the old information, and
finally input the new information. There will be about 2 seconds
between the time the old info is cleared, and the new info has been
completely entered. If you happen to request a page in that 2 seconds,
the player list might not be complete. Wait about 5 seconds and
refresh the page.</li>
</ul></li>
</ul>

<p><a href="#top">Top</a></p>

<h2>
<a name="strengthindex"></a>
What is the Strength Index?
</h2>

<p>The Strength Index is a rough measurement of a player's skill which
basically means, if a player has a higher Strength Index than you
have, that means they are better than you. :-P The exact formula for
the Strength Index is:</p>

<p><code>($score / $wins) * abs($score / 2)</code></p>

<p><a href="#top">Top</a></p>

<? include('footer.inc') ?>

