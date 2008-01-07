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
		echo "<tr><td>".
				"<a href=\"groups.php?action=members&id=".$groupid."\">".
				$groupname."</td><td>".count( $data->getGroupMembers( $groupid ) ).
				"</a></td></tr>\n";

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
						"<a href=\"orgs.php?action=detail&id=".$orgid."\">".
						"<img src=\"img/wrench.png\">" : "&nbsp;" )."</a></td>".
				"<td colspan=\"2\">&nbsp;</td></tr>\n";

		// Group name and links (if applicable)
		ksort( $group_array );
		foreach( array_keys( $group_array ) as $groupid )
			echo "<tr><td>".
					( $data->isOrgAdminGroup( $groupid, $orgid ) ? "<img src=\"img/key_high.png\">" :
							( $data->isSpecialAdminGroup( $groupid ) ?
									"<img src=\"img/key_low.png\">" : "&nbsp;" ) )."</td>".
					"<td>&nbsp;</td>".
					"<td><a href=\"groups.php?action=members&id=".$groupid."\">".
					$data->getGroupName( $groupid ).
					"</a></td>".
					( $data->isGroupAdmin( $auth->getUserID(), $groupid ) ?
							"<td style=\"padding: 0\">".
							"<a href=\"groups.php?action=detail&id=".$groupid."\">".
							"<img src=\"img/wrench.png\"></a></td>".
							"<td><a href=\"groups.php?action=delete&id=".$groupid."\">".
							"<img src=\"img/delete.png\"></td>" :
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

<br><a href="orgs.php?action=detail">Click here to register a new organization.</a>

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

?>