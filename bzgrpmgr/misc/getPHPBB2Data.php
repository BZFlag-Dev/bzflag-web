<?
// This script converts and copies data from phpBB2 tables to the new
// MySQL database storage method for the BZFlag Group Management System.

foreach( $argv as $line ) echo $line."\n";

// DB args from command line
if( count( $argv ) < 3 )
	die( "Please supply the source and destination database names.\n" );

if( count( $argv ) == 4 )
	$dbuser = $argv[3];
else {
	echo "Please enter the MySQL username:\n";
	$dbuser = trim(fgets(STDIN));
}
echo "Please enter the MySQL password:\n";
$dbpass = trim(fgets(STDIN));
$conn = mysql_connect( "localhost", $dbuser, $dbpass ) or
	die( "Could not connect to MySQL.\n" );

mysql_select_db( $argv[1], $conn ) or
	die( "Could not select source database.\n" );

// Go through all the table names and parse them out into orgs and groups.
$sql = "SELECT * FROM phpbb_groups WHERE group_name != \"\" ";	//LIMIT 30";
$result = mysql_query( $sql );
echo "There are ".mysql_num_rows( $result )." groups in this database.\n";

// Define the classes that we'll use now
class membership {
	public $pending;
}
class group {
	public $name;
	public $moderator;
	public $description;
	public $type;
	public $members;
}

$orgs = array();

// Compile dah big array
while( $result && $RA = mysql_fetch_array( $result ) ) {
	// explode the old group name
	$entry = explode( ".", $RA['group_name'] );
	if( count( $entry ) < 2 ) {
		// not in standard org.group form
		$orgname = "misfits";
		$groupname = $RA['group_name'];
	} else {
		$orgname = $entry[0];
		$groupname = $entry[1];
	}

	// If there are groups with the same prefix in different cases,
	// they go in the same org, so use the one that's already there.
	foreach( array_keys( $orgs ) as $oldorg ) {
		if( strtoupper( $oldorg ) == strtoupper( $orgname ) ) {
			$orgname = $oldorg;

			break;
		}
	}

	$orgs[$orgname][$RA['group_name']] = new group();
	$orgs[$orgname][$RA['group_name']]->name = $groupname;
	$orgs[$orgname][$RA['group_name']]->moderator = $RA['group_moderator'];
	$orgs[$orgname][$RA['group_name']]->description = $RA['group_description'];
	$orgs[$orgname][$RA['group_name']]->type = $RA['group_type'];
	$orgs[$orgname][$RA['group_name']]->members = array();

	// Get the members
	$sql = "SELECT * FROM phpbb_user_group WHERE group_id = ".$RA['group_id'];
	$subresult = mysql_query( $sql );

	while( $subresult && $subRA = mysql_fetch_array( $subresult ) )
		$orgs[$orgname][$RA['group_name']]->members[$subRA['user_id']]->pending = $subRA['user_pending'];
}

// Put it all into the new database
mysql_select_db( $argv[2], $conn ) or
	die( "Could not select destination database.\n" );
foreach( $orgs as $orgname => $groups ) {
	// Assume the moderator of the first group to be the contact
	$sql = "INSERT INTO groups_orgs (orgname, contact) VALUES (\"".
			$orgname."\", ".current( $groups )->moderator.")";
	mysql_query( $sql );

	// Get the current orgid
	$sql = "SELECT MAX(orgid) FROM groups_orgs";
	$result = mysql_query( $sql );
	if( $result && mysql_num_rows( $result ) > 0 )
		$orgid = mysql_result( $result, 0 );
	else
		die( "Could not get the current orgid.\n" );

	// Create the moderators group
	$sql = "INSERT INTO groups_groups (groupname, orgid) VALUES (".
			"\"moderators\", ".$orgid.")";
	mysql_query( $sql );

	// Get the groupid of the moderators group
	$sql = "SELECT MAX(groupid) FROM groups_groups";
	$result = mysql_query( $sql );
	if( $result && mysql_num_rows( $result ) > 0 )
		$groupid = mysql_result( $result, 0 );
	else
		die( "Could not get the groupid for the moderator group.\n" );

	// Set the moderator group to have access
	$sql = "INSERT INTO groups_permissions (groupid, orgadmin) VALUES (".$groupid.", 1)";
	mysql_query( $sql );

	// Insert all moderators
	$mods = array();
	foreach( $groups as $groupobj )
		$mods[$groupobj->moderator] = 1;
	foreach( array_keys( $mods ) as $userid ) {
		$sql = "INSERT INTO groups_memberships (userid, groupid) VALUES (".
				$userid.", ".$groupid.")";
		mysql_query( $sql );
	}

	// Create the member groups
	foreach( $groups as $groupobj ) {
		$sql = "INSERT INTO groups_groups (groupname, orgid, state, description) VALUES (\"".
				$groupobj->name."\", ".$orgid.", \"".
				( $groupobj->type == 0 ? "open" : "" ).
				( $groupobj->type == 1 ? "closed" : "" ).
				( $groupobj->type == 2 ? "hidden" : "" ).
				"\", \"".$groupobj->description."\")";
		mysql_query( $sql );

		$sql = "SELECT MAX(groupid) FROM groups_groups";
		$result = mysql_query( $sql );
		if( $result && mysql_num_rows( $result ) > 0 )
			$groupid = mysql_result( $result, 0 );
		else
			die( "Could not get the current groupid.\n" );

		// Insert the members
		foreach( $groupobj->members as $userid => $membershipObj ) {
			$sql = "INSERT INTO groups_memberships (userid, groupid, pending) VALUES (".
					$userid.", ".$groupid.", ".$membershipObj->pending.")";
			mysql_query( $sql );
		}
	}
}

// Output it
/*
foreach( $orgs as $orgname => $groups ) {
	echo $orgname."\n";

	foreach( $groups as $groupobj ) {
		echo "\t.".$groupobj->name." ".$groupid."\n";

		foreach( $groupobj->members as $member ) {
			echo "\t\t".$member." -> ";

			$sql = "SELECT username from phpbb_users WHERE user_id = ".$member;

			$result = mysql_query( $sql );
			if( $result && mysql_num_rows( $result ) > 0 )
				echo mysql_result( $result, 0 );

			echo "\n";
		}
	}
}
*/

exit;