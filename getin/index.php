<?
include "../document.php";

$doc = new Document;
$doc->begin("get involved");
print <<< end
For now, the best way to get hacking at bzflag is to just jump in. Post
patches to the SourceForge page, and if your work is good you'll get CVS
access. These pages might be of interest:<br><br>
<a href="http://sourceforge.net/tracker/?atid=103248&group_id=3248&func=browse">Current bugs</a><br>
<a href="http://sourceforge.net/tracker/?atid=303248&group_id=3248&func=browse">Current patches</a><br>
<a href="http://sourceforge.net/tracker/?atid=353248&group_id=3248&func=browse">Feature requests</a><br>
<a href="/developers/flags.php">TODO flags list</a><br>
<br>
end;
$doc->end();

?>
