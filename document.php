<?
if(!defined("__DOCUMENT_INC__")) {
define("__DOCUMENT_INC__", 1);

$db = mysql_connect("mysql", "bzflag", "foobar");
mysql_select_db("bzflag");

class Document {
  function begin($title, $session = 0) {
print <<< end
<html>
<head>
<title>BZFlag - $title</title>
</head>
<link rel="stylesheet" type="text/css" href="/general.css">
<body>
<div align="center">
<table width="80%" border="0" cellpadding="0" cellspacing="0" bgcolor="#000000"><tr><td>
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
<a href="/download/" class="navbar">download</a><br>
<a href="/help/" class="navbar">help</a><br>
<a href="/developers/" class="navbar">developers</a><br>
<a href="/screenshots/" class="navbar">screenshots</a><br>
<a href="/getin/" class="navbar">get&nbsp;involved!</a><br>
<a href="/links/" class="navbar">links</a><br>
<a href="/cgi-bin/moin.cgi/" class="navbar">wiki</a><br>
<a href="/league/" class="navbar">CTF&nbsp;league</a><br>
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
    <tr><td bgcolor="#ffffff" colspan="2" align="right">
end;
echo '      <span class="copyright">copyright &copy; 1993-'.gmdate('Y').'&nbsp;</span>';
print <<< end
    </td></tr>
  </table>
</td></tr></table>
<br>
<table border="0"><tr><td>
<a href="http://sourceforge.net/project/?group_id=3248"><img src="http://sourceforge.net/sflogo.php?group_id=3248&type=1" alt="sourceforge" border="0"></a>
&nbsp;
<a href="http://www.opengl.org/"><img src="/images/opengl.gif" alt="opengl" border="0"></a>
&nbsp;
<a href="http://www.linuxgames.com/"><img src="/images/linuxgames.gif" alt="linuxgames" border="0"></a>
&nbsp;
<a href="http://www.telefragged.com/"><img src="/images/telefragged.gif" alt="telefragged" border="0"></a>
</table>
</div>
</body>
</html>
end;
  }
}

}
?>
