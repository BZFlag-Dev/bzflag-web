<?
include "../document.php";

$doc = new Document;
$doc->begin("installation");
print <<< end
<b>windows</b><br><br>
The windows installer is a self-extracting executable. Click on link to
download the installer. Either open it from its current location or save
to disk and double click on the saved file in the explorer. Then follow
the installer wizard's steps to complete the installation. You can double
click the installed desktop icon to start BZFlag.<br><br>

To remove BZFlag, start the Add/Remove programs control panel, select
`BZFlag' and press the `Uninstall' button. An uninstaller dialog appears
and finishes it's job very quickly. Click the `OK' button. At this point,
all the files have been removed except two: the uninstaller itself
(bzfuinst.exe) and a file named bzflag.bzc. You can safely delete the
former. If you're going to install a new version of BZFlag, you probably
want to keep the latter file, which stores your configuration settings.
Otherwise, remove it and the directory it was in.<br><br>

<b>linux</b><br><br>
The Linux install can use either RPM or DEB formats. To install, use
rpm or apt, repectively. Its possible that the following symbolic links
may need to be made to get BZFlag to install properly over Mesa:<br>
$ ln -s libMesaGL.so.3 /usr/lib/libGL.so.1<br>
$ ln -s libMesaGLU.so.3 /usr/lib/libGLU.so.1<br>
Also, rpm may require the --nodeps option for some systems (to get
around broken dependencies). However, Mesa is absolutely required for DRI
systems.<br><br>

<b>irix</b><br><br>
If you have swmgr (software manager) and the appropriate MIME types installed, which is how systems are shipped:
<ol>
<li>Click on the link to download BZFlag</li>
<li>Wait for the download to finish</li>
<li>Enter your root password (if required)</li>
<li>Make sure `BZFlag' is selected for install</li>
<li>Press `Start'</li>
<li>Wait for the installation to complete</li>
<li>Quit software manager</li>
<li>Run BZFlag using `/usr/local/bin/bzflag' from a shell</li>
</ol>
If swmgr doesn't start or your browser forces you to save the file to disk then:
<ol>
<li>Save the downloaded file (bzflag.tardist)</li>
<li>Make an temporary directory: mkdir /usr/tmp/bzflag</li>
<li>Unpack the distribution: cat bzflag.tardist | ( cd /usr/tmp/bzflag; tar xvf - )</li>
<li>Point swmgr (or inst) at the temporary directory (you must normally be root to run swmgr or inst): swmgr -f /usr/tmp/bzflag</li>
<li>Install the software and quit swmgr.</li>
<li>Remove the unpacked tardist file: rm -rf /usr/tmp/bzflag</li>
<li>Run BZFlag using `/usr/local/bin/bzflag' from a shell</li>
</ol>
If you don't have root permissions, you can still use swmgr or inst by using an alternate root:

<ol>
<li>Save the downloaded file (bzflag.tardist)</li>
<li>Make an alternate root directory: mkdir /usr/tmp/bzflagroot</li>
<li>Unpack the distribution: cat bzflag.tardist | ( cd /usr/tmp/bzflagroot; tar xvf - )</li>
<li>Start swmgr (or inst) with an alternate root: swmgr -r /usr/tmp/bzflagroot -f /usr/tmp/bzflagroot</li>
<li>Install the software and quit swmgr.</li>
<li>Move the software to a permanent location. You'll find files under:</li>
<ul>
<li>/usr/tmp/bzflagroot/usr/local/bin/bzflag</li>
<li>/usr/tmp/bzflagroot/usr/local/bin/bzfs</li>
<li>/usr/tmp/bzflagroot/usr/local/lib/bzflag/*</li>
</ul>
<li>Remove the old installation stuff: rm -rf /usr/tmp/bzflagroot</li>
<li>Run BZFlag the first time with the -directory option, pointing to the location where you moved the data files to.</li>
</ol>

Note that you won't get the BZFlag desktop icons. You must be root for that.<br><br>

To uninstall BZFlag, use swmgr or inst. In swmgr, select `Managed
Installed Software,' choose BZFlag for removal, and click `Start'.
In inst, select BZFlag for removal (`remove BZFlag') and `go'.
end;
$doc->end();

?>
