<?php

// Includes
require_once( 'include/global.inc' );

// Main action switch
switch( $_GET['action'] ) {
	case "detail":
		// Editing new or existing organization

		// Verify auth
		if( $_GET['id'] && ! $data->isOrgAdmin( $auth->getUserID(), $_GET['id'] ) )
			header( "Location: ." );

		require_once( 'template/header.inc' );

		?>

<form method="POST" action="orgs.php?action=update">
	<?php if( $_GET['id'] ) echo "<input type=\"hidden\" name=\"id\" value=\"".$_GET['id']."\">"; ?>
	<table>
		<tr>
			<td>Organization name:</td>
			<td><input type="text" name="orgname" value="<?php
if( $_GET['id'] ) echo $data->getOrgName( $_GET['id'] );
					?>" size="20"></td>
		</tr>
		<tr><td>&nbsp;</td><td><input type="submit" name="submit" value="<?php
if( $_GET['id'] ) echo "Update"; else echo "Create";
				?>"></td></tr>
	</table>
</form>

<i>Note: The organization name must not be the username of an existing user.</i>

<?php

		require_once( 'template/footer.inc' );

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