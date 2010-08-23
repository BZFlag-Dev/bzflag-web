<?php // $Id: template.php,v 1.7 2006/08/29 00:29:49 dennismp Exp $ vim:sts=2:et:sw=2
?>

<html><head>
  <title><?= $PageTitle ?></title>
  <META name="description" content="Game league website powered by WEB-LEAGUE">
  <META name="keywords" content="WEB-LEAGUE, game, league, website, matches, teams, players, forums">
  <script TYPE='text/javascript' SRC="<?= THEME_DIR ?>/script.js" LANGUAGE="JavaScript1.1" ></SCRIPT>
  <link rel="SHORTCUT ICON" href="templates/favicon.ico">
</head>

<link rel="stylesheet" type="text/css" href="<?= THEME_DIR ?>/style.css">

<body style="margin: 0px">

<? adminMenu(); ?>

<table style="margin-bottom:0px" border=0 background="<?= THEME_DIR ?>ctflogo.jpg" width=100% border=0 cellspacing=0 cellpadding=0>

<TR><TD colspan=3>
<TABLE width=100% cellpadding=0 cellspacing=0>
<TR valign=top height=49>
  <TD align=left width=33%><img src="<?= THEME_DIR ?>floatleft.gif"</td>
  <TD align=center width=33%><img src="<?= THEME_DIR ?>floatcenter.gif"</td>
  <TD align=right><img border=0 src="<?= THEME_DIR ?>floatright.gif"</td></tr>
  </td></table>
<TR valign=bottom class="statBar" height=14>
  <TD align=left width=33%><nobr>&nbsp;&nbsp;UTC: <?= gmdate('M d Y H:i') ?></nobr></td>
  <TD align=center> 
    <?
    if ($Authenticated){
      echo "<nobr>$UserName ($UserLevel)</nobr>&nbsp;&nbsp;&nbsp;<nobr>"
          . htmlLink ('[Profile]', 'playerinfo', "id=$UserID", LINK_BOLD) . '&nbsp;&nbsp;'
          . htmlLink ('[LOGOUT]',  'home', 'logout_x=1', LINK_BOLD) .'</nobr>';
    }else
      echo "<nobr>Welcome, guest</nobr>";
    ?>
  </td>
  <TD align=right width=33%><nobr><?= htmlLink ("$UsersOnline user(s) online", 'online', '', LINK_BOLD) 
?>&nbsp;&nbsp;</nobr></td>
</tr></table>

<TABLE width=100% border=0 cellpadding=0 cellspacing=10>
<TR><TD width=90 valign=top>

  <TABLE width=100% cellpadding=0 cellspacing=0>
  <TR><TD align=center class=menuHead>MENU</td></tr>
  <TR><TD class=menuBody>

    <TABLE class=navbar width=100% cellpadding=0 cellspacing=0><TR align=left><TD class="navbar">

<?
      if (!$Authenticated){
        echo htmlLink ('Login', 'login', null, LINK_NEW);
if (PRIVATE_LEAGUE == 0)
        echo htmlLink ('Register', 'register', null, LINK_NEW);
      }
      echo htmlLink ('Home', 'home');
      if ($Authenticated){
        if( $_SESSION['new_mail'] ) 
          echo htmlLink ('Bz mail', 'messages', null, LINK_ALERT);
        elseif ($_SESSION['mail_unread'] > 0)
          echo htmlLink ('Bz mail', 'messages', null, LINK_NEW);
        else
          echo htmlLink ('Bz mail', 'messages');
      } 
      if ($_SESSION['new_news'])
        echo htmlLink ('News', 'news', null, LINK_NEW);
      else
        echo htmlLink ('News', 'news');
      if (ENABLE_FORUMS){
        if ($_SESSION['new_forum'] > 0)
          echo htmlLink ('Forums', 'bzforums', null, LINK_NEW);
        else
          echo htmlLink ('Forums', 'bzforums');
      }
      if ($_SESSION['new_match'])
        echo htmlLink ('Matches', 'fights', null, LINK_NEW);
      else
        echo htmlLink ('Matches', 'fights');
      /* [seasonal league] */
      if(ENABLE_SEASONS) {
        echo htmlLink ('Ladder', 'season',null,$_SESSION['new_league_match']?LINK_NEW:null);
        echo htmlLink ('Seasons', 'seasons',null,$_SESSION['end_season']?LINK_NEW:null);
        #echo htmlLink ('Standings', 'standings',null,$_SESSION['end_season']?LINK_NEW:null);
      }
      echo htmlLink ('Teams', 'teams');
      echo htmlLink ('Players', 'players');
      echo htmlLink ('Rules', 'rules');
      echo htmlLink ('FAQ', 'faq');
      echo htmlLink ('Links', 'links');
      echo htmlLink ('Contact', 'contact');
      echo htmlLink ('Bans', 'shame');

?>

<?
  echo '<HR></td></tr><TR><TD align=center><font size=-2>Color Theme:
    <form method=post action="" name=setth><select name="settheme" onChange="setth.submit();">';
  $tkeys = array_keys ($_SESSION['themes']);
  foreach ($tkeys as $tk)
    htmlOption ($tk, $_SESSION['themes'][$tk][0], THEME_NAME);
  echo '</select></form>';
?>

      </td></tr></table>

    </td></tr></table>

</td><TD valign=top>

  <TABLE width=100% cellpadding=0 cellspacing=0>

  <TR><TD width=100% align=center >
    <TABLE class=mainhead width=100% cellpadding=0 cellspacing=0 border=0>
    <TR>
    <TD align=left width=50><IMG SRC="<?= THEME_DIR ?>tanktinyl.gif"></td>
    <TD align=center width=100%><?= $SectionTitle ?></td>
    <TD align=right width=50><IMG SRC="<?= THEME_DIR ?>tanktinyr.gif"></td>
    </td></tr></table>

  </td></tr>
  <TR><TD class=mainBody valign=top> 
    <?= $PageContent ?><BR>
  </td></tr></table>



</td></tr></table>
<A HREF="http://CalderaCandles.com"> &nbsp; </a>
</body>
</html>
