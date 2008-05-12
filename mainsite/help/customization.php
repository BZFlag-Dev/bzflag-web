<?
include "../document.php";

$doc = new Document;
$doc->begin("customization");
print <<< end
<b>Customizing BZFlag</b><br><br>

This document only applies to version 1.7. Earlier versions are customized differently. Run `man bzflagrc' on your system for more information.<br>
<b>Sound Files</b><br><br>

You can customize the sounds that BZFlag plays by getting it to load your
own sound files. You can do this by replacing the original sound files
themselves; by putting sound files in another directory and using the
-directory command line option; or by copying the original data files to
a new location, replacing the sound files, and using the -directory option.
On UNIX, you can of course use links to data files instead of making copies.<br><br>

The sound files must have the original name. The files are 22.05kHz 16 bit WAV
files, stereo or mono. The sound files are (not including the usual extension .wav):<br>
<table border="0" width="100%">
<tr><td>boom</td><td>Played when a shell explodes.</td></tr>
<tr><td>explosion</td><td>Played when a tank explodes.</td></tr>
<tr><td>fire</td><td>Played when a shot is fired.</td></tr>
<tr><td>flag_alert</td><td>Played when your team flag is grabbed by an enemy.</td></tr>
<tr><td>flag_drop</td><td>Played when a flag is dropped.</td></tr>
<tr><td>flag_grab</td><td>Played when a flag is grabbed.</td></tr>
<tr><td>flag_lost</td><td>Played when your team flag is captured.</td></tr>
<tr><td>flag_won</td><td>Played when you capture an enemy flag.</td></tr>
<tr><td>jump</td><td>Played when a tank jumps.</td></tr>
<tr><td>land</td><td>Played when a tank lands.</td></tr>
<tr><td>laser</td><td>Played when the laser is fired.</td></tr>
<tr><td>pop</td><td>Played when a tank appears in the game.</td></tr>
<tr><td>ricochet</td><td>Played when a shot ricochets.</td></tr>
<tr><td>shock</td><td>Played when the shock wave is fired.</td></tr>
<tr><td>teleport</td><td>Played when a tank is teleported.</td></tr>
</table><br>
<b>Configuration File</b><br><br>

BZFlag saves state across invokations in a file. On UNIX systems, this file is
~/.bzflag or ~/.bzflag.$(HOST); the latter is used only if HOST is defined. On
Windows systems, this file is bzflag.bzc and it's in your profile directory.
The format of the file is the same regardless of platform.<br><br>

This page describes the format of the configuration file and
the options that BZFlag understands and uses.<br><br>

Not all options are available through the GUI or the command line. Options
only available through the configuration file are marked in bold. Options
not available through the GUI are marked in italics. Some options available
through the GUI are not available from the command line; these are not marked.<br><br>
<b>Format</b><br><br>

Each line in the file begins with a name (containing no whitespace) followed
by whitespace, followed by a value. The value begins at the first non-whitespace
character and includes all characters to the end of the line.<br><br>

<b>Server Options</b><br><br>
<table border="0" width="100%">
<tr><td>callsign</td><td>Last callsign (see -callsign).</td></tr>
<tr><td>email</td><td>Last email (see -email).</td></tr>
<tr><td>list</td><td>Set the server list server url (see -list).</td></tr>
<tr><td>port</td><td>Last server port number (see -port).</td></tr>
<tr><td>server</td><td>Last server.</td></tr>
<tr><td>team Last</td><td>team name (see -team).</td></tr>
<tr><td>interface</td><td>Last multicast interface (see -interface).</td></tr>
<tr><td>startcode</td><td>Used for start server menu. Use the GUI to modify this.</td></tr>
</table><br>

<b>Rendering Options</b><br><br>
<table border="0" width="100%">
<tr><td>blend</td><td>yes/on -- turns blending on or off.</td></tr>
<tr><td>dither</td><td>yes/no -- turns dithering on or off.</td></tr>
<tr><td>gamma</td><td>Sets the gamma correction value (brightness).</td></tr>
<tr><td>geometry</td><td>Set the window geometry; uses standard X geometry format.</td></tr>
<tr><td>lighting</td><td>yes/no -- turns lighting on or off.</td></tr>
<tr><td>quality</td><td>low/medium/high -- sets the rendering quality level.</td></tr>
<tr><td>resolution</td><td>Sets the default screen resolution.</td></tr>
<tr><td>shadows</td><td>yes/no -- turns shadows on or off.</td></tr>
<tr><td>smooth</td><td>yes/no -- turns point & line antialiasing on or off.</td></tr>
<tr><td>texture</td><td>Sets the texturing filter or turns texturing off. Values can be: no, nearest, linear, nearestmipmapnearest, linearmipmapnearest, nearestmipmaplinear, and linearmipmaplinear.</td></tr>
<tr><td>view</td><td>normal/stereo/three -- see the -view command line option.</td></tr>
<tr><td>zbuffer</td><td>yes/no/disable -- turns the zbuffer algorithm on or off. No zbuffer is allocated when disabled.</td></tr>
<tr><td>eyesep</td><td>Sets the distance between the eyes for stereo rendering.</td></tr>
<tr><td>focal</td><td>Sets the distance to the focal plane for stereo rendering.</td></tr>
<tr><td>maxlod</td><td>Sets the maximum level of detail to render. Walls are meshed when lighting is on and this limits the number of polygons that will be generated.</td></tr>
<tr><td>zbuffersplit</td><td>yes/no -- uses a hack to clear the zbuffer less often, at expense of depth resolution.</td></tr>
<tr><td>zoom</td><td>1-8 -- renders at a low resolution and zooms pixels by this factor.</td></tr>
<tr><td>redcolor</td><td>Three floating point numbers between 0 and 1 (inclusive) -- Gives the red, green, and blue proportions (respectively) of a red team tank's color. This is mainly intended for colorblind users to adjust team colors to something they can unambiguously perceive.</td></tr>
<tr><td>greencolor</td><td>Like redcolor but for green team tanks.</td></tr>
<tr><td>bluecolor</td><td>Like redcolor but for blue team tanks.</td></tr>
<tr><td>purplecolor</td><td>Like redcolor but for purple team tanks.</td></tr>
<tr><td>redradar</td><td>Three floating point numbers between 0 and 1 (inclusive) -- Gives the red, green, and blue proportions (respectively) of a red team tank's color on radar. This is mainly intended for colorblind users to adjust team colors to something they can unambiguously perceive.</td></tr>
<tr><td>greenradar</td><td>Like redradar but for green team tanks.</td></tr>
<tr><td>blueradar</td><td>Like redradar but for blue team tanks.</td></tr>
<tr><td>purpleradar</td><td>Like redradar but for purple team tanks.</td></tr>
<tr><td>enhancedradar</td><td>Enables blending of buildings on radar based on height.</td></tr>
</table><br>

<b>Key Options</b><br><br>

Key values are either a single key's name or two key names separated by a
slash (e.g. =/+). The mouse buttons are treated as keys and named `Left
Mouse', `Middle Mouse', and `Right Mouse'. Special keys are named by the
label on the US keyboard. For example, F1, Tab, Home, etc. The spacebar
uses the name `Space'. Other keys use their face character; for example,
A, B, Z, 4, =, }.<br><br>

