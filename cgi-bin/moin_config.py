"""
    MoinMoin - Configuration

    Copyright (c) 2000-2001 by Jürgen Hermann <jh@web.de>
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

# basic options (you normally need to change these)
sitename = 'BZFlag'
interwikiname = None
data_dir = '/home/groups/b/bz/bzflag/moin/data/'
url_prefix = '/wiki'
css_url = '/general.css'
logo_url = url_prefix + '/img/moinmoin.gif'
title1 = """<div align="center">
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
<a href="/cgi-bin/moin.cgi/OtherLinks" class="navbar">links</a><br>
<a href="/cgi-bin/moin.cgi/" class="navbar">wiki</a><br>
<a href="/league/" class="navbar">CTF&nbsp;league</a><br>
      </td></tr></table>
    </td>
    <td bgcolor="#ffffff" valign="top">
      <table border="0" cellpadding="2" width="100%"><tr><td>
        <div class="content">"""

page_footer2 = """        </div>
      </td></tr></table>
    </td></tr>
    <tr><td bgcolor="#ffffff" colspan="2" align="right">
      <span class="copyright">copyright &copy; 1993-2002&nbsp;</span>
    </td></tr>
  </table>
</td></tr></table>
<br>
<table border="0"><tr><td>
<a href="http://sourceforge.net/project/?group_id=3248"><img src="http://sourceforge.net/sflogo.php?group_id=3248&amp;type=1" alt="sourceforge" border="0"></a>
&nbsp;
<a href="http://www.opengl.org/"><img src="/images/opengl.gif" alt="opengl" border="0"></a>
&nbsp;
<a href="http://www.linuxgames.com/"><img src="/images/linuxgames.gif" alt="linuxgames" border="0"></a>
&nbsp;
<a href="http://www.telefragged.com/"><img src="/images/telefragged.gif" alt="telefragged" border="0"></a>
</table>
</div>"""

# encoding and WikiName char sets
# (change only for outside America or Western Europe)
charset = 'iso-8859-1'
upperletters = "A-ZÀÁÂÃÄÅÆÈÉÊËÌÍÎÏÒÓÔÕÖØÙÚÛÜİÇĞÑŞ"
lowerletters = "0-9a-zàáâãäåæèéêëìíîïòóôõöøùúûüıÿµßçğñş"

# options people are likely to change due to personal taste
show_hosts = 1                          # show hostnames?
nonexist_qm = 0                         # show '?' for nonexistent?
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
logo_string = '<img src="%s" border="0" alt="%s">' % (logo_url, sitename)
css_url = url_prefix + '/css/moinmoin.css' # stylesheet link, or ''
html_head = """
<META HTTP-EQUIV="Content-Type" CONTENT="text/html;charset=%s">
""" % (charset,)

# security (deactivated by default)
if 1:
#    allowed_actions = ['DeletePage', 'AttachFile']
    allowed_actions = ['AttachFile']

    from MoinMoin.security import Permissions
    class SecurityPolicy(Permissions):
        edit = 1
        delete = 1

