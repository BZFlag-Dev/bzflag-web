<? 

include ('handycode.inc');

header ('Content-type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';

?>


<rss version="2.0"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:sbo="http://db.bzflag.org/rss/1.0/modules/sbo/">

	<channel>
<?

//if ($feed == 'players') {
  if (!isset($server)) {
    $title = "Current Players";
    $link = "http://db.bzflag.org/currentplayers.php";
    $where = 1;
  } else {
    $title = "Player info for $server";
    $link = "http://db.bzflag.org/".serverlink($server, 0);
    $where = "`server` = '$server'";
  }
//}

?>
		<title>db.BZFlag.org - <?= $title ?></title>
		<link><?= $link ?></link>
		<description>db.BZFlag.org - <?= $title ?></description>
		<language>en</language>
		<copyright>Copyright 2004 Tucker McLean</copyright>
		<lastBuildDate><?= $refreshed_rfc2822 ?></lastBuildDate>
		<generator>db.BZFlag.org</generator>
                <webMaster>http://www.noodleroni.com/contact.php</webMaster>
		<ttl>120</ttl>

		<dc:language>en</dc:language>
		<dc:creator>db.BZFlag.org</dc:creator>
		<dc:rights>Copyright 2004 Tucker McLean</dc:rights>
		<dc:date><?= $refreshed_rfc2822 ?></dc:date>

		<sy:updatePeriod>hourly</sy:updatePeriod>
		<sy:updateFrequency>30</sy:updateFrequency>
		<sy:updateBase>2003-09-01T12:00+00:00</sy:updateBase>
		<image>
			<url>http://db.bzflag.org/images/link_blue.png</url>
			<link>http://db.bzflag.org/</link>
			<title>db.BZFlag.org</title>
			<height>31</height>
			<width>88</width>
		</image>
<?

if (isset($order)) {
	if ($order == 'callsign') { $orderby = "`callsign`";
	} elseif ($order == 'callsigndesc') { $orderby = "`callsign` DESC";
	} elseif ($order == 'server') { $orderby = "`server`";
	} elseif ($order == 'serverdesc') { $orderby = "`server` DESC";
	} elseif ($order == 'score') { $orderby = "`score`";
	} elseif ($order == 'scoredesc') { $orderby = "`score` DESC";
	} elseif ($order == 'strengthindex') { $orderby = "`strengthindex`";
	} elseif ($order == 'strengthindexdesc') { $orderby = "`strengthindex` DESC";
	} else { $order == 'callsign'; $orderby = "`callsign`";
	}
} else {
	$order = 'callsign';
	$orderby = '`callsign`';
}
		

if ((num_rows("SELECT * FROM `currentplayers` WHERE $where ORDER BY $orderby")) > 0) {


	$currentplayers = mysql_query ("SELECT * FROM `currentplayers` WHERE $where ORDER BY $orderby");


	while ($row = mysql_fetch_array($currentplayers, MYSQL_ASSOC)) {

?>
		<item>
			<title><?= htmlentities($row[callsign]) ?></title>
                        <link>http://db.bzflag.org/<?= callsignlink(htmlentities($row[callsign]), 0, 0, 0) ?></link>
			<description><?= htmlentities($row[callsign]) ?></description>
			<pubDate><?= $refreshed_rfc2822 ?></pubDate>
                        <sbo:callsign><?= htmlentities($row[callsign]) ?></sbo:callsign>
                        <sbo:email><?= htmlentities($row[email]) ?></sbo:email>
                        <sbo:server><?= $row[server] ?></sbo:server>
                        <sbo:score><?= $row[score] ?></sbo:score>
                        <sbo:strengthindex><?= $row[strengthindex] ?></sbo:strengthindex>
                        <sbo:team><?= $teamnames[$row[team]] ?></sbo:team>
		</item>
<?

		   }
} else {

 ?>
                <item>
                       <title>No info available</title>
       	               <link>http://db.bzflag.org/</link>
                       <description>There is no info in this feed. Try another one.</description>
                       <pubDate><?= $refreshed_rfc2822 ?></pubDate>
                </item>
<?
		   }
?>

	</channel>
</rss>
