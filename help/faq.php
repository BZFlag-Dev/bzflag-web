<?
include "../document.php";

$doc = new Document;
$doc->begin("frequently asked questions");
print <<< end
These are some questions that just don't fit anywhere else.<br><br>
<b>What is OpenGL?</b><br>
OpenGL is a portable graphics library. Its an open standard, which means that
no single company controls it and improvements are agreed upon by all. Its one
of the reasons BZFlag works on so many different platforms.<br><br>

<b>What does "Oscillation Overthruster" mean?</b><br>
The term comes from the movie "The Adventures of Buckaroo Banzai: Across the Eighth
Dimension." In it our hero B.B. and his team of scientists invent the "oscillation
overthruster," a device which allows matter to move effortlessly through other matter.
B.B. demonstrates by driving his rocket car through a mountain. (The special effects
used for the inside of the mountain are a lot cooler than the floating triangles
inside buildings in BZFlag).<br><br>

<b>What's with the latitude and longitude command line options?</b><br>
You can tell BZFlag the latitude and longitude of the host you're using and it'll
calculate the sun position based on them. For example, if you used a latitude of 90
and any longitude, you'd be at the north pole; the sun would never come up in the winter
and never set in the summer. The sun, moon, and star positions are based on the latitude,
longitude, time of day, and time of year and should be fairly accurate. The phase of the
moon is also accurately computed. This feature is more of a curiosity than something useful.<br><br>

<b>How come i hear distant explosions well after i see the explosion?</b><br>
BZFlag's audio attempts to mimic reality. While a jumping tank might not be realistic
sound does in fact travel slower than light. This is the same effect as when watching fireworks.
If you listen carefully to the shockwave, you might also hear the doppler effect in action.<br><br>

<b>Are there any cheat codes?</b><br>
No.<br><br>

<b>Why not?</b><br>
Cheat codes don't really make sense in a multiplayer game. If one player cheats, it ruins
it for everybody. Soon everyone would be cheating, which just makes gameplay bad.<br><br>

<b>But I see cheaters all the time?!?</b><br>
Yeah, unfortunately. Because of the open nature of BZFlag, its not too difficult for
someone with programming experience to create a cheat client. At the moment the best
solution is to find the server admin or just change servers when a cheater shows up.
end;
$doc->end();

?>
