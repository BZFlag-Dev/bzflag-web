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
	default:
		$output .= "\t\t\tMy Memberships:<br>\n";
		$output .= "\t\t\t<br>\n\n";

		$output .= "\t\t\tAdmin:<br>\n";
		foreach( $data->getOrgAdminships( $userdata['bzid'] ) as $orgid ) {
			$output .= "\t\t\t".$data->getOrgName( $orgid )."<br>\n";
			if( $data->isUserAdmin( $orgid, $userdata['bzid'] ) ) 
				$output .= " and we're a user admin too<br>\n";
			if( $data->isGroupAdmin( $orgid, $userdata['bzid'] ) ) 
				$output .= " and we're a group admin too<br>\n";
			else echo "no<br>\n";
			if( $data->isAdmin( $orgid, $userdata['bzid'] ) ) 
				$output .= " and we're an admin too<br>\n";
		}

		break;
}


// Dump output
print_header( "" );
echo $output;
print_footer();

?>