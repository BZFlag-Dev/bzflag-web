"""
    MoinMoin - Configuration

    Copyright (c) 2000-2001 by J¸rgen Hermann <jh@web.de>
    All rights reserved, see COPYING for details.

    Note that there are more config options than you'll find in
    the version of this file that is installed by default; see
    the module MoinMoin.config for a full list of names and their
    default values.

    Also, the URL http://purl.net/wiki/moin/HelpOnConfiguration has
    a list of config options.

    $Id$
"""
__version__ = "$Revision$"[11:-2]

# If you run several wikis on one host (commonly called a wiki farm),
# uncommenting the following allows you to load global settings for
# all your wikis. You will then have to create "farm_config.py" in
# the MoinMoin package directory.
#
# from MoinMoin.farm_config import *
import os

# basic options (you normally need to change these)
sitename = 'BZFlag'
interwikiname = None
data_dir = '/home/groups/b/bz/bzflag/moin/data/'
url_prefix = '/wiki'
css_url = '/general.css'
logo_url = url_prefix + '/img/moinmoin.gif'
#url_path = os.environ.get("SCRIPT_NAME", "/wiki") \
#     + os.environ.get("PATH_INFO", "/FrontPage")
url_path = "/wiki" + os.environ.get("PATH_INFO", "/FrontPage")
page_icons = ''
page_footer1 = '<hr>'
navi_bar = ''
title1 = """<div align="center">
<table width="100%%" border="0" cellpadding="0" cellspacing="0" bgcolor="#000000"><tr><td>
  <table border="0" cellpadding="0" cellspacing="1" width="100%%">
    <tr>
      <td colspan="2"><table border="0" cellpadding="0" cellspacing="0" width="100%%">
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
<a href="http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/bzflag/bzflag/LICENSE?rev=HEAD&amp;content-type=text/vnd.viewcvs-markup" class="navbar">license</a><br>
<a href="/getin/" class="navbar">get&nbsp;involved!</a><br>
<a href="/wiki/OtherLinks" class="navbar">links</a><br>
<a href="/league/" class="navbar">CTF&nbsp;league</a><br>
<a href="http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/bzflag/bzflag/TODO?rev=HEAD" class="navbar">TODO</a><br>
<br>
<a href="/wiki/FrontPage" class="navbar">wiki</a><br>
&nbsp;<a href="/wiki/FindPage" class="navbar">FindPage</a><br>
&nbsp;<a href="/wiki/HelpContents" class="navbar">HelpContents</a><br>
&nbsp;<a href="/wiki/OtherLinks" class="navbar">OtherLinks</a><br>
&nbsp;<a href="/wiki/RecentChanges" class="navbar">RecentChanges</a><br>
&nbsp;<a href="/wiki/UserPreferences" class="navbar">UserPreferences</a><br>
<a href="%(url_path)s" class="navbar">page</a><br>
&nbsp;<a href="%(url_path)s?action=edit" class="navbar">edit</a><br>
&nbsp;<a href="%(url_path)s?action=info" class="navbar">info</a><br>
&nbsp;<a href="%(url_path)s?action=print" class="navbar">print</a><br>
&nbsp;<a href="%(url_path)s?action=subscribe" class="navbar">subscribe</a><br>
&nbsp;<a href="%(url_path)s?action=format&amp;mimetype=text/xml" class="navbar">XML</a><br>
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
      </td></tr></table>
    </td>
    <td bgcolor="#ffffff" valign="top">
      <table border="0" cellpadding="2" width="100%%"><tr><td>
        <div class="content">""" % locals()

page_footer2 = """        </div>
      </td></tr></table>
    </td></tr>
    <tr>
      <td colspan="2"><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr>
	<td bgcolor="#ffffff" align="right">
	  <span class="copyright">copyright &copy; <a href="/wiki/CurrentMaintainer">CurrentMaintainer</a> 1993-2002&nbsp;</span>
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
<a href="http://sourceforge.net/donate/index.php?group_id=3248"><img src="http://images.sourceforge.net/images/project-support.jpg" width="88" height="32" border="0" alt="Support This Project"></a>
<a href="http://www.linuxgames.com/"><img src="/images/linuxgames.gif" width="88" height="31" alt="linuxgames" border="0"></a>
<a href="http://www.telefragged.com/"><img src="/images/telefragged.gif" width="88" height="31" alt="telefragged" border="0"></a>
</td></tr></table>
</div>"""

# encoding and WikiName char sets
# (change only for outside America or Western Europe)
charset = 'iso-8859-1'
upperletters = "A-Z¿¡¬√ƒ≈∆»… ÀÃÕŒœ“”‘’÷ÿŸ⁄€‹›«–—ﬁ"
lowerletters = "0-9a-z‡·‚„‰ÂÊËÈÍÎÏÌÓÔÚÛÙıˆ¯˘˙˚¸˝ˇµﬂÁÒ˛"

# options people are likely to change due to personal taste
show_hosts = 1                          # show hostnames?
nonexist_qm = 1                         # show '?' for nonexistent?
backtick_meta = 1                       # allow `inline typewriter`?
allow_extended_names = 1                # allow ["..."] markup?
edit_rows = 30                          # editor size
max_macro_size = 50                     # max size of RecentChanges in KB (0=unlimited)

# charting needs "gdchart" installed!
# you can remove the test and gain a little speed (i.e. keep only
# the chart_options assignment, or remove this code section altogether)
try:
    import gdchart
    chart_options = {'width': 720, 'height': 400}
except ImportError:
    pass

# values that depend on above configuration settings
#logo_string = '<img src="%s" border="0" alt="%s">' % (logo_url, sitename)
logo_string = sitename
css_url = url_prefix + '/css/moinmoin.css' # stylesheet link, or ''
html_head = """
<META HTTP-EQUIV="Content-Type" CONTENT="text/html;charset=%s">
<link href="/favicon.ico" rel="shortcut icon">
""" % (charset,)

# security (deactivated by default)
if 1:
#    allowed_actions = ['DeletePage', 'AttachFile']
    allowed_actions = ['AttachFile']

    from MoinMoin.security import Permissions
    class SecurityPolicy(Permissions):
        edit = 1
        delete = 1
