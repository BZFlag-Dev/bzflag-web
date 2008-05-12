<?
include "../document.php";

$doc = new Document;
$doc->begin("links");
print <<< end
Here are a collection of links related to BZFlag:<br><br>
<a href="http://www.sourceforge.net/projects/bzflag">Sourceforge project page</a><br>
<a href="http://www.chesco.com/~dbrosius/BZFlag/BZFlag.html">MrApathyCream's page</a><br>
<a href="http://www.bzflag.de/">Oma & Frank's BZFlag page</a><br>
<a href="http://pub33.ezboard.com/bbzflag">The unofficial forums</a><br>
<a href="http://sourceforge.net/mail/?group_id=3248">The mailing lists</a><br>
<a href="http://worldstrider.tripod.com/">Gerbil's BZFlag page</a><br>
<a href="http://bzflag.free.fr/">Valoche's BZFlag page</a><br>
<a href="http://www.lans-web.com/bzflag/">Grape of Wrath's page</a><br>

<br>
end;
$doc->end();

?>
