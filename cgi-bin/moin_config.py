# -*- coding: iso-8859-15 -*-
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
mail_smarthost = 'localhost'
mail_from = 'BZFlag Wiki <noreply@BZFlag.org>'
title1 = """<div align="center">
  <table border="0" cellpadding="0" cellspacing="0" width="85%%">
    <tr>
      <td align="center">
        <img src="/images/title.png" alt="BZFlag">
      </td>
    </tr>
    <tr>
      <td>
        &nbsp;
      </td>
    </tr>
  </table>
  <table border="0" cellpadding="0" cellspacing="0" width="85%%"><!-- Main table for entire page -->
    <tr>
    <td valign="top">  <!-- left sidebar, row 1 main table, field one -->
    <table width="125" bgcolor="#AAAAAA" class="sidebar_border" border="0" cellspacing="0" cellpadding="4">
      <tr><td align="center">
      <table align="center" width="100" height=26 background="/images/content_header.png">
        <tr><td align="center">Contents</td></tr>
      </table>
      </td></tr>
      <tr><td align="center">
      <table border="0" cellpadding="2">
        <tr><td>
          <table class="sidebar_border" bgcolor="#FFFFFF" border="0" cellpadding="0">
          <tr><td>
            <a href="/" class="navbar">home</a><br>
            <a href="/wiki/GettingHelp" class="navbar">getting&nbsp;help</a><br>
            <a href="/wiki/Download" class="navbar">download</a><br>
            <a href="/help/" class="navbar">help</a><br>
            <a href="/wiki/Developers" class="navbar">developers</a><br>
            <a href="/screenshots/" class="navbar">screenshots</a><br>
            <a href="http://bzflag.cvs.sourceforge.net/bzflag/bzflag/COPYING?view=markup" class="navbar">license</a><br>
            <a href="/getin/" class="navbar">get&nbsp;involved!</a><br>
            <a href="/wiki/OtherLinks" class="navbar">links</a><br>
            <a href="/wiki/" class="navbar">wiki</a><br>
            <a href="http://my.BZFlag.org/" class="navbar">stats</a><br>
            <a href="http://my.BZFlag.org/bb/" class="navbar">forums</a><br>
            <a href="http://my.BZFlag.org/league/" class="navbar">CTF&nbsp;league</a><br>
            <a href="http://bzflag.cvs.sourceforge.net/bzflag/bzflag/TODO?view=markup" class="navbar">TODO</a><br>
            <br>
            <a href="http://sourceforge.net/projects/bzflag/" class="navbar">sourceforge</a><br>
            &nbsp;<a href="http://bzflag.cvs.sourceforge.net/bzflag" class="navbar">browse&nbsp;CVS</a><br>
<!-- Is this still in use?      &nbsp;<a href="/doxygen/" class="navbar">browse&nbsp;doxygen</a><br> -->
            &nbsp;<a href="http://sourceforge.net/tracker/?group_id=3248&amp;atid=103248" class="navbar">bug&nbsp;reports</a><br>
            &nbsp;<a href="http://sourceforge.net/cvs/?group_id=3248" class="navbar">CVS&nbsp;access</a><br>
            &nbsp;<a href="http://sourceforge.net/tracker/?atid=353248&amp;group_id=3248&amp;func=browse" class="navbar">feature&nbsp;requests</a><br>
            &nbsp;<a href="http://sourceforge.net/mail/?group_id=3248" class="navbar">mailing&nbsp;lists</a><br>
            &nbsp;<a href="http://sourceforge.net/tracker/?atid=423059&amp;group_id=3248&amp;func=browse" class="navbar">maps</a><br>
            &nbsp;<a href="http://sourceforge.net/tracker/?group_id=3248&amp;atid=203248" class="navbar">support</a><br>                  </td></tr>
          </table>
        </td></tr>
        <tr>
        <td align="center" >
          <table align="center" border="0" cellspacing="0" cellpadding="0">
          <tr><td><a href="http://www.opengl.org/"><img src="/images/opengl.gif" alt="opengl" width="88" height="31" border="0"></a></td></tr>
          <tr><td><a href="http://sourceforge.net/project/?group_id=3248"><img src="http://sourceforge.net/sflogo.php?group_id=3248&amp;type=1" width="88" height="31" alt="sourceforge" border="0"></a></td></tr>
          <tr><td><a href="http://sourceforge.net/donate/index.php?group_id=3248"><img src="http://images.sourceforge.net/images/project-support.jpg" width="88" height="32" border="0" alt="Support This Project"/></a></td></tr>
          <tr><td><a href="http://www.linuxgames.com/"><img src="/images/linuxgames.gif" width="88" height="31" alt="linuxgames" border="0"></a></td></tr>
          <tr><td><a href="http://www.telefragged.com/"><img src="/images/telefragged.gif" width="88" height="31" alt="telefragged" border="0"></a></td></tr>          </table>
          </td>
          </tr>
          </table>
        </td></tr>
        </table>
      </td>  <!-- End left sidebar, row 1 main table, field one -->

    <td bgcolor="#ffffff" valign="top">
      <table border="0" cellpadding="2" width="100%%"><tr><td>
        <div class="content">""" % locals()

page_footer2 = """        </div>
      </td></tr></table>
    </td></tr>
    <tr>
      <td>&nbsp;</td><td><table border="0" cellpadding="0" cellspacing="0" width="100%%"><tr>
        <td bgcolor="#ffffff" align="right">
          <span class="copyright">copyright &copy; <a href="/wiki/CurrentMaintainer">CurrentMaintainer</a> 1993-2002&nbsp;</span>
        </td>
      </tr></table></td>
    </tr>
  </table>

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
upperletters = "A-ZÀÁÂÃÄÅÆÈÉÊËÌÍÎÏÒÓÔÕÖØÙÚÛÜÝÇÐÑÞ"
lowerletters = "0-9a-zàáâãäåæèéêëìíîïòóôõöøùúûüýÿµßçðñþ"

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
# css_url = url_prefix + '/css/moinmoin.css' # stylesheet link, or ''
html_head = """
<META HTTP-EQUIV="Content-Type" CONTENT="text/html;charset=%s">
<link rel="stylesheet" type="text/css" href="/general.css">
<link href="/favicon.ico" rel="shortcut icon">
""" % (charset,)

# security (deactivated by default)
if 1:
    allowed_actions = ['DeletePage', 'AttachFile']

    from MoinMoin.security import Permissions
    class SecurityPolicy(Permissions):
        edit = 1
        delete = 1
        def __init__(self, user):
                global excluded_actions
                self.user = user

                # only allow edits/deletes by logged-in users
                self.edit = self.edit and user.valid
                self.delete = self.edit and user.valid
