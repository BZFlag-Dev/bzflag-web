<?php 
include('serversettings.php');
$link = mysql_pconnect($dbhost, $dbuname, $dbpass)or die("Could not connect: " . mysql_error($link));
$rss['title'] = "Bzflag List Server";  
$rss['description'] = "Bzflag's Award Winning List Server";
$rss['link'] = "http://bzflag.org";
    echo ('<?xml version="1.0"?>');
    echo ('<rss version="2.0">');
    echo "<channel>\n";
    echo "<title>{$rss['title']}</title>\n";
    echo "<description>{$rss['description']}</description>\n";
    echo "<link>{$rss['link']}</link>\n";
    
   $result = mysql_query("SELECT nameport,title FROM servers ORDER BY `nameport` ASC", $link);
    
   
        if(mysql_num_rows($result) > 0) {
            while($row = mysql_fetch_array($result)) {
                 echo "<item>\n";

                echo "<title>{$row['title']} - {$row['nameport']}</title>\n";
                $time = strtotime("{$row['date']} {$row['time']}");
                echo "<pubDate>".date("r",$time)."</pubDate>\n";
                
                echo "</item>\n";
            }
        } else {
            echo "<item>\n";
            echo "<link>{$rss['link']}</link>\n";
            echo "<title>No Servers</title>\n";
            echo "<description>no servers</description>\n";
            echo "</item>\n";
        }
    
    echo "</channel>\n";
    echo "</rss>\n";
        /*
        
<?xml version="1.0"?>
<rss version="2.0">
<channel>
<title>title_of_site</title>
<description>description_of_site</description>
<link>http://link.to.site</link>

<item>
<title>title_of_article</title>
<description>short_desc_of_article  ...</description>
<pubDate>pubdate_in_ RFC 822_format</pubDate>
<link>link_to_article</link>
</item>

<item>
...
</item>

</channel>
</rss>
        */
?> 