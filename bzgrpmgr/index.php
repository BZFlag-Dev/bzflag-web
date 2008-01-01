<?php

// Includes
require_once( 'include/global.inc' );

// Page content
require_once( 'template/header.inc' );

// ==================== General info ====================
?>

<p>
	<span class="large">
		Welcome<?php if( $auth->isLoggedIn() ) echo ", ".$auth->getUsername(); ?>!<br>
	</span>

<?php if( $auth->isLoggedIn() ) {
	?>

		You are logged in.

<?php
} else {
?>
Please log in to manage your groups.
<?php
} ?>

</p>

<?php
// ==================== My memberships ====================

if( $auth->isLoggedIn() ) {
	?>
<p>

<span class="large">My Memberships</span><br>

<?php

$groupids = $data->getGroupsByUser( $auth->getUserID() );
if( count( $groupids ) == 0 )
	echo "You are not a member of any groups at this time.<br>\n";
else {
	echo "<table>\n";
	echo "<tr><th>Group</th><th>Members</th></tr>\n";

	foreach( $groupids as $groupid )
		// Form an array of ORG.GROUP names to ids, so we can sort alphabetically
		$group_array[$data->getOrgName( $data->getGroupOrg( $groupid ) ).".".$data->getGroupName( $groupid )] = $groupid;
	asort( $group_array );

	foreach( $group_array as $groupname => $groupid )
		echo "<tr><td>".$groupname."</td><td>".count( $data->getGroupMembers( $groupid ) )."</td></tr>\n";

	echo "</table>\n";
}
?>

</p>

<?php
}

// ==================== Org and group data ====================

if( $auth->isLoggedIn() ) {
	?>
<p>

<span class="large">My Groups</span><br>

<?php

/*
++ Organization Name<info/admin link> ++
++++ Group Name<members link> ++++ <admin> ++++ <delete> ++++ (adminlevel) ++++
++++ Add a group ++++
++ Add an organization ++
*/

// Generate array of orgid's[groupid's]
$orgs = array();
foreach( $data->getGroupsByUser( $auth->getUserID() ) as $membergroupid ) {
	$orgid = $data->getOrg( $membergroupid );
	$org_groups = $data->getOrgGroups( $orgid );

	// $data->isGroupAdmin() is expensive, so we'll use another
	if( $data->isOrgAdminGroup( $membergroupid ) ) {
		// no need to bother with each group... they're all admined,
		// so add them all
		foreach( $org_groups as $groupid ) {
			if( ! $orgs[$orgid] )
				$orgs[$orgid] = array();

			// Use a hash here so we don't get duplicate group ids
			$orgs[$orgid][$groupid] = 1;
		}
	} else if( $data->isSpecialAdminGroup( $membergroupid ) ) {
		foreach( $org_groups as $groupid ) {
			// See if this is the one we have special perms on
			if( $data->isSpecialAdminGroup( $membergroupid, $groupid ) ) {
				if( ! $orgs[$orgid] )
					$orgs[$orgid] = array();

				// Use a hash here so we don't get duplicate group ids
				$orgs[$orgid][$groupid] = 1;
			}
		}
	}
}

if( count( $orgs ) == 0 )
	echo "You do not administer any groups at this time.<br>\n";
else {
	echo "<table>\n";
	echo "<tr><th colspan=\"3\">Name</th><th colspan=\"2\">&nbsp;</th></tr>\n";

	foreach( $orgs as $orgid => $group_array ) {
		// Org info
		echo "<tr class=\"org\"><td colspan=\"3\">".$data->getOrgName( $orgid )."</td>".
				"<td>".( $data->isOrgAdmin( $auth->getUserID(), $orgid ) ?
						"<img src=\"img/wrench.png\">" : "&nbsp;" )."</td>".
				"<td colspan=\"2\">&nbsp;</td></tr>\n";

		// Group name and links (if applicable)
		ksort( $group_array );
		foreach( array_keys( $group_array ) as $groupid )
			echo "<tr><td>".
					( $data->isOrgAdminGroup( $groupid, $orgid ) ? "<img src=\"img/key_high.png\">" :
							( $data->isSpecialAdminGroup( $groupid ) ?
									"<img src=\"img/key_low.png\">" : "&nbsp;" ) )."</td>".
					"<td>&nbsp;</td>".
					"<td>".$data->getGroupName( $groupid )."</td>".
					( $data->isGroupAdmin( $auth->getUserID(), $groupid ) ?
							"<td style=\"padding: 0\"><img src=\"img/wrench.png\"></td>".
							"<td><img src=\"img/delete.png\"></td>" :
							"<td>&nbsp;</td><td>&nbsp;</td>" ).
					"</tr>\n";

		// Final row for creating a new group
		if( $data->isOrgAdmin( $auth->getUserID(), $orgid ) )
			echo "<tr>".
					"<td>&nbsp;</td><td>&nbsp;</td>".
					"<td>New Group</td>".
					"<td><img src=\"img/new.png\"></td>".
					"<td>&nbsp;</td></tr>\n";
	}

	echo "</table>\n";
}

?>

<br><a href="">Click here to register a new organization.</a>

</p>

<?php
}

// ==================== Site Information ====================
?>

<p>
<span class="large">Site Information</span><br>
Total Organizations: <?php echo $data->getNumOrgs(); ?><br>
Total Groups: <?php echo $data->getNumGroups(); ?><br>
Current Users:<br>
Users Today:
</span>
</p>

<?php

require_once( 'template/footer.inc' );







/*
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
		break;
}


// Dump output
print_header( "" );
echo $output;
print_footer();
*/
?>