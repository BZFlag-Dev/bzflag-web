<?php // $Id$


function section_newsadmin (){
  section_news_common (true);
}

function section_news (){
  section_news_common (false);
}

function section_news_permissions()  {
  return array(
    'edit_news' => 'Add/Edit news entries'
  );
}

// _POST: 'text', 'date'
//        'state': 1 to submit change/addition
// _GET:  'id' id of entry to update (0 to add new)

function section_news_common ($admReq){
  $numarts = $_GET['numarts'];
  if ($numarts<5 || $numarts>1000)
    $numarts = 15;

  if (isFuncAllowed('news::edit_news') && $admReq){
    $adm = true;
    foreach (array('id', 'text', 'date', 'state', 'del') as $v)
      $$v = $_POST[$v];   
    if (!isset($id))
      $id = $_GET['id'];
    echo '<CENTER>';
    if (isset($id)){
      if ($state == 1){
        echo '<DIV class=feedback><BR>';
        section_news_submit ($id, $del, $text, $date);
        echo '</div><BR>';
      } else {
        section_news_presentForm($id);
        return;
      }
    }
  }
  //unset ($_SESSION['new_news']);
  section_news_displayNews($adm, $_GET['link'], $numarts);
}


function section_news_displayNews ($adm, $lnk, $num){
  section_news_doSelectForm($lnk, $num);
  if($adm)
    echo '<center><BR>' . htmlURLbutton ('ADD News', 'newsadmin', 'id=0', ADMBUT);
  $res = sqlQuery('select *, unix_timestamp(newsdate) as utime from '. TBL_NEWS ." order by newsdate desc limit $num");
  echo "<table align=center border=0 cellspacing=0 cellpadding=1>";
  while($obj = mysql_fetch_object($res)){
    if ($obj->utime > $_SESSION['last_login'])
      echo '<TR class=new>';
    else
      echo '<TR>';
    echo '<td><i>'.$obj->newsdate.'</i></td><td align=right>';

    if ($adm)
      htmlMiniTable (array ("<i>By:</i>$obj->authorname", 
        htmlURLbutSmall('Edit', 'newsadmin', "id=$obj->id", ADMBUT)));
    else 
      echo "<i>By:</i> $obj->authorname";
    echo '</td></tr><TR><TD colspan=2>' .  text_disp($obj->text).
    '</td></tr>
      <tr><td colspan=2 align=center><hr></td></tr>';
  }
  echo "</table>";
}



function section_news_submit ($id, $del, $text, $date){
  if (isset($del)){
    sqlQuery ('delete from '. TBL_NEWS ."  WHERE id=$id");
    echo 'News DELETED !';
  } else if ($id==0){     // new entry ...
    echo 'News ADDED !';
    sqlQuery ('insert into '. TBL_NEWS ." (newsdate, authorname, text)
      VALUES('$date', '{$GLOBALS['UserName']}', '$text')" );
    session_refresh_all();
  }else{
    echo 'News CHANGED !';
    sqlQuery ('update '. TBL_NEWS ." set newsdate='$date', text='$text' WHERE id=$id");
  }
}



function section_news_presentForm ($id){
  echo '<BR><div class=feedback>';
  if ($id > 0){
    $row = mysql_fetch_object(sqlQuery('select * from '. TBL_NEWS ." where id=$id"));
    echo "EDITING NEWS (id #$id, by:$row->authorname)";
  }else{  
    echo "ADDING NEWS";
    $row->newsdate = gmdate('Y-m-d H:i:s');
  }
  $link=$_GET['link'];  
  echo '</div><BR>';
  echo "<form method=post><table align=center border=0 cellspacing=0 cellpadding=1>
      <input type=hidden name=link value=$link>
      <input type=hidden name=state value=1>
      <tr><td align=right>Date:</td><TD width=8></td>
          <TD><input type=text size=20 maxlength=20 name=date value='$row->newsdate'></td></tr>
      <tr><td align=right valign=top>Text:</td><TD width=8></td>
          <TD><textarea cols=70 rows=10 name=text>$row->text</textarea></td></tr>
      <tr><td align=center colspan=3><BR>";
      htmlMiniTable (array (htmlFormButton ('Submit', '', ADMBUT), 
            $id==0?'':htmlFormButton ('DELETE News', 'del', ADMBUT),
            htmlURLbutton('Cancel', 'newsadmin',null,CLRBUT)), 8);
      echo '</td></tr></form></table>';
}



function section_news_doSelectForm ($link, $num){
  echo "<TABLE align=center class=insetForm><TR><TD>
  <form action=\"index.php\" name=selarts>
  <input type=hidden name=link value=$link>
  </td><TD>
  <select name=numarts onChange=\"selarts.submit();\">";
    htmlOption (15, '15 Articles', $num);
    htmlOption (50, '50 Articles', $num);
    htmlOption (1000,  'ALL Articles', $num);
  echo'</td></tr></table></form>';
}

?>
