<?php // $Id: shame.php,v 1.1 2006/07/01 01:12:06 dennismp Exp $ vim:sts=2:et:sw=2

define (TBL_SHAME, "bzl_shame");

function section_shameadmin (){
  section_shame_shame (true);
}

function section_shame (){
  section_shame_shame (false);
}

function section_shame_permissions()  {
  return array(
    'edit_shame' => 'Add/Edit Hall Of Shame entries'
  );
}

// _POST: 'text', 'date'
//        'state': 1 to submit change/addition
// _GET:  'id' id of entry to update (0 to add new)

function section_shame_shame ($admReq){
  $numarts = $_GET['numarts'];
  if ($numarts<5 || $numarts>1000)
    $numarts = 15;

  if (isFuncAllowed('shame::edit_shame') && $admReq){
    $adm = true;
    foreach (array('id', 'text', 'date', 'state', 'del') as $v)
      $$v = $_POST[$v];   
    if (!isset($id))
      $id = $_GET['id'];
    echo '<CENTER>';
    if (isset($id)){
      if ($state == 1){
        echo '<DIV class=feedback><BR>';
        section_shame_submitNews ($id, $del, $text, $date);
        echo '</div><BR>';
      } else {
        section_shame_presentForm($id);
        return;
      }
    }
  }
  section_shame_displayNews($adm, $_GET['link'], $numarts);
}


function section_shame_displayNews ($adm, $lnk, $num){
  section_shame_doSelectForm($lnk, $num);
  if($adm)
    echo '<center><BR>' . htmlURLbutton ('ADD News', 'shameadmin', 'id=0', ADMBUT);
  $res = sqlQuery('select *, unix_timestamp(newsdate) as utime from '. TBL_SHAME ." order by newsdate desc limit $num");
  echo "<table align=center border=0 cellspacing=0 cellpadding=1>";
  while($obj = mysql_fetch_object($res)){
    if ($obj->utime > $_SESSION['last_login'])
      echo '<TR class=new>';
    else
      echo '<TR>';
    echo '<td><i>'.$obj->newsdate.'</i></td><td align=right>';

    if ($adm)
      htmlMiniTable (array ("<i>By:</i>$obj->authorname", 
        htmlURLbutSmall('Edit', 'shameadmin', "id=$obj->id", ADMBUT)));
    else 
      echo "<i>By:</i> $obj->authorname";
    echo '</td></tr><TR><TD colspan=2>' .  text_disp($obj->text).
    '</td></tr>
      <tr><td colspan=2 align=center><hr></td></tr>';
  }
  echo "</table>";
}



function section_shame_submitNews ($id, $del, $text, $date){
  if (isset($del)){
    sqlQuery ('delete from '. TBL_SHAME ."  WHERE id=$id");
    echo 'Ban DELETED !';
  } else if ($id==0){     // new entry ...
    echo 'Ban ADDED !';
    sqlQuery ('insert into '. TBL_SHAME ." (newsdate, authorname, text)
      VALUES('$date', '{$GLOBALS['UserName']}', '$text')" );
    session_refresh_all();
  }else{
    echo 'Ban CHANGED !';
    sqlQuery ('update '. TBL_SHAME ." set newsdate='$date', text='$text' WHERE id=$id");
  }
}



function section_shame_presentForm ($id){
  echo '<BR><div class=feedback>';
  if ($id > 0){
    $row = mysql_fetch_object(sqlQuery('select * from '. TBL_SHAME ." where id=$id"));
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
            htmlURLbutton('Cancel', 'shameadmin',null,CLRBUT)), 8);
      echo '</td></tr></form></table>';
}



function section_shame_doSelectForm ($link, $num){
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
