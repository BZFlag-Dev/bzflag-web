<?php
function section_faq(&$templ)
{
	$obj = mysql_fetch_object(mysql_query("select faq from l_siteconfig"));
	$faq = nl2br($obj->faq);
	/*
					FAQ format would be:
					Q:...........
					A:...........
					So we can display it in a fancy way.
	*/
	$faq = str_replace("<HEAD>", "<br><br></font><font class=hfaq><center><b>", $faq);
	$faq = str_replace("<Q>", "</b></font></center><hr><font class=qfaq><b>Q:</b></font>&nbsp;<font class=qfaqbg>", $faq);
	$faq = str_replace("<A>", "</font><font class=afaq>A:</font>&nbsp;", $faq);
	echo $faq;
}
?>