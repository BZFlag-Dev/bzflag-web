<?php

// Includes
require_once( 'include/global.php' );

// Page content
require_once( 'template/header.php' );

?>

<p><i>Please note: this system is currently a work-in-progress. Some features may not work well or at all. The development for this system follows the guidelines located at the BZFlag Wiki <a href="http://my.bzflag.org/w/Group_Management_System" target="blank">here</a>. To contribute to this project, please visit the #bzflag channel on the Freenode IRC network to discuss current development.</i></p>

<p>Welcome to the BZFlag Global Group Management system. BZFlag is a free online multiplayer cross-platform open source 3D tank battle game. Global groups enable server owners to grant permissions to users based on their membership in these groups. Groups are organized into organizations. To use this management system, you must first log in with your BZFlag global username and password. To register, please visit <a href="http://my.bzflag.org/bb">http://my.bzflag.org/bb</a>.</p>

<p>Once you are logged in, additional options will be available to you. To register an organization, please click the "Create an Organization" link. An organization may contain any number of groups. Once groups are created, users may be added as members. To view your current group memberships, please click the "My Memberships" link. To manage groups or add members, click the "My Groups" link.</p>

<p>For more information on BZFlag or to download the game, please visit <a href="http://www.bzflag.org">http://www.bzflag.org</a>.</p>

<?php

require_once( 'template/footer.php' );








