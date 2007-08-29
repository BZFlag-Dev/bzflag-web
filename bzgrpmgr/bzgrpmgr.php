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

require_once( "data_phpbb2.class.php" );
require_once( "functions.inc" );
require_once( "config.php" );

// Temporary userdata settings... will incorporate with weblogin later
$userdata['bzid'] = 2;
$userdata['callsign'] = $data->getUsername( $userdata['bzid'] );
$userdata['admin'] = true;

// Begin main logic
$output = "";

// Auth section

// Action section
switch( $_GET['action'] ) {
	case "listmemberships":
		$output .= "\t\t<table border=1 cellpadding=2>\n";
		foreach( $data->getGroups( $userdata['bzid'] ) as $groupid ) {
			$output .= "\t\t\t<tr><td>".
					$data->getGroupname( $groupid ).
					"</td></tr>\n";
		}
		$output .= "\t\t</table>\n";

		break;
	case "creategroup":
		$output .= "\t\t<dl><dt>Organizations</dt>\n";
		$output .= "\t\t<dd>None</dd>\n";
		$output .= "\t\t</dl><br>\n";

		// This will be an AJAX pop-down thing later on, hopefully
//		$output .= "\t\t<dl><dt>Groups</dt>\n";
//		$output .= "\t\t<dd>None</dd>\n";
//		$output .= "\t\t</dl><br>\n";
		
		break;
	default:
		$output .= "Welcome, ".$userdata['callsign']."!<br><br>\n";

		// Little test to grab list of users
		$output .= "<b>Users in DB:</b><br>\n";
		foreach( $data->getUserList() as $var ) {
			$output .= $data->getUsername( $var )." = user_id ".
					$var."<br>\n";
		}

		break;
}


// Dump output
print_header( "" );
echo $output;
print_footer();

?>