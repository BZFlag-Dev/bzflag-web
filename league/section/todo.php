<?php
function section_todo(&$templ)
{
	$obj = mysql_fetch_object(mysql_query("select todo from l_siteconfig"));
	echo nl2br($obj->todo);
}
?>