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
switch( $_REQUEST['action'] ) {
	case "createorg":
		// User has clicked the "new organization" link
		$output .= <<<ENCLOSE
		<form method="POST" action="bzgrpmgr.php">
		<input type="hidden" name="action" value="submitcreateorg">
		<table>
			<tr>
				<td>Organization name:</td>
				<td><input type="text" name="orgname" value="" size="20"></td>
			</tr>
			<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="Create"></td></tr>
		</table>
		</form>

		<i>Note: The organization name must not be the username of an existing user.</i>

ENCLOSE;
		break;
	case "submitcreateorg":
		// FIXME validate data entry
		// FIXME check against user data
		// FIXME block against reserved group names

		// check for existing group name
		// FIXME create clean sitewide die method?
		if( $data->orgExists( $_POST['orgname'] ) ) {
			$output .= "The given organization name already exists.<br>\n";		
			exit;
		}

		// Create the organization
		echo $data->createOrg( $_POST['orgname'], $userdata['bzid'] );

		break;
	default:
$data->createGroup( "testgroup", "owners group", 1 );
		$output .= "\t\t<b>Groups:</b>\n";

		$orgs = array();
		foreach( $data->getGroups( $userdata['bzid'] ) as $groupid ) {
			// Generate array of orgid's->groupid's
			$orgid = $data->getOrg( $groupid );
			$orgs[$orgid] = array();

			foreach( $data->getOrgGroups( $orgid ) as $groupid )
				array_push( $orgs[$orgid], $groupid );
		}

		// Output orgs, groups, and associated links
		$output .= "\t\t<table>\n";
		foreach( $orgs as $orgid => $group_array ) {

			// Org name & admin link (if applicable)
			$output .= "\t\t\t<tr><td colspan=\"4\">";
			if( $data->isGroupAdmin( $orgid, $userdata['bzid'] ) )
				$output .= "<a href=\"?action=orgadmin&id=".$orgid."\">".
						$data->getOrgName( $orgid )."</a>";
			else
				$output .= $data->getOrgName( $orgid );
			$output .= "</td></tr>\n";

			// Group name and links (if applicable)
			foreach( $group_array as $groupid ) {
				$output .= "\t\t\t<tr><td>&nbsp;</td>";
				$output .= "<td>".$data->getGroupName( $groupid )."</td>";
				$output .= ( $data->isGroupAdmin( $orgid, $userdata['bzid'] ) ?
						"<td><a href=\"?action=groupadmin&id=".$groupid."\">".
								"Settings</a></td>".
						"<td><a href=\"?action=groupdelete&id=".$groupid."\">".
								"Delete</a></td>" :
						"<td colspan=\"2\">&nbsp;</td>" ).
						"</tr>\n";
			}

			// Final row for creating a new group
			if( $data->isGroupAdmin( $orgid, $userdata['bzid'] ) )
				$output .= "\t\t\t<tr><td>&nbsp;</td><td colspan=\"2\"><a href=\"?creategroup&id=".
						$orgid."\"><i>New Group</i></a></td></tr>\n";
		}
		// Final row for creating a new organization
		$output .= "\t\t\t<tr><td colspan=\"3\"><a href=\"?action=createorg\"><i>New Organization".
				"</i></a></td></tr>\n";
		$output .= "\t\t</table>\n";

		break;
}


// Dump output
print_header( "" );
echo $output;
print_footer();

?>