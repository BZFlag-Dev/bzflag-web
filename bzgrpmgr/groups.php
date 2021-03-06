<?php

// Includes
require_once( 'include/global.php' );

if(isset($_GET['ou']) && isset($_GET['grp'])) {
	$got_data = true;
	$org_name = $_GET['ou'];
	$group_name = $_GET['grp'];
	$data->fillAllUserInfo($auth->getUserID());
} else
	$got_data = false;


// Main action switch
switch( $_GET['action'] ) {
	case "members":
		if( ! $got_data ) {
			// Where's our bloody data?
			header( "Location: ." );

			exit;
		}
		
		$data->fillGroupInfo($org_name, $group_name);
		
		//$data->dump_cache();
		
		if( $data->getGroupState( $org_name, $group_name ) == GRPSTATE_INACTIVE ) {
			// Group is inactive
			graceful_exit( "Sorry, this group has been locked by an administrator." );
		}
		
		if( $data->getGroupState( $org_name, $group_name ) == GRPSTATE_HIDDEN &&
				! $data->isGroupMember( $data->getUserID(), $org_name, $group_name ) ) {
			// Group is hidden and this user is not a member
			graceful_exit( "Sorry, you may not view the members of this group." );
		}

		$isAdmin = $data->isGroupAdmin( $auth->getUserID(), $org_name, $group_name );
		
		//if($isAdmin) echo 'admin<br>'; else echo 'not admin<br>';

		require_once( 'template/header.php' );

		?>

		<?php if( $isAdmin ) { ?><form method="POST" action="groups.php?action=membersadmin"><?php } ?>
		<table>
			<tr>
				<th<?php if( $isAdmin ) echo " colspan=\"2\""; ?>>
					<?php echo $org_name .".". $group_name; ?>
				</th>
			</tr>

			<?php

			foreach( $data->getGroupMembers( $org_name, $group_name ) as $memberid )
				// need to limit the number of users listed per page here
				// (make variable?)
				echo "<tr>".
						( $isAdmin ? "<td>"."<input type=\"checkbox\" name=\"object_".$memberid."\">" : "" ).
						"<td><a href=\"http://my.bzflag.org/bb/profile.php?mode=viewprofile&u=".
								$memberid."\" target=\"blank\">".
								$data->getUsername( $memberid )."</a></td>".
						"</tr>";

			?>

			<?php if( $isAdmin ) echo "<tr><td><a href=\"groups.php?action=addmember\">".
					"<img src=\"template/img/new.png\"></a></td>".
					"<td>New member</td></tr>\n"; ?>

		</table>

		<?php

		if( $isAdmin ) {
			?>

			<br>

			With selected:&nbsp;
			<select name="manipulate">
				<option selected="selected" onClick="document.getElementById('object_destination').style.visibility = 'hidden';
						document.getElementById('object_destination').style.display = 'none';">No action</option>
				<option value="delete" onClick="document.getElementById('object_destination').style.visibility = 'hidden';
						document.getElementById('object_destination').style.display = 'none';">Delete</option>
				<option value="move" onClick="document.getElementById('object_destination').style.visibility = 'visible';
						document.getElementById('object_destination').style.display = 'inline';">Move</option>
				<option value="copy" onClick="document.getElementById('object_destination').style.visibility = 'visible';
						document.getElementById('object_destination').style.display = 'inline';">Copy</option>
			</select>

			<div id="object_destination" style="visibility: hidden; display: none">

				to&nbsp;

				<select name="destination">
				<?php
				// Output names of possible destination groups as options
				foreach($data->getGroupsAdministratedBy( $auth->getUserID() ) as $org => $groups)
					foreach(array_keys($groups) as $group)
						echo "<option>".$org.".".$group."</option>\n";

				?>
			
			</select>

			</div>

			<input type="submit" value="Go">
		</form>

		<form method="GET action="groups.php?action=<?php
				if( $data->isGroupMember( $auth->getUserID(), $org_name, $group_name ) ) echo "leavegroup"; else echo "joingroup";
				?>">
			<input type="submit" value="<?php
				if( $data->isGroupMember( $auth->getUserID(), $org_name, $group_name ) ) echo "Leave group"; else echo "Join group";
				?>">
		</form>

			<?php
		}

		require_once( 'template/footer.php' );
		
		break;
	case "detail":
		// Editing new or existing organization

		// Verify auth
		if( $got_data && ! $data->isOrgAdmin( $auth->getUserID(), $org_name, $group_name ) )
			header( "Location: ." );

		require_once( 'template/header.php' );

		?>

<form method="POST" action="orgs.php?action=update">
	<?php if( $got_data ) echo "<input type=\"hidden\" name=\"id\" value=\"".$org_name.'.'.$group_name."\">"; ?>
	<table>
		<tr>
			<td>Organization name:</td>
			<td><input type="text" name="orgname" value="<?php
if( $got_data ) echo $org_name;
					?>" size="20"></td>
		</tr>
		<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="<?php
if( $got_data ) echo "Update"; else echo "Create";
				?>"></td></tr>
	</table>
</form>

<i>Note: The organization name must not be the username of an existing user.</i>

<?php

		require_once( 'template/footer.php' );

		break;
	case "update":
		// FIXME validate data entry
		// FIXME check against user data
		// FIXME block against reserved group names

		// check for existing group name
		// FIXME create clean sitewide die method?

		// Various checks
		if( ! $_POST['orgname'] ) {
			// Where's our bloody data?
			header( "Location: ." );

			exit;
		} else if( $_POST['id'] && ! $data->isOrgAdmin( $auth->getUserID(), $_POST['id'] ) )
			// User is not an admin of this org
			graceful_exit( "Sorry, you are not authorized to administer that organization." );
		else if( $data->getUserID( $_POST['orgname'] ) &&
				strtolower( $auth->getUserName() ) != strtolower( $_POST['orgname'] ) )
			// Username equal to specified org name exists and is not this user
			graceful_exit( "Sorry, you cannot name an organization with the username of another user." );
		else if( $data->getOrgID( $_POST['orgname'] ) && 
				$data->getOrgID( $_POST['orgname'] != $_POST['id'] ) )
			// Org name exists and is not this same org
			graceful_exit( "Sorry, an organization with that name already exists." );


		if( $_POST['id'] )
			// Update the organization's name
			$data->setOrgName( $_POST['id'], $_POST['orgname'] );
		else
			// Create the organization
			$data->createOrg( $_POST['orgname'], $auth->getUserID()  );

		header( "Location: ." );

		break;
	default:
 		header( "Location: ." );

		break;
}

?>


<?php
/*

// Action section
switch( $_REQUEST['action'] ) {
	case "members":
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
*/

?>
