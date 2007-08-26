<?php

// NOTE: RUN HTML VALIDATION!

// Copyright (c) 1993 - 2004 Tim Riker
//
// This package is free software;  you can redistribute it and/or
// modify it under the terms of the license found in the file
// named COPYING that should have accompanied this file.
//
// THIS PACKAGE IS PROVIDED ``AS IS'' AND WITHOUT ANY EXPRESS OR
// IMPLIED WARRANTIES, INCLUDING, WITHOUT LIMITATION, THE IMPLIED
// WARRANTIES OF MERCHANTIBILITY AND FITNESS FOR A PARTICULAR PURPOSE.

require_once( "functions.inc" );
require_once( "config.php" );
require_once( "data_phpbb2.inc" );

// Temporary userdata settings... will incorporate with weblogin later
$userdata['bzid'] = 2;
$userdata['callsign'] = getUsername( $userdata['bzid'] );
$userdata['admin'] = true;

// Connect to MySQL server
$mysql_connection = mysql_connect( $config['sql_hostname'],
		$config['sql_username'],
		$config['sql_password'], true ) or
				die( "Could not connect to MySQL database." );
mysql_select_db( $config['sql_database'], $mysql_connection ) or
		die( "Could not select MySQL database." );

// Begin main logic
$output = "";

// Auth section

// Action section
switch( $_GET['action'] ) {
	default:
		$output .= "Welcome, "./*$userdata['callsign'].*/"!<br><br>\n";

		// Little test to grab list of users
		$output .= "<b>Users in DB:</b><br>\n";
		foreach( getUserList() as $var ) {
			$output .= getusername( $var )." = user_id ".
					$var."<br>\n";
		}

		break;
}


// Dump output
print_header( "" );
echo $output;
print_footer();

?>