<?
if(!defined("__DOCUMENT_INC__")) {
define("__DOCUMENT_INC__", 1);

$db = mysql_connect("mysql", "bzflag", "foobar");
mysql_select_db("bzflag");

class Document {
  function begin($title, $session = 0) {
print <<< end
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>BZFlag - $title</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="/general.css">
</head>
<body>
<div align="center">
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#000000"><tr><td>
  <table border="0" cellpadding="0" cellspacing="1" width="100%">
    <tr>
      <td colspan="2"><table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr><td bgcolor="#013571" align="right"><img src="/images/logo2-1.jpg" alt="logo"></td>
	<td bgcolor="#818181" align="left"><img src="/images/logo2-2.jpg" alt=""></td></tr>
      </table></td>
    </tr>
    <tr>
    <td width="125" bgcolor="#ffffff" valign="top">
      <table border="0" cellpadding="2"><tr><td>
<a href="/" class="navbar">home</a><br>
<a href="/wiki/GettingHelp" class="navbar">getting&nbsp;help</a><br>
<a href="/wiki/Download" class="navbar">download</a><br>
<a href="/help/" class="navbar">help</a><br>
<a href="/developers/" class="navbar">developers</a><br>
<a href="/screenshots/" class="navbar">screenshots</a><br>
<a href="http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/bzflag/bzflag/COPYING?rev=HEAD&amp;content-type=text/vnd.viewcvs-markup" class="navbar">license</a><br>
<a href="/getin/" class="navbar">get&nbsp;involved!</a><br>
<a href="/wiki/OtherLinks" class="navbar">links</a><br>
<a href="/wiki/" class="navbar">wiki</a><br>
<a href="/league/" class="navbar">CTF&nbsp;league</a><br>
<a href="http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/bzflag/bzflag/TODO?rev=HEAD" class="navbar">TODO</a><br>
<br>
<a href="http://sourceforge.net/projects/bzflag/" class="navbar">sourceforge</a><br>
&nbsp;<a href="http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/bzflag" class="navbar">browse&nbsp;CVS</a><br>
&nbsp;<a href="/doxygen/" class="navbar">browse&nbsp;doxygen</a><br>
&nbsp;<a href="http://sourceforge.net/tracker/?group_id=3248&amp;atid=103248" class="navbar">bug&nbsp;reports</a><br>
&nbsp;<a href="http://sourceforge.net/cvs/?group_id=3248" class="navbar">CVS&nbsp;access</a><br>
&nbsp;<a href="http://sourceforge.net/tracker/?atid=353248&amp;group_id=3248&amp;func=browse" class="navbar">feature&nbsp;requests</a><br>
&nbsp;<a href="http://sourceforge.net/forum/?group_id=3248" class="navbar">forums</a><br>
&nbsp;<a href="http://sourceforge.net/mail/?group_id=3248" class="navbar">mailing&nbsp;lists</a><br>
&nbsp;<a href="http://sourceforge.net/tracker/?atid=423059&amp;group_id=3248&amp;func=browse" class="navbar">maps</a><br>
&nbsp;<a href="http://sourceforge.net/tracker/?group_id=3248&amp;atid=203248" class="navbar">support</a><br>
end;
    if($session == 1) {
print <<< end
<br><a href="/admin/" class="navbar">admin</a><br>
end;
    }
print <<< end
      </td></tr></table>
    </td>
    <td bgcolor="#ffffff" valign="top">
      <table border="0" cellpadding="2" width="100%"><tr><td>
	<div class="content">
end;
  }

  function end() {
print <<< end
	</div>
      </td></tr></table>
    </td></tr>
    <tr>
      <td colspan="2"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr>
	<td bgcolor="#ffffff" align="right">
end;
echo '      <span class="copyright">copyright &copy; <a href="/wiki/CurrentMaintainer">CurrentMaintainer</a> 1993-'.gmdate('Y').'&nbsp;</span>';
print <<< end
	</td>
      </tr></table></td>
    </tr>
  </table>
</td></tr></table>
<table border="0" cellpadding="2"><tr><td valign="middle" align="center">
<form method="GET" action="http://www.google.com/custom">
<input type="hidden" name="cof" value="AH:center;S:http://BZFlag.org/;AWFID:3e0e6d8d8d5bbf7d;">
<input type="hidden" name="domains" value="BZFlag.org">
<input type="hidden" name="sitesearch" value="BZFlag.org">
<a href="http://www.google.com"><img src="http://www.google.com/logos/Logo_25wht.gif" border="0" alt="Google" height="32" width="75" align="middle"></a>&nbsp;<input type="text" name="q" size="31" maxlength="255" value="">&nbsp;<input type="submit" name="sa" value="Search">
</form>
</td></tr><tr><td align="center">
<a href="http://www.opengl.org/"><img src="/images/opengl.gif" alt="opengl" width="88" height="31" border="0"></a>
<a href="http://sourceforge.net/project/?group_id=3248"><img src="http://sourceforge.net/sflogo.php?group_id=3248&amp;type=1" width="88" height="31" alt="sourceforge" border="0"></a>
<a href="http://www.linuxgames.com/"><img src="/images/linuxgames.gif" width="88" height="31" alt="linuxgames" border="0"></a>
<a href="http://www.telefragged.com/"><img src="/images/telefragged.gif" width="88" height="31" alt="telefragged" border="0"></a>
</td></tr></table>
</div>
</body>
</html>
end;
  }
}

}
?>
