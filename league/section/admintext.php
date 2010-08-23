<?php // $Id: admintext.php,v 1.4 2005/04/03 08:31:53 menotume Exp $ vim:sts=2:et:sw=2

// func: faq, homepage, contact, motd or todo

function section_admintext(){
  $func = isset($_POST['func']) ? $_POST['func'] : $_GET['func'];
  $link = isset($_POST['link']) ? $_POST['link'] : $_GET['link'];
  $text = $_POST['text'];
  $ok = $_POST['ok'];

  if ($func=='homepage'){
    if (!isFuncAllowed('edit_homepage')) return errorPage ('Not Authorized');
    $title = 'Home Page text:';
  }else if ($func=='rules'){
    if (!isFuncAllowed('edit_rules')) return errorPage ('Not Authorized');
    $title = 'Match Rules and How-Tos:';
  }else if ($func=='motd'){
    if (!isFuncAllowed('edit_motd')) return errorPage ('Not Authorized');
    $title = 'Quote of the Day:';
  }else if ($func=='faq'){
    if (!isFuncAllowed('edit_faq')) return errorPage ('Not Authorized');
    $title = 'F.A.Q.:<p>
      <TABLE border=0><TR><TD>Format:</td><TD>&lt;HEAD&gt;Print a bold heading</td></tr>
      <TR><TD></td><TD>&lt;Q&gt;This is my question [carriage return]</td></tr>
      <TR><TD></td><TD>&lt;A&gt;This is the answer[carriage return]</td></tr>
      <TR><TD colspan=2><HR>Icon images may be used by prefixing the image filename<BR>
        with either &lt;IMGPATH&gt; or &lt;SMILEYPATH&gt; like so:</td></tr>
      <TR><TD colspan=2 align=center> &lt;img src="&lt;SMILEYPATH&gt;bigsmile.gif"&gt;</td></tr>
      </td></tr></table>';
  }else if ($func=='contact'){
    if (!isFuncAllowed('edit_contacts')) return errorPage ('Not Authorized');
    $title = 'Contact Page text:';
  }else if ($func=='todo'){
    if (!isFuncAllowed('edit_todo')) return errorPage ('Not Authorized');
    $title = 'Scratchpad text (simple wiki)';
  }else
    return errorPage ('Invalid function.');




  if ($ok && $text){
    sqlQuery("update bzl_siteconfig set text=\"$text\" where name='$func'");
    $text = stripslashes ($text);
    echo "<center><BR><DIV class=feedback>Site updated, thank you {$_SESSION['callsign']}</div><BR>";
  } else {
    $row = mysql_fetch_object(sqlQuery("select text from bzl_siteconfig where name='$func'"));
    if (!$row)
      sqlQuery ("INSERT INTO bzl_siteconfig (name, text) VALUES ('$func', '')");
    $text = $row->text;
  }

  echo "<BR><CENTER><form method=post>
    <input type=hidden name=func value='$func'>
    <input type=hidden name=link value='$link'>
    <div class=contTitle>$title</div><BR>
    <textarea name=text cols=80 rows=20>$text</textarea>
    <TABLE><TR><TD>" 
      .htmlFormButton ('Submit', 'ok')
      .'</td><TD width=9></td><TD>' 
      .htmlFormButton ('Reset', 'cancel', CLRBUT)
      .'</td></tr></table></form>';
}

function section_admintext_permissions() {
  return array(
    'edit_homepage' => 'Edit frontpage',
    'edit_contacts' => 'Edit contactpage',
    'edit_rules' => 'Edit rulespage',
    'edit_faq' => 'Edit FAQ',
    'edit_todo' => 'Edit TODO',
  );
}
?>
