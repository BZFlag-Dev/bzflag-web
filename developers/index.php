<?
include "../document.php";

$doc = new Document;
$doc->begin("developers");
print <<< end
<p>Most development happens on the
<a href="http://sourceforge.net/projects/bzflag">BZFlag SourceForge site</a>.
There you'll find BZFlag source code, helpful documentation, as well as
mailing lists and various trackers.</p>
<p>
<a href="../doxygen/">Source Code Documentation (doxygen)</a><br>
<a href="networking.php">Networking</a><br>
<a href="clientserver.php">Client/Server communication</a><br>
<a href="clientclient.php">Client/Client communication</a><br>
<a href="protocol.php">Protocol</a><br>
<a href="flags.php">TODO Flags list</a><br>
</p>
end;
$doc->end();

?>
