<?php

// Includes
require_once( 'include/global.php' );

// Page content
require_once( 'template/header.php' );

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
// ==================== My memberships & my groups ====================

if( $auth->isLoggedIn() ) {

	$data->fillAllUserInfo($auth->getUserID());
	?>

	<p>

	<table>
		<tr>
			<td><span class="large">My Memberships</span></td>
			<td><span class="large">My Groups</span></td>
		</tr>
		<tr>
			<td style="vertical-align: top; padding-right: 20px">

				<?php

				$group_array = $data->getGroupsByUser( $auth->getUserID() );
				if( count( $group_array ) == 0 )
					echo "You are not a member of any groups at this time.<br>\n";
				else {
					echo "<table>\n";
					echo "<tr><th>Group</th><th>Members</th></tr>\n";
								
					asort( $group_array );

					foreach( $group_array as $ou_grp )
						echo "<tr><td>".
							"<a href=\"groups.php?action=members&ou=".
							$ou_grp[0]."&grp=".$ou_grp[1]."\">".
							$ou_grp[0].".".$ou_grp[1]."</td><td>".
									$data->getGroupMemberCount( $ou_grp[0], $ou_grp[1] ).
							"</a></td></tr>\n";

					echo "</table>\n";
				}

				?>

			</td>
			<td style="vertical-align: top; padding-left: 20px">

				<?php

				// Generate array of orgid's[groupid's]
				$org_group_arr = $data->getGroupsAdministratedBy( $auth->getUserID() );
					
				if( count( $org_group_arr ) == 0 )
					echo "You do not administer any groups at this time.<br>\n";
				else {
					echo "<table>\n";
					echo "<tr><th colspan=\"3\">Name</th><th colspan=\"2\">&nbsp;</th></tr>\n";

					foreach( $org_group_arr as $org => $group_array ) {
						// Org info
						echo "<tr class=\"org\"><td colspan=\"3\">".
								$org."</td>".
								"<td>".( $data->isOrgAdmin(
										$auth->getUserID(), $org ) ?
										"<a href=\"orgs.php?action=detail&ou=".
										$org."\">".
										"<img src=\"template/img/wrench.png\">" :
										"&nbsp;" )."</a></td>".
										"<td colspan=\"2\">&nbsp;</td></tr>\n";

						// Group name and links (if applicable)
						foreach( array_keys($group_array) as $group )
							echo "<tr><td>".
									( $data->isOrgAdminGroup( $org, $group ) ?
											"<img src=\"template/img/key_high.png\">" :
									( $data->isSpecialAdminGroup( $org, $group ) ?
											"<img src=\"template/img/key_low.png\">" :
											"&nbsp;" ) )."</td>".
									"<td>&nbsp;</td>".
									"<td><a href=\"groups.php?action=members&ou=".$org.
									"&grp=".$group."\">".$group.
									"</a></td>".
									//( $data->isGroupAdmin( $auth->getUserID(), $group ) ?
											"<td style=\"padding: 0\">".
											"<a href=\"groups.php?action=detail&ou=".$org.
											"&grp=".$group."\">".
											"<img src=\"template/img/wrench.png\"></a></td>".
											"<td><a href=\"groups.php?action=delete&grp=".
											"&ou=".$org."&grp=".$group."\">".
											"<img src=\"template/img/delete.png\"></td>"// :
									//		"<td>&nbsp;</td><td>&nbsp;</td>" ).
											. "</tr>\n";

						// Final row for creating a new group
						if( $data->isOrgAdmin( $auth->getUserID(), $org ) )
							echo "<tr>".
									"<td>&nbsp;</td><td>&nbsp;</td>".
									"<td>New Group</td>".
									"<td><a href=\"groups.php?action=detail\">".
									"<img src=\"template/img/new.png\"></a></td>".
									"<td>&nbsp;</td></tr>\n";
					}

					echo "</table>\n";
				}				



				?>

				<br><a href="orgs.php?action=detail">Click here to register a new organization.</a>

			</td>
		</tr>
	</table>

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

require_once( 'template/footer.php' );

?>