Note that BZFlag uses some keys that don't have options, so they cannot
be customized. The frames per second toggle is such a key. These functions
are masked if a customizable key uses that key.<br><br>
<table border="0" width="100%">
<tr><td>binocularsKey</td><td>Toggles binoculars.</td></tr>
<tr><td>dropFlagKey</td><td>The key to drop a flag.</td></tr>
<tr><td>fireKey</td><td>The key to shoot.</td></tr>
<tr><td>flagHelpKey</td><td>Toggles flag help.</td></tr>
<tr><td>identifyKey</td><td>The key to identify/lock-on a target.</td></tr>
<tr><td>jumpKey</td><td>Jumps.</td></tr>
<tr><td>pauseKey</td><td>Pauses the game.</td></tr>
<tr><td>scoreKey</td><td>Toggles the score sheet.</td></tr>
<tr><td>shortRangeKey</td><td>Sets the radar to short range.</td></tr>
<tr><td>mediumRangeKey</td><td>Sets the radar to medium rage.</td></tr>
<tr><td>longRangeKey</td><td>Sets the radar to long range.</td></tr>
<tr><td>sendAllKey</td><td>Sends a message to everybody</td></tr>
<tr><td>sendTeamKey</td><td>Sends a message to teammates.</td></tr>
<tr><td>sendNemesisKey</td><td>Sends a private message to last victim or killer.</td></tr>
<tr><td>sendRecipientKey</td><td>Sends a private message to a player.</td></tr>
<tr><td>timeBackwardKey</td><td>Changes the time of day backward.</td></tr>
<tr><td>timeForwardKey</td><td>Changes the time of day forward.</td></tr>
</table><br>

ALT-F1 to ALT-F10 and CTRL-F1 to CTRL-F10 lets you send predefined
messages to all players resp. to your team. You can define these
messages in your config file:

<table border="0" width="100%">
<tr><td>quickMessage1</td><td>arbitrary text -- define quick message for ALT-F1</td></tr>
<tr><td>quickMessage2</td><td>arbitrary text -- define quick message for ALT-F2</td></tr>
<tr><td>...</td><td>...</td></tr>
<tr><td>quickTeamMessage1</td><td>arbitrary text -- define quick team message for CTRL-F1</td></tr>
<tr><td>quickTeamMessage2</td><td>arbitrary text -- define quick team message for CTRL-F2</td></tr>
<tr><td>...</td><td>...</td></tr>
</table><br>

<b>Other Options</b><br><br>
<table border="0" width="100%">
<tr><td>directory</td><td>The directory where the data files are.</td></tr>
<tr><td>volume</td><td>0-10 -- sets the sound effect volume.</td></tr>
<tr><td>latitude</td><td>The latitude used for celestial calculations.</td></tr>
<tr><td>longitude</td><td>The longitude used for celestial calculations.</td></tr>
<tr><td>fakecursor</td><td>yes/no -- when on BZFlag draws a cross hair where the cursor is. This is primarily useful on 3D pass-through cards like the 3Dfx Voodoo.</td></tr>
<tr><td>mousegrab</td><td>yes/no -- when off BZFlag will not grab the mouse under any circumstances. BZFlag will otherwise grab the mouse when it thinks not grabbing it may interfere with the game.</td></tr>
</table>
end;
$doc->end();

?>
