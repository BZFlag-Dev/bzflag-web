<?
include "../document.php";

$doc = new Document;
$doc->begin("download");
print <<< end
The newest downloads are available on the SourceForge
<a href="http://www.sourceforge.net/projects/bzflag">download page</a>
or from the
<a href="http://ftp1.sourceforge.net/bzflag/">downloads directory</a>.<br><br>

For those of you who like to be on the bleeding edge, you can download
the <a href="http://sourceforge.net/cvs/?group_id=3248">CVS sources</a>
and compile it yourself. There are also
<a href="/developers/compiling.php">compiling instructions</a>
available for various platforms.<br><br>

<b>BZFlag system requirements:</b><br><br>
BZFlag supports the following operating systems:<br>
<ul>
<li>Windows 95 or newer</li>
<li>Linux 2.1 or newer</li>
<li>Solaris (all versions)</li>
<li>Irix 5.3 or newer</li>
</ul>
BZFlag requires at least 4MB of disk space and 16MB of RAM. A working internet
connection is also required to play.<br><br>
These are also highly recommended:<br>
<ul>
<li>An OpenGL capable 3D graphics accelerator</li>
<li>A sound card</li>
<li>A fast internet connection</li>
</ul>
Your graphics card must support OpenGL to get any 3D accleration. Most modern
cards support OpenGL but Windows users may have to install extra drivers. You
should check your card vendor's web site for the latest drivers.<br><br>
Linux users should look to <a href="http://www.nvidia.com/">nVidia</a> or
<a href="http://dri.sourceforge.net/">The DRI Project</a> to set up OpenGL
acceleration.<br><br>
Irix users have hardware acceleration already, but not all systems accelerate
everything. You may have to turn off the depth buffer or texture mapping
in order to get decent performance.<br><br>
end;
$doc->end();

?>
