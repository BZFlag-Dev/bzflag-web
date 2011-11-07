<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
  xmlns:sbo="{$page.baseURL}rss/1.0/modules/sbo/">

  <channel>
    <title>{$page.servername} - {$page.title}</title>
    <link>{if $input.server}{$page.baseURL}serverinfo.php?server={$input.server|escape:'url'}{else}{$page.baseURL}currentplayers.php{/if}</link>
    <description>{$page.servername} - {$page.title}</description>
    <language>en-us</language>
    <copyright>Copyright {$smarty.now|date_format:"%Y"} Tim Riker</copyright>
    <lastBuildDate>{$page.refreshed_rfc2822}</lastBuildDate>
    <generator>{$page.servername}</generator>
    <ttl>5</ttl>

    <dc:language>en-us</dc:language>
    <dc:creator>{$page.servername}</dc:creator>
    <dc:rights>Copyright {$smarty.now|date_format:"%Y"} Tim Riker</dc:rights>
    <dc:date>{$page.refreshed_w3c}</dc:date>

    <sy:updatePeriod>hourly</sy:updatePeriod>
    <sy:updateFrequency>30</sy:updateFrequency>
    <sy:updateBase>2003-09-01T12:00+00:00</sy:updateBase>
    <image>
      <url>{$page.baseURL}images/link_blue.png</url>
      <link>{if $input.server}{$page.baseURL}serverinfo.php?server={$input.server|escape:'url'}{else}{$page.baseURL}currentplayers.php{/if}</link>
      <title>{$page.servername} - {$page.title}</title>
      <height>31</height>
      <width>88</width>
    </image>
{foreach from=$data.players item=player name=players}
    <item>
      <title>{$player.callsign|escape}</title>
      <link>{$page.baseURL}playerinfo.php?callsign={$player.callsign|escape:'url'}</link>
      <description><![CDATA[{$player.callsign|escape}{if $player.email}<br>Email: {$player.email|escape}{/if}<br>Team: {$data.teamnames[$player.team]}{if $player.team != 'O'}<br>Score: {$player.score}<br>Strength Index: {$player.strengthindex|string_format:"%.3f"}{/if}{if !$input.server}<br>Current Server: {$player.server}{/if}]]></description>
      <pubDate>{$page.refreshed_rfc2822}</pubDate>
      <sbo:callsign>{$player.callsign|escape}</sbo:callsign>
      <sbo:email>{$player.email|escape}</sbo:email>
      <sbo:server>{$player.server}</sbo:server>
      <sbo:score>{$player.score}</sbo:score>
      <sbo:strengthindex>{$player.strengthindex|string_format:"%.3f"}</sbo:strengthindex>
      <sbo:team>{$data.teamnames[$player.team]}</sbo:team>
    </item>
{/foreach}
  </channel>
</rss>
