<?
include "../document.php";

$doc = new Document;
$doc->begin("compiling instructions");
print <<< end
<b>windows</b><br><br>
Building bzflag on windows requires Microsoft Visual C++. The process is as follows:
<pre>
open win32\bzflag.dsw
select `bzflag Release' as the active configuration
build
select `bzfs Release' as the active configuration
build
select `bzfls Release' as the active configuration
build

Then to build the installer:
open package\win32\install.dsw
select `bzfinst Release' as the active configuration
rebuild all
--- or ---
open package\win32\install.dsw
select `bzfuinst Release' as the active configuration
build
select `makedb Release' as the active configuration
build
then in a command prompt:
  > cd package\win32
  > mkdir lib
  > bin/makedb.exe -i bzflag.spc -o lib\database.obj -b 1048576
  (makedb compresses the files. larger block sizes
   take longer but compress better)
back in VC++:
select `bzfinst Release' as the active configuration
build

the installer will be in dist\bzfinst.exe
</pre>
<br><br>
<b>*nix</b><br><br>
building for *nix operating systems is fairly easy.<br>
<pre>
$ make &lt;platform&gt;
where &lt;platform&gt; is the operating system you wish to build for.
Available platforms are:
  irix-mips2
  irix-mips3
  linux
  linux-i386
  linux-ppc
  solaris-cc
  solaris-gcc
  win32
followed by:
$ make
binaries will be in ./bin
</pre>
<br><br>
end;
$doc->end();

?>
