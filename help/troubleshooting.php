<?
include "../document.php";

$doc = new Document;
$doc->begin("troubleshooting");
print <<< end
This is a list of common problems and their solutions. If your
problem isn't listed here, check the
<a href="http://sourceforge.net/bugs/?group_id=3248">bugs list</a> and
write to the <a href="http://sourceforge.net/mail/?group_id=3248">mailing lists</a>.
<br><br>
<b>BZFlag runs slowly</b><br>
There are two possible causes for this. If your system has an old graphics card,
this can usually be solved by turning off some rendering options through the
options menu. Some options can also trip up certain graphics cards. If your card
is relatively new, you can try turning off shadows or smoothing and seeing if there
is any improvement.<br><br>

<b>Can't connect to servers</b><br>
If you can't connect to a server, the problem is often that your internet connection
is just too slow. There isn't a good fix for this problem, and the best choice is just
to try a different server, preferably one geographically closer to you.<br><br>

<b>All players are not responding</b><br>
A symptom of this problem is that shots seem to come from nowhere and none of the tanks
move around. There are a couple causes for this. The first is that you have a firewall
that filters out UDP. The solution for such a problem is either to adjust the firewall
or turn off UDP networking. The other cause is that BZFlag has incorrectly decided that
it can use multicasting. To fix this, try running bzflag from the command line with the
option "-ttl 0".<br><br>

<b>Missing DSOUND.DLL (Windows)</b><br>
You need DirectSound, a part of DirectX. See
<a href=""></a> for more information.<br><br>

<b>Missing OPENGL32.DLL (Windows)</b><br>
You need OpenGL. See
<a href=""></a> for more information.<br><br>

<b>Missing GLU32.DLL (Windows)</b><br>
You need OpenGL. See
<a href=""></a> for more information.<br><br>

<b>Missing WS2_32.DLL (Windows)</b><br>
You need Winsock 2. See
<a href=""></a> for more information.<br><br>

<b>Couldn't open /dev/dsp (Linux)</b><br>
The game couldn't open the sound device. Sound is disabled when this occurs. Check your
drivers and make sure no other programs are using the device.<br><br>

<b>Audio ioctl failed (Linux)</b><br>
The game was unable to configure the sound device. This may or may not disable sound
within the game.<br><br>

<b>Warning: openMulticast: can't join multicast group</b><br>
This is only a warning and is not important. BZFlag will function normally.<br><br>
end;
$doc->end();

?>
