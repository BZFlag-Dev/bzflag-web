#!/bin/sh

# use local man page, or pull from remote
USE_LOCAL=0

echo 'Content-type: text/html'
echo ''
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'
case "$QUERY_STRING" in
    bzadmin.6.in|bzflag.6.in|bzfs.6.in|bzw.5.in)
	content=""
	if [ "x$USE_LOCAL" = "x1" ] ; then
	    command="`echo $QUERY_STRING | /usr/bin/sed 's/\.[56]\.in$//'`"
	    content="`/usr/bin/man $command`"
	else
	    #content="`/usr/local/bin/wget -O - http://cvs.sourceforge.net/viewcvs.py/bzflag/bzflag/man/$QUERY_STRING?rev=HEAD`"
#http://bzflag.svn.sourceforge.net/viewvc/*checkout*/bzflag/branches/v2_0branch/bzflag/man/bzfs.6.in
	    content="`/usr/local/bin/wget -O - http://bzflag.svn.sourceforge.net/viewvc/*checkout*/bzflag/branches/v2_0branch/bzflag/man/$QUERY_STRING|/usr/bin/groff -man -T ascii 2>&1`"
	echo "$content" \
	    | /usr/local/bin/man2html \
	    | /usr/bin/sed -e "s~^using the manual pages.<BR>$~using the manual pages from http://cvs.sourceforge.net/viewcvs.py/bzflag/bzflag/man/$QUERY_STRING<BR>~" \
	    | /usr/bin/sed -e "s~<A HREF=\"bzfman.cgi\">Return to Main Contents</A><HR>~<a href=\"$SCRIPT_NAME\">Top</a><HR>~"
	    fi
	;;
    *)
	echo '<html>'
	echo '<head>'
	echo '<title>BZFlag man pages</title>'
	echo '</head>'
	echo '<body>'
	if [ "x$USE_LOCAL" = "x1" ] ; then
	echo '<h1>Display <a href="http://BZFlag.org/">BZFlag</a> latest release man pages.</h1>'
	else
	echo '<h1>Display <a href="http://BZFlag.org/">BZFlag</a> man pages from CVS.</h1>'
	fi
	echo '<ul>'
	echo '<li><a href="?bzadmin.6.in">bzadmin</a></li>'
	echo '<li><a href="?bzflag.6.in">bzflag</a></li>'
	echo '<li><a href="?bzfs.6.in">bzfs</a></li>'
	echo '<li><a href="?bzw.5.in">bzw</a></li>'
	echo '</ul>'
	echo '</body>'
	echo '</html>'
	;;
esac
# exit cleanly for httpd regardless of the last command exit code
return 0
