<?php
// message of the day server for bzflag

// get our configs
include('document.php');

if ($action == "rss")	// generate a feed
{
	header('Content-type: text/rss');
	echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
	$content .= "<rss version=\"2.0\" xmlns:content=\"http://purl.org/rss/1.0/modules/content/\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\">\n";
	$content .= " <channel>\n";
	$content .= "  <title>BZFlag Message of the Day</title>\n";
	$content .= "  <description>The BZFlag Message of the Day news feed.</description>\n";
	$content .= "  <link>http://bzflag.org/motd.php?action=rss</link>\n";
	$content .= "  <language>en</language>\n";
	$content .= "  <pubDate>". gmdate('D, d M Y H:i:s T') ."</pubDate>\n";
	$content .= "  <generator>our monkey butler</generator>\n";
	$content .= "  <ttl>60</ttl>\n";
	$content .= "  <docs>http://blogs.law.harvard.edu/tech/rss</docs>\n";
	$content .= "  <image>\n";
	$content .= "   <title>BZFlag</title>\n";
	$content .= "   <url>http://bzflag.org/images/bzflag_icon_128x128.jpg</url>\n";
	$content .= "   <width>128</width><height>128</height>\n";
	$content .= "  </image>\n";

	
	// get a list of each field from servers and just dump every field
	if (isset($id))
	{
		$query = "SELECT * FROM motd WHERE id='$id'";
	} else {
		$query = "SELECT * FROM motd";
	}
	$result = mysql_query($query) or die("Query failed : " . mysql_error());
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		$content .= "  <item>\n";
		$content .= "   <title>$row[title]</title>\n";
		$content .= "   <link>http://bzflag.org/motd.php?action=rss&id=$row[id]</link>\n";
		$content .= "   <description>$row[description]</description>\n";
		if (isset($row['author'])) {
		  $content .= "   <author>$row[author]</author>\n";
		}
		if (isset($row['pubDate'])) {
		  $content .= "   <pubDate>$row[pubDate]</pubDate>\n";
		}
		if (isset($row['version'])) {
		  $content .= "   <category>$row[version]</category>\n";
		}
		if (isset($row['id'])) {
		  $content .= "   <guid isPermaLink=\"true\">http://bzflag.org/motd.php?action=rss&gid=$row[id]</guid>\n";
		}
		$content .= "   <source>BZFlag MOTD</source>\n";
		$content .= "  </item>\n";
	}
	
	$content .= " </channel>\n";
	$content .= "</rss>\n";
	
	echo ($content);
}
else
{
	header('Content-type: text/plain');
	// get a list of each field from servers and just dump every field
	$query = "SELECT * FROM motd";
	$result = mysql_query($query) or die("Query failed : " . mysql_error());
	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
	{
		if (isset($row[title])) {
			echo "$row[title] \n";
			echo "Posted by $row[author] on $row[pubDate]\n";
			echo "$row[description]\n";
			echo "BZFlag version: $row[version]\n"; 
			echo "\n"; 
		}
	}
}
?>