/*
require_once( "data_phpbb2.class.php" );
require_once( "functions.php" );
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
	case "showmembers":
		if( ! $_GET['id'] ) die( "Missing parameter.\n" );

		$output .= "\t\t<b>Members:</b><br><br>\n\n";
		$output .= "\t\tGroup Name: <i>".
				$data->getOrgName( $data->getOrg( $_GET['id'] ) ).".".
				$data->getGroupName( $_GET['id'] ).
				"</i><br><br>\n\n";

		// FIXME: check data input
		$members = false;
		foreach( $data->getMembers( $_GET['id'] ) as $memberid ) {
			$members = true;
			$output .= $data->getUsername( $memberid )."<br>\n";
		}
		if( ! $members ) {
			$output .= "\t\t<i>No members.</i>\n";
		}

		$output .= "\t\t<br><br>\n\n";

		break;
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
			
			print_header( "" );
			echo $output;
			print_footer();
			exit;
		}

		// Create the organization
		$data->createOrg( $_POST['orgname'], $userdata['bzid'] );
		header( "Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'] );
		break;
	case "editgroup":
		// FIXME check for group validity, etc.
		// FIXME add support for group state

		// User is editing the settings of a group or has clicked the "new group" link
		if( $_GET['groupid'] )
			// We're editing
			$output .= "\t\tEditing settings for group <b>".
					$data->getOrgName( $data->getOrg( $_GET['groupid'] ) ).".".
					$data->getGroupName( $_GET['groupid'] )."</b>.<br><br>\n\n";
		else
			// We're creating a new group
			$output .= "\t\tCreate a group for the</i> <b>".
					$data->getOrgName($_GET['orgid'])."</b> organization.<br><br>\n\n";


		// create values array
		$values = array(
				'groupname' => $data->getGroupName( $_GET['groupid'] ),
				'description' => $data->getGroupDescription( $_GET['groupid'] ),
				'adminusers' => $data->isUserAdminGroup( $_GET['groupid'] ),
				'admingroups' => $data->isGroupAdminGroup( $_GET['groupid'] ),
				'admin' => $data->isAdminGroup( $_GET['groupid'] )
				);

		// output form
		$output .= <<<ENCLOSE
		<form method="POST" action="bzgrpmgr.php">
		<input type="hidden" name="action" value="submiteditgroup">
		<input type="hidden" name="orgid" value="
ENCLOSE;
		$output .= $_GET['orgid'];
		$output .= <<<ENCLOSE
">
		<input type="hidden" name="groupid" value="
ENCLOSE;
		$output .= ( $_GET['groupid'] ? $_GET['groupid'] : "0" );
		$output .= <<<ENCLOSE
">
		<table>
			<tr>
				<td>Group name:</td>
				<td><input type="text" name="groupname" value="
ENCLOSE;
		$output .= $values['groupname'];
		$output .= <<<ENCLOSE
" size="20"></td>
			</tr>
			<tr>
				<td>Group description:</td>
				<td><input type="text" name="description" value="
ENCLOSE;
		$output .= $values['description'];
		$output .= <<<ENCLOSE
" size="40"></td>
			</tr>
			<tr>
				<td>Members are full organization admins?</td>
				<td><input type="checkbox" name="admingroups"
ENCLOSE;
		if( $values['admin'] ) $output .= " checked";
		$output .= <<<ENCLOSE
></td>
			</tr>
			<tr>
				<td>Members may manage users for the organization?</td>
				<td><input type="checkbox" name="admingroups"
ENCLOSE;
		if( $values['admingroups'] ) $output .= " checked";
		$output .= <<<ENCLOSE
></td>
			</tr>
				<td>Members may manage groups for the organization?</td>
				<td><input type="checkbox" name="admingroups"
ENCLOSE;
		if( $values['adminusers'] ) $output .= " checked";
		$output .= <<<ENCLOSE
></td>
			</tr>
			<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="
ENCLOSE;
		$output .= ( $_GET['groupid'] ? "Update" : "Create" );
		$output .= <<<ENCLOSE
"></td></tr>
		</table>
		</form>

ENCLOSE;
		break;
	case "submiteditgroup":
		// FIXME validate data entry
		// FIXME check against user data
		// check for existing group name

// foreach( $_POST as $key => $value ) $output .= $key." => ".$value."<br>\n";

		// If new group, make sure the group name doesn't already exist in the organization
		$existingGroupNames = array();
		foreach( $data->getOrgGroups( $_POST['orgid'] ) as $groupid ) {
			$existingGroupNames[$data->getGroupName( $groupid )] = 1;
		}
		if( $existingGroupNames[$_POST['groupname']] ) {
			$output .= "The given organization name already exists.<br>\n";

			print_header( "" );
			echo $output;
			print_footer();
			exit;
		}

		// FIXME parse the post data for validity
		if( $_POST['groupid'] ) {
			// Update the group
			$data->updateGroup( $_POST['groupid'], $_POST['groupname'],
					$_POST['description'], $_POST['orgid'] );

			// Redirect to home 
			header( "Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'] );
		} else {
			// Create the group
			$myGroupid = $data->createGroup( $_POST['groupname'], $_POST['description'], $_POST['orgid'] );
			$data->setUserAdminGroup( $_POST['groupid'], $_POST['adminusers'] );
			$data->setGroupAdminGroup( $_POST['groupid'], $_POST['admingroups'] );
			$data->setAdminGroup( $_POST['groupid'], $_POST['admin'] );

			// Redirect to home 
			header( "Location: http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'] );
		}

		break;
	default:
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
				$output .= "<td><a href=\"?action=showmembers&id=".$groupid."\">";
				$output .= $data->getGroupName( $groupid );
				$output .= "</a></td>";
				$output .= ( $data->isGroupAdmin( $orgid, $userdata['bzid'] ) ?
						"<td><a href=\"?action=editgroup&groupid=".$groupid."\">".
								"Settings</a></td>" :
//						"<td><a href=\"?action=groupdelete&id=".$groupid."\">".
//								"Delete</a></td>"
						"<td colspan=\"3\">&nbsp;</td>" ).
						"</tr>\n";
			}

			// Final row for creating a new group
			if( $data->isGroupAdmin( $orgid, $userdata['bzid'] ) )
				$output .= "\t\t\t<tr><td>&nbsp;</td><td colspan=\"2\">".
						"<a href=\"?action=editgroup&orgid=".
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
*/
?>
