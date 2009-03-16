<?
include "../document.php";

$doc = new Document;
$doc->begin("get involved");
print <<< end

<p>The best way to contribute to BZFlag is to just jump in.  Edit the
<a href="http://my.bzflag.org/w/">wiki</a> documentation.  <a
href="https://sourceforge.net/tracker2/?func=add&group_id=3248&atid=103248">Post</a>
bug reports.  Review an existing patch.  Fix bugs.  Make your own
patch.</p>

<p>If you <a
href="https://sourceforge.net/tracker2/?func=add&group_id=3248&atid=303248">post
patches</a> to the SourceForge tracker and your work is good, you will
very likely get commit access to work on the BZFlag source code
directly.</p>

<p>These should help get you started:</p>
<ul>
<li><a href="http://sourceforge.net/tracker/?atid=103248&group_id=3248&func=browse">Current bugs</a><br>
<li><a href="http://sourceforge.net/tracker/?atid=303248&group_id=3248&func=browse">Current patches</a><br>
<li><a href="http://sourceforge.net/tracker/?atid=353248&group_id=3248&func=browse">Feature requests</a><br>
</ul>
<br>
end;
$doc->end();

?>
