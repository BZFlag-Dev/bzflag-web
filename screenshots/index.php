<?
include "../document.php";

$doc = new Document;
$doc->begin("screenshots");
print <<< end
<h1>Screenshots</h1>
<p>Feel free to <a href="mailto:learner AT brlcad DOT org">submit a screenshot</a> that you would like to see added.  Screenshots can be created while playing BZFlag by pressing the F5 key.  This will generate either a raw image (sgi format) or a gamma-corrected png, depending on your version of BZFlag.</p>

<p>Fullsize mages should be converted to either jpeg or png format.  Any resolution is okay, but please include a jpeg thumbnail if you would.  Thumbnail images should be approximately no more than 128 pixels wide, preserving the original aspect ratio.  <tiny>(*nix users can try running "<code>djpeg image.jpg  | pnmscale -pixels 16384 | cjpeg -progressive > t-image.jpg</code>")</tiny></p>

<br>

<table border=0 cellspacing=16 ><tr><td align="center">

<table border=0><tr><td align="center">
<a href="c3po.jpg"><img src="t-c3po.jpg" border=0 alt="he didn't even see him coming"></a>
</td></tr><tr><td align="center">
c3po destroys a guided missle shooter with grace and style
</td></tr></table>

</td></tr><tr><td align="center">

<table border=0><tr><td align="center">
<a href="bzadminphone.jpg"><img src="t-bzadminphone.jpg" border=0 alt="now if we could just play bzflag on it too..."></a>
</td></tr><tr><td align="center">
Nidhoggr shows bzadmin running on his phone
</td></tr></table>

</td></tr></table>

<br><br>
<hr>
<p>These are some screenshots of the experimental 1.8 version.  Much of the effort for this release was included in the 1.10 version.</p>
<br>
<a href="bzfi0000.jpg"><img src="t-bzfi0000.jpg" border="0" alt="screenshot 0000"></a>
<a href="bzfi0001.jpg"><img src="t-bzfi0001.jpg" border="0" alt="screenshot 0001"></a>
<a href="bzfi0002.jpg"><img src="t-bzfi0002.jpg" border="0" alt="screenshot 0002"></a>
<a href="bzfi0003.jpg"><img src="t-bzfi0003.jpg" border="0" alt="screenshot 0003"></a>
<a href="bzfi0004.jpg"><img src="t-bzfi0004.jpg" border="0" alt="screenshot 0004"></a>
<a href="bzfi0005.jpg"><img src="t-bzfi0005.jpg" border="0" alt="screenshot 0005"></a>
<a href="bzfi0006.jpg"><img src="t-bzfi0006.jpg" border="0" alt="screenshot 0006"></a>
<a href="bzfi0007.jpg"><img src="t-bzfi0007.jpg" border="0" alt="screenshot 0007"></a>
<a href="bzfi0008.jpg"><img src="t-bzfi0008.jpg" border="0" alt="screenshot 0008"></a>
<a href="bzfi0009.jpg"><img src="t-bzfi0009.jpg" border="0" alt="screenshot 0009"></a>
<a href="bzfi0010.jpg"><img src="t-bzfi0010.jpg" border="0" alt="screenshot 0010"></a>
<a href="bzfi0011.jpg"><img src="t-bzfi0011.jpg" border="0" alt="screenshot 0011"></a>
<a href="bzfi0012.jpg"><img src="t-bzfi0012.jpg" border="0" alt="screenshot 0012"></a>
<a href="bzfi0013.jpg"><img src="t-bzfi0013.jpg" border="0" alt="screenshot 0013"></a>
<a href="bzfi0014.jpg"><img src="t-bzfi0014.jpg" border="0" alt="screenshot 0014"></a>
<a href="bzfi0015.jpg"><img src="t-bzfi0015.jpg" border="0" alt="screenshot 0015"></a>
<a href="bzfi0016.jpg"><img src="t-bzfi0016.jpg" border="0" alt="screenshot 0016"></a>
<a href="bzfi0017.jpg"><img src="t-bzfi0017.jpg" border="0" alt="screenshot 0017"></a>
<a href="bzfi0018.jpg"><img src="t-bzfi0018.jpg" border="0" alt="screenshot 0018"></a>
<a href="bzfi0019.jpg"><img src="t-bzfi0019.jpg" border="0" alt="screenshot 0019"></a>
<a href="bzfi0020.jpg"><img src="t-bzfi0020.jpg" border="0" alt="screenshot 0020"></a>
<a href="bzfi0021.jpg"><img src="t-bzfi0021.jpg" border="0" alt="screenshot 0021"></a>
<a href="bzfi0022.jpg"><img src="t-bzfi0022.jpg" border="0" alt="screenshot 0022"></a>
<a href="bzfi0023.jpg"><img src="t-bzfi0023.jpg" border="0" alt="screenshot 0023"></a>
<a href="bzfi0024.jpg"><img src="t-bzfi0024.jpg" border="0" alt="screenshot 0024"></a>
<a href="bzfi0025.jpg"><img src="t-bzfi0025.jpg" border="0" alt="screenshot 0025"></a>
<a href="bzfi0026.jpg"><img src="t-bzfi0026.jpg" border="0" alt="screenshot 0026"></a>
<a href="bzfi0027.jpg"><img src="t-bzfi0027.jpg" border="0" alt="screenshot 0027"></a>
<a href="bzfi0028.jpg"><img src="t-bzfi0028.jpg" border="0" alt="screenshot 0028"></a>
<a href="bzfi0029.jpg"><img src="t-bzfi0029.jpg" border="0" alt="screenshot 0029"></a>
<a href="bzfi0030.jpg"><img src="t-bzfi0030.jpg" border="0" alt="screenshot 0030"></a>

<hr>
<h1>Other Images</h1>

<p>Here are some fun images to look at.  Feel free to <a href="mailto:learne AT brlcad DOT org">submit</a> other BZFlag-related artwork.</p>
<br>

<table border=0 cellspacing=16 ><tr><td align="center">

<table border=0><tr><td align="center">
<a href="bzkitty.jpg"><img src="t-bzkitty.jpg" border=0 alt="bring it on..."></a>
</td></tr><tr><td align="center">
daBomb's kitty is ready for battle
</td></tr></table>

</td></tr></table>

<!--

<hr>
<h1>Other Images</h1>

<p>Here are some fun half-random images to look at.  Feel free to <a href="mailto:learne AT brlcad DOT org">submit</a> other BZFlag-related artwork.</p>
<br>
<table border=0><tr><td align="center">
<img src="" border=0 alt="">
</td></tr><tr><td align="center">
picture comment goes here
</td></tr></table>
-->

end;
$doc->end();

?>
