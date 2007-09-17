<?
if(!defined("__DOCUMENT_INC__")) {
define("__DOCUMENT_INC__", 1);

$db = mysql_connect("server", "user", "password");
mysql_select_db("database");

class Document {
  function begin($title, $session = 0) {
print <<< end
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>BZFlag - $title</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" type="text/css" href="/general.css">
<link href="/favicon.ico" rel="shortcut icon">
</head>
<body>
<div align="center">
	<table border="0" cellpadding="0" cellspacing="0" width="85%">
		<tr>
			<td align="center">
				<img src="/images/title.png">
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
		</tr>
	</table>
	<table border="0" cellpadding="0" cellspacing="0" width="85%"><!-- Main table for entire page -->
		<tr>
			<td valign="top">	<!-- left sidebar, row 1 main table, field one -->
				<table width="125" bgcolor="#AAAAAA" class="sidebar_border" border="0" cellspacing="0" cellpadding="4">
					<tr><td align="center">
						<table align="center" height="26" width="100" background="/images/content_header.png">
							<tr><td align="center">Contents</td></tr>
						</table>
					</td></tr>

					<tr><td align="center">
						<table border="0" cellpadding="2">
							<tr><td>
								<table class="sidebar_border" bgcolor="#FFFFFF" border="0" cellpadding="0">
									<tr><td>
										<a href="/" class="navbar">home</a><br>
										<a href="http://my.bzflag.org/w/Getting Help" class="navbar">getting&nbsp;help</a><br>
										<a href="http://my.bzflag.org/w/Download" class="navbar">download</a><br>
										<a href="/help/" class="navbar">help</a><br>
										<a href="http://my.bzflag.org/w/BZFlag_Source" class="navbar">developers</a><br>
										<a href="/screenshots/" class="navbar">screenshots</a><br>
										<a href="http://bzflag.svn.sourceforge.net/viewvc/*checkout*/bzflag/trunk/bzflag/COPYING" class="navbar">license</a><br>
										<a href="/getin/" class="navbar">get&nbsp;involved!</a><br>
										<a href="http://my.bzflag.org/w/Other_Links" class="navbar">links</a><br>
										<a href="http://my.bzflag.org/w/Main_Page" class="navbar">wiki</a><br>
										<a href="http://my.BZFlag.org/" class="navbar">stats</a><br>
										<a href="http://my.BZFlag.org/bb/" class="navbar">forums</a><br>
										<a href="http://my.BZFlag.org/league/" class="navbar">CTF&nbsp;league</a><br>
										<a href="http://bzflag.svn.sourceforge.net/viewvc/*checkout*/bzflag/trunk/bzflag/TODO" class="navbar">TODO</a><br>
										<br>
										<a href="http://sourceforge.net/projects/bzflag/" class="navbar">sourceforge</a><br>
										&nbsp;<a href="http://bzflag.svn.sourceforge.net/viewvc/bzflag/" class="navbar">browse&nbsp;Subversion</a><br>
										&nbsp;<a href="http://sourceforge.net/tracker/?group_id=3248&amp;atid=103248" class="navbar">bug&nbsp;reports</a><br>
										&nbsp;<a href="http://my.bzflag.org/w/BZFlag_SVN" class="navbar">SVN&nbsp;access</a><br>
										&nbsp;<a href="http://sourceforge.net/tracker/?atid=353248&amp;group_id=3248&amp;func=browse" class="navbar">feature&nbsp;requests</a><br>
										&nbsp;<a href="http://sourceforge.net/mail/?group_id=3248" class="navbar">mailing&nbsp;lists</a><br>
										&nbsp;<a href="http://sourceforge.net/tracker/?atid=423059&amp;group_id=3248&amp;func=browse" class="navbar">maps</a><br>
										&nbsp;<a href="http://sourceforge.net/tracker/?group_id=3248&amp;atid=203248" class="navbar">support</a><br>
end;
if($session == 1){
print <<< end
	<br><a href="/admin/" class="navbar">admin</a><br>
end;
}
print <<< end
									</td></tr>
								</table>
							</td></tr>
							<tr>
								<td align="center" >
									<table align="center" border="0" cellspacing="0" cellpadding="0">
										<tr><td><a href="http://www.opengl.org/"><img src="/images/opengl.gif" alt="opengl" width="88" height="31" border="0"></a></td></tr>
										<tr><td><a href="http://sourceforge.net/project/?group_id=3248"><img src="http://sourceforge.net/sflogo.php?group_id=3248&amp;type=1" width="88" height="31" alt="sourceforge" border="0"></a></td></tr>
										<tr><td><a href="http://sourceforge.net/donate/index.php?group_id=3248"><img src="http://images.sourceforge.net/images/project-support.jpg" width="88" height="32" border="0" alt="Support This Project"/></a></td></tr>
										<tr><td><a href="http://www.linuxgames.com/"><img src="/images/linuxgames.gif" width="88" height="31" alt="linuxgames" border="0"></a></td></tr>
										<tr><td><a href="http://www.telefragged.com/"><img src="/images/telefragged.gif" width="88" height="31" alt="telefragged" border="0"></a></td></tr>
									</table>
								</td>
							</tr>
						</table>
					</td></tr>
				</table>
			</td>	<!-- End left sidebar, row 1 main table, field one -->

			<td bgcolor="#ffffff" valign="top"> 	<!-- right main area, row 1 main table, field 2 -->
				<table bgcolor="#888888" border="0" cellspacing="0" cellpadding="0" width="100%">
					<tr>
						<td>
							<!-- Main content area -->
							<table class="sidebar_border" border="0" cellspacing="0" cellpadding="6" bgcolor="#FFFFFF">
								<tr><td>
									<div class="content">
end;
  }

	function end(){
print <<< end
								</div>
								</tr></td>
							</table>
						</td>
					</tr>
				</table>

			</td><!-- End right main area, row 1 main table, field 2 -->
		</tr>
	</table><!-- End main table for entire page -->

	<table width="85%" class="sidebar_border" bgcolor="#FFFFFF" border="0" cellpadding="2"> <!-- Search bar at bottom of page -->
		<tr>
			<td valign="middle" align="left">
				<form method="GET" action="http://www.google.com/custom">
				Search This Site<input type="hidden" name="cof" value="AH:center;S:http://BZFlag.org/;AWFID:3e0e6d8d8d5bbf7d;">
				<input type="hidden" name="domains" value="BZFlag.org">
				<input type="hidden" name="sitesearch" value="BZFlag.org">
				<a href="http://www.google.com"><img src="http://www.google.com/logos/Logo_25wht.gif" border="0" alt="Google" height="32" width="75" align="middle"></a>&nbsp;<input type="text" name="q" size="31" maxlength="255" value="">&nbsp;<input type="submit" name="sa" value="Search">
				</form>
			</td>
			<td valign="middle" align="right">
				<table border="0" cellpadding="0" cellspacing="0" width="85%">
					<tr>
						<td bgcolor="#ffffff" align="right">
end;
echo '<span class="copyright">copyright &copy; <a href="/wiki/CurrentMaintainer">CurrentMaintainer</a> 1993-'.gmdate('Y').'&nbsp;</span>';
print <<< end
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>

</body>
</html>
end;
  }
}

}
?>
