<?php
// message of the day server for bzflag

// get our configs
include('document.php');

if ($action == "rss")	// generate a feed
{
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n\n";
	$content .= "<!DOCTYPE rss [<!ENTITY % HTMLlat1 PUBLIC \"-//W3V//ENTITIES Latin 1 for XHTML//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml-lat1.ent\">]>\n";
	$content .= "<rss version=\"0.92\" xml:base=\"http://jeffm.bakadigital.com/\">\n";
	$content .= "<channel>\n";
	$content .= "  <title>BZFlag Message of the Day</title>\n";
	$content .= "  <description>BZFlag MOTD Rss Feed</description>\n";
	$content .= "  <link>http://jeffm.bakadigital.com/motd/motd.php?action=rss</link>\n";
	$content .= "  <language>en</language>\n";
	
	// get a list of each field from servers and just dump every field
	$query = "SELECT * FROM motd";
	$result = mysql_query($query) or die("Query failed : " . mysql_error());
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$content .= "  <item>\n";
		$content .= "    <title>$row[message]</title>\n";
		$content .= "    <message>$row[message]</message>\n";
		$content .= "  </item>\n";
	}
	
	$content .= "  </channel>\n";
    $content .= "</rss>\n";
	
	echo ($content);
}
else
{
	// get a list of each field from servers and just dump every field
	$query = "SELECT * FROM motd";
	$result = mysql_query($query) or die("Query failed : " . mysql_error());
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		echo $row[message]; // message
		echo " "; 
	}
}
?>
