#!/bin/sh
case "$QUERY_STRING" in
 bzadmin|bzflag|bzfs|bzw)
  case "$QUERY_STRING" in
    bzw)
      MAN_EXTENSION=5s
      ;;
    *)
      MAN_EXTENSION=6s
      ;;
  esac
  wget -O - http://cvs.sourceforge.net/viewcvs.py/*checkout\*/bzflag/bzflag/man/$QUERY_STRING.$MAN_EXTENSION?rev=HEAD \
  | man2html \
  | sed -e "s~^using the manual pages.<BR>$~using the manual pages from http://cvs.sourceforge.net/viewcvs.py/*checkout\*/bzflag/bzflag/man/$QUERY_STRING.6s<BR>~" \
  | sed -e "s~<A HREF=\"/cgi-bin/man/man2html\">Return to Main Contents</A><HR>~<a href=\"$SCRIPT_NAME\">Top</a><HR>~"
  ;;
 *)
  echo 'Content-type: text/html'
  echo ''
  echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'
  echo '<html>'
  echo '<head>'
  echo '<title>BZFlag man pages from CVS</title>'
  echo '</head>'
  echo '<body>'
  echo '<h1>Display <a href="http://BZFlag.org/">BZFlag</a> man pages from CVS.</h1>'
  echo '<ul>'
  echo '<li><a href="?bzadmin">bzadmin</a></li>'
  echo '<li><a href="?bzflag">bzflag</a></li>'
  echo '<li><a href="?bzfs">bzfs</a></li>'
  echo '<li><a href="?bzw">bzw</a></li>'
  echo '</ul>'
  echo '</body>'
  echo '</html>'
  ;;
esac
