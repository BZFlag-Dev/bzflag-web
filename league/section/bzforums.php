<?php // $Id$ vim:sts=2:et:sw=2
function section_bzforums() {
  require_once('lib/common.php');

  $allowDelete = isFuncAllowed('post_delete');
  $allowNew    = isFuncAllowed('post_new');
  $allowReply  = isFuncAllowed('post_reply');
  $allowEdit   = isFuncAllowed('post_edit');
  $allowLock   = isFuncAllowed('topic_lock');
  $allowSticky = isFuncAllowed('topic_sticky');
  $allowTDelete= isFuncAllowed('topic_delete');
  $allowViewDeleted= isFuncAllowed('topic_view_deleted');
  $showRoles   = isFuncAllowed('show_roles');

  $POSTSPERPAGE = 10;
  $link     = 'bzforums';

  $utcNOW = gmdate("Y-m-d H:i:s");

  // Variables: 
  //   top = which post is first on the page i think
  $vars = array('top','threadid','forumid','id', 'action');
  foreach($vars as $var)
    $$var = $_REQUEST[$var]; 

  // Get and print the forum title
  if(isset($forumid)) {
    $row = mysql_fetch_object(
      mysql_query("select title, status from l_forum where id=$forumid"));
    echo 'Forum: <a href="index.php?link='.$link.'&'.SID.'">
      <font size=+1>BZforums</font></a><font size=+1><i>&nbsp;/&nbsp;</i></font>
      <a href="index.php?link='.$link.'&forumid='.$forumid.'&'.SID.'">
      <font size=+1>'.$row->title.'</font></a><hr>';
    if( $row->status != 'Open' ) {
      print "This forum is not accessible. Sorry";
      return;
    }
  }
  // check if we need to change permissions (eg, disallow reply/post for locked topics)
  if(isset($threadid) ) {
    $threadid = intval($threadid);
    $row = mysql_fetch_object(
      mysql_query("select status, is_sticky from l_forumthread where id=$threadid"));
    if( $row->status == 'locked' && !isAdmin()) {
      $allowNew   = false;
      $allowReply = false;
    }
    elseif( $row->status == 'deleted' && !$allowViewDeleted ) {
      errorPage('This topic is deleted');
      return;
    }
    $threadstatus = $row->status;
    $threadsticky = $row->is_sticky;
  }

  // Perform deletion
  if( $allowDelete && $action == 'delete2' && $id != '' ) {
    $id = intval($id);
    mysql_query("DELETE FROM l_forummsg WHERE msgid = '$id' LIMIT 1") or die(mysql_error());

    // Check if there is any message left for that thread
    $threadid = intval($threadid);
    $res = mysql_query("SELECT count(1) FROM l_forummsg WHERE threadid = '$threadid'") or die(mysql_error());
    $row = mysql_fetch_row($res);
    if( $row[0] == 0 ) {
      // No messages in thread: deleting thread - jumo to threadlist
      mysql_query("DELETE FROM l_forumthread WHERE id = '$threadid' LIMIT 1") or die(mysql_error());
      header("Location: index.php?link=bzforums&forumid=$forumid");
      exit;
    }
    $action = '';
  }
  elseif( $action == 'lock' ) {
    $threadid = intval($threadid);
    if( !$allowLock ) {
      errorPage("You are not allowed to lock topics");
      return;
    }

      mysql_query("UPDATE l_forumthread SET status = 'locked', status_by = {$_SESSION['playerid']}, 
                  status_at = '$utcNOW' WHERE id = '$threadid' and status != 'locked' LIMIT 1") or die(mysql_error());
      $action = '';
      $threadstatus = 'locked';
  }
  elseif( $action == 'revive' ) {
    $threadid = intval($threadid);
    if( !$allowLock && $threadstatus == 'locked' ) {
      errorPage("You are not allowed to unlock topics");
      return;
    }
    if( !$allowTDelete && $threadstatus == 'deleted' )  {
      errorPage("You are not allowed to undelete topics");
      return;
    }

    mysql_query("UPDATE l_forumthread SET status = 'normal', 
                 status_by = {$_SESSION['playerid']}, status_at = '$utcNOW' WHERE id = '$threadid' LIMIT 1") or die(mysql_error());
    $action = '';
    $threadstatus = 'normal';
  }
  elseif( $action == 'deletetopic' ) {
    $threadid = intval($threadid);
    if( !$allowTDelete )  {
      errorPage("You are not allowed to delete topics");
      return;
    }

    mysql_query("UPDATE l_forumthread SET status = 'deleted', status_by = {$_SESSION['playerid']}, 
                 status_at = '$utcNOW' WHERE id = '$threadid' and status != 'deleted' LIMIT 1") or die(mysql_error());
    $action = '';
    $threadstatus = 'deleted';
  }
  elseif( $action == 'sticky' ) {
    $threadid = intval($threadid);
    if( !$allowSticky )  {
      errorPage("You are not allowed to use sticky");
      return;
    }

    mysql_query("UPDATE l_forumthread SET is_sticky = 1 WHERE id = '$threadid' LIMIT 1") or die(mysql_error());
    $action = '';
    $threadsticky = 1;
  }
  elseif( $action == 'unsticky' ) {
    $threadid = intval($threadid);
    if( !$allowSticky )  {
      errorPage("You are not allowed to use sticky");
      return;
    }

    mysql_query("UPDATE l_forumthread SET is_sticky = 0 WHERE id = '$threadid' LIMIT 1") or die(mysql_error());
    $action = '';
    $threadsticky = 0;
  }
  elseif( $action == 'new' || $action == 'reply' || $action == 'edit' ) {
    if( $action == 'new' && !$allowNew  ) {
      errorPage("You are not allowed to post new topics");
      return;
    }
    elseif($action == 'reply' && !$allowReply )  {
      errorPage("You are not allowed to reply");
      return;
    }
    elseif($action == 'edit' && !$allowEdit )  {
      errorPage("You are not allowed to edit");
      return;
    }

    echo '<form method=post name="post" action="index.php">'.SID_FORM;
    echo '<input type=hidden name=threadid value='.$threadid.'>';
    echo '<input type=hidden name=link value="'.$link.'">';
    echo '<input type=hidden name=forumid value='.$forumid.'>';
    echo '<input type=hidden name=id value='.$id.'>';
    echo '<input type=hidden name=top value='.$top.'>' . snFormInit();
    echo '<table border=0 align=center cellspacing=0 cellpadding=0>';
    echo '<tr class=tablehead><td colspan=2 align=center>';
    if( $action == 'new' ) echo 'New Topic';
    elseif( $action == 'reply' ) echo 'New Reply';
    else echo "Edit message";
    echo '</td></tr>';
    echo '<tr><td>Subject: </td><td>';
    
    if( $action == 'new' ) {
      print '<input type=text name=subject size=50 maxlength=80>';
    }
    else {
      $row = mysql_fetch_object(mysql_query("select subject from l_forumthread where id=$threadid"));
      echo '<i>'.$row->subject.'</i>';
    }
      
    print '</td></tr>';
    echo '<tr><td valign="top">Message</td><td>';

    if( $action == 'edit' ) {
      $id = intval($id);
      $row = mysql_fetch_object(mysql_query("select msg from l_forummsg where msgid=$id"));
      print '<textarea cols=50 rows=10 name=forummsg>'.
        htmlspecialchars($row->msg). '</textarea>';
    }
    else {
      print '<textarea cols=50 rows=10 name=forummsg></textarea>';
    }
    print '</td></tr>';

    // Form buttons
    echo '<tr><td colspan=2 align=center>'
      .htmlFormButton ('OK', 'save_'.$action)
      .'&nbsp'
      .htmlFormButton ('Cancel', 'cancel', CLRBUT)
      .'</td></tr>';
    print '<tr><td>Smiley:</td><td align="middle">';

    $res = mysql_query("select image, code from l_smiley GROUP BY image") or die(mysql_error());
    while($row = mysql_fetch_object($res)) {
        print '<a href="#" onclick="javascript:document.post.forummsg.value += \' ' . $row->code . '\'"><img border=0 src="' . THEME_DIR . 'smilies/' .$row->image. '" border=0/></a> ';
    }
    print "</tr>";
    echo '</table></form>';
  }
  elseif( isset($_POST['save_new']) ) {
    $forummsg = section_bzforums_stripExtraLF($_POST['forummsg']);
    $subject  = $_POST['subject'];

    // New topic
    if( $allowNew ) {
        mysql_query("INSERT INTO l_forumthread (id,forumid,creatorid,subject) VALUES(0, $forumid, {$_SESSION['playerid']}, '".$subject."')") or die(mysql_error());
        $threadid = mysql_insert_id();
        mysql_query("insert into l_forummsg(msgid,threadid,fromid,msg,datesent)  
                    values(0, $threadid, {$_SESSION['playerid']}, '".$forummsg."', '$utcNOW')") or die(mysql_error());
        session_refresh_all();
      }
      else {
        errorPage("You are not allowed to post new topics");
        return;
      }
  }
  elseif( isset($_POST['save_reply']) ) {
    $forummsg = section_bzforums_stripExtraLF($_POST['forummsg']);
    if( $allowReply ) {
      snCheck ('bzforums', "forumid=$forumid&threadid=$threadid");
      mysql_query("insert into l_forummsg(msgid,threadid,fromid,msg,datesent) 
                   values(0, $threadid, {$_SESSION['playerid']}, '".$forummsg."', '$utcNOW')");
      session_refresh_all();
    }
    else {
      errorPage("You are not allowed to reply");
      return;
    }
  }
  elseif( isset($_POST['save_edit']) ) {
    $forummsg = section_bzforums_stripExtraLF($_POST['forummsg']);
    if( $allowReply ) {
      $id = intval($id);
      snCheck ('bzforums', "forumid=$forumid&threadid=$threadid");
//      mysql_query("UPDATE l_forummsg SET msg = '$forummsg' WHERE msgid = '$id'");
      mysql_query("UPDATE l_forummsg SET msg = '$forummsg', status = 'edited', 
                  status_by = {$_SESSION['playerid']}, status_at = '$utcNOW' WHERE msgid = '$id'");
      session_refresh_all();
    }
    else {
      errorPage("You are not allowed to edit");
      return;
    }
  }

  if(isset($threadid)) {
    // Display a thread
    // Control buttons
    // 06/28/2002: $POSTSPERPAGE posts/page max.

    if(!isset($top)) {
      $top = 0;
      $newtop = 0;
    }

    if($top == "") {
      $top = 0;
      $newtop = 0;
    }

    if($action=='') {
      echo '<TABLE align=right><TR>';
      if( $allowReply && $threadstatus != 'deleted')
        echo '<td>' . htmlURLbutton ('Add Reply', $link, "forumid=$forumid&threadid=$threadid&action=reply&top=$top").'</td>';
      if( $allowLock && $threadstatus != 'locked' ) 
        echo '<td>' . htmlURLbutton ('Lock topic', $link, "forumid=$forumid&threadid=$threadid&action=lock&top=$top", ADMBUT).'</td>';
      if( $allowTDelete && $threadstatus != 'deleted') 
        echo '<td>' . htmlURLbutton ('Delete topic', $link, "forumid=$forumid&threadid=$threadid&action=deletetopic&top=$top", ADMBUT).'</td>';
      if( ( $allowLock && $threadstatus == 'locked') || ( $allowTDelete && $threadstatus == 'deleted' ) )
        echo '<td>' . htmlURLbutton ('Revive topic', $link, "forumid=$forumid&threadid=$threadid&action=revive&top=$top", ADMBUT).'</td>';
      if(  $allowSticky )
        if( $threadsticky  )
          echo '<td>' . htmlURLbutton ('Remove sticky', $link, "forumid=$forumid&threadid=$threadid&action=unsticky&top=$top", ADMBUT).'</td>';
        else
          echo '<td>' . htmlURLbutton ('Make sticky', $link, "forumid=$forumid&threadid=$threadid&action=sticky&top=$top", ADMBUT).'</td>';
      echo '</tr></table><BR clear=all>';
    }
    $temp = mysql_fetch_object(mysql_query("select count(msgid) num from l_forummsg where threadid=$threadid"));
    $numpost = $temp->num;
    $numpages = floor(($numpost + $POSTSPERPAGE - 1) / $POSTSPERPAGE);
    $curpage = $top / $POSTSPERPAGE + 1;
    $res = sqlQuery("select msgid, msg, datesent, l_forummsg.status, status_at, l_player.id pid, 
      player2.callsign as editedby, l_player.callsign, r.name as role
      from (l_forummsg, l_player, bzl_roles r)
      LEFT JOIN l_player player2  ON  player2.id = status_by
      where threadid=$threadid
      and fromid = l_player.id
      and l_player.role_id = r.id
      order by datesent
      limit $top, $POSTSPERPAGE");

    // Table header
    $sub = mysql_fetch_object(mysql_query("select subject, status from l_forumthread where id=$threadid"));
    echo '<table align=center border=1 cellspacing=0 cellpadding=1>';
    if( $threadsticky )
      $status = '<b>Sticky</b> ';
    else
      $status = '';
    if( $sub->status == 'locked' ) 
      $status .= '<b>Locked</b> ';
    elseif( $sub->status == 'deleted' ) 
      $status .= '<b>Deleted</b> ';

    echo '<tr class=tabhead><td colspan=3><font size=+1>' . $status .'<i>'.smileys($sub->subject).'</i></font>';

    // Display pages if multiple pages and not adding a post
    if(($numpages > 1) && (!isset($addpost))) {
      echo '<div align=right>';
      for($i = 1; $i <= $numpages; $i++) {
          if($i == $curpage) {
              echo $i.'&nbsp;';
          } else {
              $newtop = ($i - 1) * $POSTSPERPAGE;
              echo '<a href="index.php?link='.$link.'&forumid='.$forumid.'&threadid='.$threadid.'&top='.$newtop.'&'.SID.'">'.$i.'</a>&nbsp;';
          }
      }
      echo '</div>';
    }
    echo '</td></tr>';

    echo '<tr class=tablehead><td>Author</td><td>Message</td></tr>';

    $cf = 1;
    while($row = mysql_fetch_object($res)) {
      echo '<tr class=forum'.$cf.'>';

      echo '<td valign=top rowspan="' . ($allowDelete?2:1) . '"><a href="index.php?link=playerinfo&id='.$row->pid.'&'.SID.'">'.$row->callsign.'</a>';
      if( $showRoles ) print " <small>({$row->role})</small>";
      
      echo '<br><font size=-2>('.$row->datesent.')</font></td>';
      $msgbody = smileys(wordwrap(nl2br(htmlspecialchars($row->msg)), 75, " ", true)); // DMP 19oct2007: Added wordwrap to break annoyingly long lines
      $highlight = false;
    
      // Setup available commands

      // Comamnds: If a ALL key exists, it will be the only one showed, otherwise all elements is shown
      $commands = array();  

      if( $allowDelete ) {
        if(  $action=='delete' && $id==$row->msgid ) {
          $highlight = true;
          $commands['ALL'] = 'Confirmation: ' .
            htmlLink('Delete','bzforums', 'action=delete2&id=' . $row->msgid . '&forumid='.$forumid .'&threadid='. $threadid.'&top='.$top) . ' OR ' .
            htmlLink('Cancel?','bzforums', 'id=' . $row->msgid . '&forumid='.$forumid .'&threadid='. $threadid.'&top='.$top);
        }
        else {
          $commands[] = htmlLink('[delete]','bzforums', 'action=delete&id=' . $row->msgid . '&forumid='.$forumid .'&threadid='. $threadid.'&top='.$top);
        }
      }

      if( $allowEdit ) {
        $commands[] = htmlLink('[edit]','bzforums', 'action=edit&id=' . $row->msgid . '&forumid='.$forumid .'&threadid='. $threadid.'&top='.$top);
      }
 
      if( $highlight )
        echo '<td valign=top><font color=red>'.$msgbody.'</font>';
      else 
        echo '<td valign=top>'.$msgbody;


      if ($row->status == 'edited')
        echo "<BR><small>(Message edited by $row->editedby at: $row->status_at)</small>";
      echo '</td></tr>';

      // Show commands if any
      if( count($commands) ) {
        // if action is set dont show i$commands unless it contains 'ALL'
        if( ($action != '' && isset($commands['ALL'])) || $action == '' ) {
          echo '<tr><td align="right" colspan="2" valign=top><small>'.
            (isset($commands['ALL']) ? $commands['ALL'] : join(' ', $commands)) .
            '</small></td></tr>';
        }
        else {
          echo '<tr></tr>';
        }
          
      }
      $cf = 3 - $cf;
    }
    echo '</table>';
    
    if ($curpage < $numpages)
      echo '<CENTER><BR>'. htmlURLbutSmall ("NEXT Page", $link, 
           "forumid=$forumid&threadid=$threadid&top=". $curpage*$POSTSPERPAGE);
    
  }
   else {
    if(isset($forumid)&&(!isset($addpost))) {
      // Display specific forum
      // Control buttons
      if($allowNew) {
        echo '<div align=right>'
          . htmlURLbutton ('New Topic', $link, "forumid=$forumid&action=new&top=$top") . '</div>';
      }
      if( $allowViewDeleted ) 
        $viewClause = '';
      else 
        $viewClause = "AND l_forumthread.status != 'deleted' ";

      $res = mysql_query("select l_forumthread.id,l_forumthread.subject, l_player.id pid, 
          l_player.callsign, max(l_forummsg.datesent) ds, 
          unix_timestamp(max(l_forummsg.datesent)) datesent_ts,
          l_forumthread.status, 
          l_forumthread.status_at,
          splayer.callsign as status_by, l_forumthread.is_sticky
        from (l_forumthread, l_forummsg, l_player, l_player l_player2)
        left join l_player splayer  ON (splayer.id = l_forumthread.status_by)
        where l_forumthread.forumid=$forumid
          and l_forumthread.creatorid = l_player.id
          $viewClause
          and l_forumthread.id = l_forummsg.threadid
          and l_player2.id = l_forummsg.fromid
        group by l_forumthread.id, l_forumthread.subject, l_player.id, l_player.callsign
        order by l_forumthread.is_sticky DESC, ds desc") or die(mysql_error());

      // Table header
      echo '<table align=center border=1 cellspacing=0 cellpadding=1>';
      echo '<tr class=tabhead><td width=50%>Topic</td><td align=center>Replies</td><td 
          align=center>Last Comment</td><td align=center>Started by</td></tr>';

      while($row = mysql_fetch_object($res)) {
        // Get the last author for this topic
        $la = mysql_fetch_object(mysql_query("select l_player.id pid, l_player.callsign
          from l_player, l_forummsg
          where threadid = $row->id
          and fromid = l_player.id
          order by datesent desc
          limit 0, 1"));

        $nr = mysql_fetch_object(mysql_query("select count(*)-1 num
          from l_forummsg
          where threadid = ".$row->id));
        echo '<tr><td>';
        if( $row->is_sticky == 1 ) {
          print '<b>STICKY</b> ';
        }
        if( $row->status == 'locked' ) {
          echo '<b>LOCKED</b>; ';
        }
        elseif( $row->status == 'deleted' ) {
          echo '<b>DELETED</b>; ';
        }
        echo '<a href="index.php?link='.$link.'&forumid='.$forumid.'&threadid='.$row->id.'&'.SID.'">';

        if( empty($row->subject) )
          $row->subject = '(no subject)';

        if( isset($_SESSION['last_login']) && ($_SESSION['last_login'] < $row->datesent_ts ))
          echo '<font color="red"><b>' . smileys($row->subject).'</b></font></a>';
        else
          echo smileys($row->subject).'</a>';

        $numpages = floor(($nr->num + 1 + $POSTSPERPAGE - 1) / $POSTSPERPAGE);

        if($numpages > 1) {
          echo '&nbsp;(<img src="'. THEME_DIR .'/multipage.gif">&nbsp;';
          for($i = 2; $i <= $numpages; $i++) {
            $t = ($i - 1) * 10;
            echo '<a href="index.php?link='.$link.'&top='.$t.'&forumid='.$forumid.'&threadid='.$row->id.'&'.SID.'">'.$i.'</a>&nbsp;';
          }
          echo ')';
        }

        if( $row->status != 'normal' ) {
          if (!$row->status_by)
            $row->status_by = 'SYSTEM';
          echo "<br /><small>by $row->status_by at {$row->status_at}</small>";
        }
        echo '</td>
        <td align=center>'.$nr->num;
        echo '</td>
        <td align=center>'.$row->ds.'<br>by <a href="index.php?link=playerinfo&id='.$la->pid.'&'.SID.'">'.$la->callsign.'</a></td>
        <td align=center><a href="index.php?link=playerinfo&id='.$row->pid.'&'.SID.'">'.$row->callsign.'</a></td>
        </tr>';
      }
      echo '</table>';


    } elseif($action=='') {
      // Display forums list
      $res = mysql_query("select l_forum.id, l_forum.title, ifnull(max(l_forummsg.datesent),'n/a') md, count(l_forummsg.msgid) num,
          unix_timestamp(max(l_forummsg.datesent)) as datesent_ts
          from l_forum 
          left join l_forumthread on (l_forum.id = l_forumthread.forumid AND l_forumthread.status != 'deleted')
          left join l_forummsg on l_forumthread.id = l_forummsg.threadid
          where l_forum.status = 'open'
          group by l_forum.id, l_forum.title
          order by title") or die(mysql_error());
      echo '<table align=center border=0 cellspacing=0 cellpadding=1>
      <tr class=tabhead><td>Forums</td><td align=right># Posts</td><td align=center>Last Comment</td></tr>';
      while($row = mysql_fetch_object($res)) {
        echo '<tr><td><a href="index.php?link='.$link.'&forumid='.$row->id.'&'.SID.'">';

        if( isset($_SESSION['last_login']) && ($_SESSION['last_login'] < $row->datesent_ts ))
          echo "<font color=\"red\">" . $row->title . "</font>";
        else
          echo $row->title;

        echo '</a></td><td align=center>'.$row->num.'</td><td align=center>'.$row->md.'</td></tr>';
      }
      echo '</table>';
    }


  }
}

function section_bzforums_permissions() {
  return array(
    'post_delete' => 'Allow user to delete posts in forum',
    'post_new'    => 'Can user create new threads?',
    'post_reply'  => 'Can user post replies in exisitng threads?',
    'post_edit'   => 'Can user edit other peoples post?',
    'topic_lock'  => 'Lock a topic (no more replies can be added',
    'topic_delete'  => 'Delete a topic',
    'topic_sticky'  => 'Can user stickify a topic',
    'topic_view_deleted' => 'Show deleted topics in forum lists',
    'show_roles'  => 'Show roles of the posters (only when viewing a topic)'
  );
}

// lazy fix for too many LFs
function section_bzforums_stripExtraLF ($str){
  while ( strcmp ($new = str_replace ("\r\n\r\n\r\n", "\r\n\r\n", $str), $str) != 0)
    $str = $new;
  return $str;
}
?>
