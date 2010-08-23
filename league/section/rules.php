<?php // $Id: rules.php,v 1.2 2005/03/24 13:27:14 dennismp Exp $ vim:sts=2:et:sw=2

function section_rules(){
  $obj = mysql_fetch_object(mysql_query("select text from bzl_siteconfig where name='rules'"));
  echo '<BR>'. text_disp($obj->text, false) . '<BR>';
}
?>
