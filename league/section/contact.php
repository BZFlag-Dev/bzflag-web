<?php
function section_contact(&$templ)
{
	$obj = mysql_fetch_object(mysql_query("select contact from l_siteconfig"));
	echo nl2br($obj->contact);
}
?>