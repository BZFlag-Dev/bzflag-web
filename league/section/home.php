<?php // $Id$ vim:sts=2:et:sw=2

function section_home (){

  $obj = mysql_fetch_object(sqlQuery("select text from bzl_siteconfig where name='homepage'"));
  echo "<table align=center border=0><tr><td>".text_disp($obj->text)."</td></tr></table>";


  // Display some statistics!!!
  $numplayer = mysql_fetch_object(sqlQuery("select count(*) num from l_player where status !='deleted'"));
  $numnone = mysql_fetch_object(sqlQuery("select count(*) num from l_player where team=0 and status!='deleted'"));
  $numteam = mysql_fetch_object(sqlQuery("select count(*) num from l_team where status!='deleted'"));
  $numfight = mysql_fetch_object(sqlQuery("select count(*) num from ". TBL_MATCH));
  $countries = mysql_fetch_object (sqlQuery("select count(distinct(country)) num from l_player WHERE country>0 AND status!='deleted'"));
  echo '<hr><table border=0 align=center cellspacing=0 cellpadding=1>'.
       '<tr><td colspan=2 class=tabhead align=center>Some statistics: </td></tr>'.
       '<tr><td>Number of players</td><td align=right>'.$numplayer->num.'</td></tr>'.
       '<tr><td>Players without a team</td><td align=right>'.$numnone->num.'</td></tr>'.
       '<tr><td>Number of teams</td><td align=right>'.$numteam->num.'</td></tr>'.
       '<tr><td>Number of matches played &nbsp;</td><td align=right>'.$numfight->num.'</td></tr>'.
       '<tr><td>Countries Represented &nbsp;</td><td align=right>'.$countries->num.'</td></tr>'.
       '</table>
        <BR><BR><TABLE cellpadding=0 cellspacing=0 align=center style="border-top: solid 1px #888">
        <TR><TD><font size=1>powered by <a href="http://sf.net/projects/web-league">web-league</a> v'. CODE_VERSION .'</td></tr></table>';
}
?>
