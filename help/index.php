<?
include "../document.php";

$doc = new Document;
$doc->begin("help");
print <<< end
Help is organized into several sections. Please choose one below:<br><br>
<a href="/help/installation.php">Installation</a><br>
<a href="/help/playing.php">Playing</a><br>
<a href="/help/customization.php">Customization</a><br>
<a href="/help/troubleshooting.php">Troubleshooting</a><br>
<a href="/help/faq.php">Frequently asked questions</a><br>
end;
$doc->end();

?>
