<?php
// bzfls.php
//
// Copyright (c) 1993 - 2004 Tim Riker
//
// This package is free software;  you can redistribute it and/or
// modify it under the terms of the license found in the file
// named COPYING that should have accompanied this file.
//
// THIS PACKAGE IS PROVIDED ``AS IS'' AND WITHOUT ANY EXPRESS OR
// IMPLIED WARRANTIES, INCLUDING, WITHOUT LIMITATION, THE IMPLIED
// WARRANTIES OF MERCHANTIBILITY AND FITNESS FOR A PARTICULAR PURPOSE.

# where to send debug printing (might override below)

include('config.php.inc');

$TheRootDataPath = $rootDataPath;

function isRealDir ( $name )
{
	if ( $name == ".")
		return FALSE;
	if ( $name == "..")
		return FALSE;
	return TRUE;
}
function parseDir ($dir,$root)
{
	$thisRealDir = $root . $dir;
	if(!($dh = opendir($thisRealDir)))
		return;
	//echo "dir open for $dir<br><br>";
	
 	while (($file = readdir($dh)) !== false)
	{
		if ( filetype($thisRealDir . $file) == 'dir')
		{
			//echo "file $file" . "<br>";
			if (isRealDir($file))
			{
				//echo "parsedir $dir" . "$file" . "/". "<br>";
				$newDir = $dir . $file . '/';
				parseDir($newDir,$root);
			}
		}
		else
	   		echo "$dir$file : ". md5_file($thisRealDir . $file) . "<br>";
	}
	closedir($dh);
}

// start of script
// figure out what we are doing
if ( array_key_exists("action", $_GET) )
	$action =  $_GET['action'];
else
	$action = "list";
	
// figure out what we are doing it for
$versionToUse = $defaultVersionNumber;
if (array_key_exists("version", $_GET))
	$versionToUse = $_GET['version'];
	
// figure out where the freaking data even IS
$TheRootDataPath = $rootDataPath . '/' . $versionToUse . '/';
	
if ($action == "list")
{
	$startDir = '';
	echo "Files for $versionToUse<br><br>";
	parseDir($startDir,$TheRootDataPath);
}
else if ($action == "get" && array_key_exists("file", $_GET))
{
	$file = $_GET['file'];
	$filePath = $TheRootDataPath . $file;
	readfile($filePath);
}
else
{
	 echo "ERROR = 404, WTF? Command $action not known";
}

?>