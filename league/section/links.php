<?php // $Id$ vim:sts=2:et:sw=2

// _POST: 'url', 'name', 'comment', 'dt'
//        'state': 1 to submit change/addition
// _GET:  id' id of entry to update (0 to add new)

function section_linkadmin (){
  section_links_links (true);
}

function section_links (){
  section_links_links (false);
}

function section_links_permissions() {
  return array(
    'edit_links' => 'Add/Edit links'
  );
}


function section_links_links ($admReq){
  if (isFuncAllowed('links::edit_links') && $admReq){
    $adm = true;
    foreach (array('id', 'url', 'title', 'comment', 'state', 'ord', 'del') as $v)
      $$v = $_POST[$v];   
    if (!isset($id))
      $id = $_GET['id'];

    echo '<BR><CENTER>';
    if (isset($id)){
      if ($state == 1){
        echo '<DIV class=feedback>';
        section_links_submitLink ($id, $title, $url, $comment, $ord, $del);
        echo '</div><BR>';
      } else {
        section_links_presentForm($id);
        return;
      }
    }
  }
  section_links_displayLinks($adm);
}



function section_links_displayLinks ($adm){
  if($adm)
    echo '<center>'. htmlURLbutton('ADD New Link', 'linkadmin', 'id=0', ADMBUT) .'</center><BR>';
  $res = mysql_query('select * from '. TBL_LINKS .' order by ord, name');
  echo "<table align=center width=95% border=0 cellspacing=0 cellpadding=4>";
  $row=0;
  while($obj = mysql_fetch_object($res)) {
    $c = $row++%2?'rowodd':'roweven';
    echo "<tr class=$c><td width=25%><a href=\"$obj->url\" target=\"_blank\">$obj->name</a></td>
      <td>".text_disp($obj->comment).'</td>';
      if ($adm)
        echo "<TD>[$obj->ord]</td><TD>". htmlURLbutSmall ('Edit', 'linkadmin', "id=$obj->id", ADMBUT) .'</td>';     
      echo '</tr>';
  }
  echo "</table>";
}




function section_links_submitLink ($id, $name, $url, $comment, $ord, $del){
  $dt = gmdate('Y-m-d');
  if (isset($del)){
    sqlQuery ('delete from '. TBL_LINKS ."  WHERE id=$id");
    echo 'Link DELETED !';
  } else if ($id==0){     // new entry ...
    echo 'NEW Link added !';
    sqlQuery ('insert into '. TBL_LINKS ." (name, url, comment, ord, dt)
      VALUES('$name', '$url', '$comment', '$ord', '$dt')" );
  }else{
    echo 'Link changed !';
    sqlQuery ('update '. TBL_LINKS ." set name='$name', url='$url', comment='$comment', 
        ord='$ord', dt='$dt' WHERE id=$id");
  }
}



function section_links_presentForm ($id){
  echo '<BR><div class=feedback>';
  if ($id > 0){
    echo "EDITING LINK (id #$id)";
  $row = mysql_fetch_object(sqlQuery('select * from '. TBL_LINKS ." where id=$id"));
  }else{  
    echo "ADDING NEW LINK";
    $row->url = 'http://';
  }

  $link=$_GET['link'];  
  echo '</div><BR>';
  echo "<form method=post><table align=center border=0 cellspacing=0 cellpadding=1>
    <input type=hidden name=link value=$link>
    <input type=hidden name=state value=1>
    <tr><td align=right>Title:</td><TD><input type=text size=50 maxlength=80 name=title value='$row->name'></td></tr>
    <tr><td align=right>URL:</td><TD><input type=text size=50 maxlength=120 name=url value='$row->url'></td></tr>
    <tr><td align=right>Comment:</td><TD><textarea cols=70 rows=10 name=comment>$row->comment</textarea></td></tr>
    <tr><td align=right>Sort #:</td><TD><input type=text size=5 maxlength=4 name=ord value='$row->ord'></td></tr>
    <tr><td align=center colspan=2>"; 

    htmlMiniTable (array (htmlFormButton ('Submit', '', ADMBUT), 
            $id==0?'':htmlFormButton ('DELETE Link', 'del', ADMBUT),
            htmlURLbutton('Cancel', 'linkadmin',null,CLRBUT)), 8);
    echo '</td></tr></form></table>';
}



?>
