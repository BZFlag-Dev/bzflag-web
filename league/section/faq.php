<?php // $Id$ vim:sts=2:et:sw=2

function section_faq(){
  $obj = mysql_fetch_object(mysql_query("select text from bzl_siteconfig where name='faq'"));

  $faq = str_replace("<HEAD>", "<p></font><font class=hfaq><center><b>", $obj->text);
  $faq = str_replace("<Q>", "</b></font></center><hr><font class=qfaq><b>Q:</b></font>&nbsp;<font class=qfaqtxt>", $faq);
  $faq = str_replace("<A>", "</font><font class=afaq>A:</font>&nbsp;", $faq);
  echo '<BR>'. text_disp ($faq) . '<BR>';
}
?>
