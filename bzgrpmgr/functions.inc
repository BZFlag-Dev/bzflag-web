<?php

// Copyright (c) 1993 - 2007 Tim Riker
//
// This package is free software;  you can redistribute it and/or
// modify it under the terms of the license found in the file
// named COPYING that should have accompanied this file.
//
// THIS PACKAGE IS PROVIDED ``AS IS'' AND WITHOUT ANY EXPRESS OR
// IMPLIED WARRANTIES, INCLUDING, WITHOUT LIMITATION, THE IMPLIED
// WARRANTIES OF MERCHANTIBILITY AND FITNESS FOR A PARTICULAR PURPOSE.

function print_header( $title ) {
	echo <<<ENCLOSE
<html>
	<head>
		<title>BZFlag Global Group Manager
ENCLOSE;
	echo ( $title ? " :: ".$title : "" );
	echo <<<ENCLOSE
</title>
		<link href="bzgrpmgr.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<img src="img/header.png"><br><br>

		<a href="?action=listgroups">Group List</a>&nbsp;|&nbsp;
		<a href="?action=listmemberships">My Memberships</a>&nbsp;|&nbsp;
		<a href="?action=creategroup">Create A Group</a><br><br>


ENCLOSE;
}

function print_footer() {
	echo <<<ENCLOSE
	</body>
</html>

ENCLOSE;
}

?>