<?

include('header.inc');

if ($submitted == "yes") {
  if ($fullplayerlist == "no") {
    if ($showserver == "") {
      ?><p><strong>You must enter a server to display info from</strong></p><?
	   } else {
	     $rssfeed = "&server=$showserver";
	   }
  }
  if ($order == "callsign") {
    if ($callsigndesc == "yes") {
      $rssfeed .= "&order=callsigndesc";
    } else {
      $rssfeed .= "&order=callsign";
    }
  }
  if ($order == "server") {
    if ($serverdesc == "yes") {
      $rssfeed .= "&order=serverdesc";
    } else {
      $rssfeed .= "&order=server";
    }
  }
  if ($order == "score") {
    if ($scoreasc == "yes") {
      $rssfeed .= "&order=scoreasc";
    } else {
      $rssfeed .= "&order=score";
    }
  }
  if ($order == "strengthindexasc") {
    if ($strengthindexasc == "yes") {
      $rssfeed .= "&order=strengthindexasc";
    } else {
      $rssfeed .= "&order=strengthindex";
    }
  }
}

if (isset($rssfeed)) {
  ?>
<pre>
http://stats.bzflag.org/rss.php?feed=players<?=$rssfeed?>
</pre>
<?
   }

?>

<p>Fill out the form, and submit it. You will then be given a URL for an RSS feed with the settings you specify</p>

<form action="<?=$PHP_SELF?>" method="get">

<input type="hidden" name="submitted" value="yes" />

<table>

<caption>Server</caption>

<tr class="evenrow">
<td colspan="2"><input type="radio" name="fullplayerlist" value="yes" <?=($fullplayerlist=='yes' || !isset($fullplayerlist))?'checked="checked" ':''?>/> Show full player list</td>
</tr>
<tr>
<td><input type="radio" name="fullplayerlist" value="no" <?=($fullplayerlist=='no')?'checked="checked" ':''?>/> Show only players on this server: <input name="showserver" <?=(isset($showserver))?'value="'.$showserver.'" ':''?>/></td>
</tr>

</table>

<table>

<caption>Display Order</caption>

<tr class="evenrow">
<td><input type="radio" name="order" value="callsign" <?=($order=='callsign' || !isset($callsign))?'checked="checked" ':''?>/> Callsign</td>
<td><input type="checkbox" name="callsigndesc" value="yes" <?=($callsigndesc=='yes')?'checked="checked" ':''?>/> Descending</td>
</tr>

<tr class="oddrow">
<td><input type="radio" name="order" value="server" <?=($order=='server')?'checked="checked" ':''?>/> Server</td>
<td><input type="checkbox" name="serverdesc" value="yes" <?=($serverdesc=='yes')?'checked="checked" ':''?>/> Descending</td>
</tr>

<tr class="evenrow">
<td><input type="radio" name="order" value="score" <?=($order=='score')?'checked="checked" ':''?>/> Score</td>
<td><input type="checkbox" name="scoreasc" value="yes" <?=($scoreasc=='yes')?'checked="checked" ':''?>/> Ascending</td>
</tr>

<tr class="oddrow">
<td><input type="radio" name="order" value="strengthindex" <?=($order=='strengthindex')?'checked="checked" ':''?>/> Strength Index</td>
<td><input type="checkbox" name="strengthindexasc" value="yes" <?=($strengthindexasc=='yes')?'checked="checked" ':''?>/> Ascending</td>
</tr>

<tr class="evenrow">
<td colspan="2"><input type="submit" /></td>
</tr>

</table>

</form>

<? include('footer.inc'); ?>
