<?php

// Data layer class. This class provides the site with an interface for
// accessing all data.

// (will fail and return true if the user is the only  admin)

class data {
	private $conn;
	private $tab_prf;
	private $users_table;

	// ============================================================
	// ====================== Initialization ======================
	// ============================================================

	function __construct( $config ) {
		// Connect to the MySQL database
		$this->conn = mysql_connect( $config['host'], $config['user'],
				$config['pass'] );
		if( ! $this->conn )
			return false;
		if( ! mysql_select_db( $config['dbname'], $this->conn ) )
			return false;

		$this->tab_prf = $config['prefix'];
		$this->users_table = $config['users_table'];
	}		

	// ============================================================
	// ====================== User interface ======================
	// ============================================================

	public function getUserID( $username ) {
		$sql = "SELECT user_id FROM ".
				$this->users_table." WHERE username = \"".
				$username."\"";
		$result = mysql_query( $sql, $this->conn );
		if( ! $result )
			return false;

		if( $result && mysql_num_rows( $result ) > 0 )
			return mysql_result( $result, 0 );

		return false;
	}
	public function getUsername( $userid ) {
		$sql = "SELECT username FROM ".$this->users_table.
				" WHERE user_id = ".$userid;
		$result = mysql_query( $sql, $this->conn );
		if( ! $result )
			return false;

		if( $result && mysql_num_rows( $result ) > 0 )
				return mysql_result( $result, 0 );

		return false;
	}
	public function getEncryptedPass( $userid ) {
		$sql = "SELECT user_password FROM ".$this->users_table.
				" WHERE user_id = ".$userid;
		$result = mysql_query( $sql, $this->conn );
		if( ! $result )
			return false;

		if( $result && mysql_num_rows( $result ) > 0 )
				return mysql_result( $result, 0 );

		return false;
	}

	// ============================================================
	// ================== Organization interface ==================
	// ============================================================

	public function getNumOrgs() {
		$sql = "SELECT orgid FROM ".$this->tab_prf."orgs WHERE 1";
		$result = mysql_query( $sql, $this->conn );
		if( $result )
			return mysql_num_rows( $result );

		return false;
	}
	public function getOrgName( $orgid ) {
		$sql = "SELECT orgname FROM ".$this->tab_prf."orgs WHERE ".
				"orgid = ".$orgid;
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 ) 
			return mysql_result( $result, 0);

		return false;
	}
	public function getOrgGroups( $id ) {
		;
	}

	// Function to create an organization by name
	public function createOrg( $name, $userid ) {
		// Don't create duplicate orgs
		$sql = "SELECT orgid FROM ".$this->tab_prf."orgs WHERE ".
				"orgname = \"".$orgname."\"";
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 )
			return false;

		// Create the organization
		$sql = "INSERT INTO ".$this->tab_prf."orgs (orgname,contact) ".
				"VALUES (\"".$name."\",".$userid.")";
		mysql_query( $sql, $this->conn );

		$sql = "SELECT MAX(orgid) FROM ".$this->tab_prf."orgs";
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 )
			$myOrgID = mysql_result( $result, 0);
		if( ! $myOrgID )
			return false;

		// Create the owner group
		$ownerGroup = $this->createGroup( "owner",
				"Owner group for the ".
				$this->getOrgName( $myOrgID ).
						" organization.", $myOrgID,
						ADMINLVL_SUPER );
		if( ! $ownerGroup )
			return false;

		// Add the registrant to the owner group
		$this->addMember( $userid, $ownerGroup );

		return $myOrgID;
	}

	// ============================================================
	// ===================== Group interface ======================
	// ============================================================

	// Info functions
	public function getNumGroups() {
		$sql = "SELECT groupid FROM ".$this->tab_prf."groups WHERE 1";
		$result = mysql_query( $sql, $this->conn );
		if( $result )
			return mysql_num_rows( $result );

		return false;
	}

	public function getGroupName( $id ) {
		$sql = "SELECT groupname FROM ".$this->tab_prf."groups WHERE ".
				"groupid = ".$groupid;
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 ) 
			return mysql_result( $result, 0);

		return false;
	}
	public function getGroupDesc( $groupid ) {
		$sql = "SELECT description FROM ".$this->tab_prf.
				"groups WHERE "."groupid = ".$groupid;
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 ) 
			return mysql_result( $result, 0);

		return false;
	}
	public function getGroupOrg( $groupid ) {
		$sql = "SELECT orgid FROM ".$this->tab_prf.
				"groups WHERE groupid=".$groupid;
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 )
			return mysql_result( $result, 0);

		return false;
	}
	public function getGroupAdminLevel( $groupid ) {
		$sql = "SELECT adminlevel FROM ".$this->tab_prf.
				"groups WHERE groupid=".$groupid;
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 )
			return mysql_result( $result, 0);

		return false;
	}
	public function getGroupMembers( $groupid ) {
		$members = array();

		$sql = "SELECT userid FROM ".$this->tab_prf.
				"memberships WHERE groupid=".$groupid;
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 )
			while( $result_array = mysql_fetch_array( $result ) )
				array_push( $members, $result_array['userid'] );
		return $members;
	}
	// Manipulation functions
	public function createGroup( $groupname, $desc, $orgid,
			$adminlevel = 0 ) {
		// Don't create duplicate groups
		$sql = "SELECT groupid FROM groups WHERE ".
				"groupname=\"".$groupname."\" AND ".
				"orgid=".$orgid;
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 )
			return false;

		$sql = "INSERT INTO groups (groupname, description, orgid) ".
				"VALUES (\"".$groupname."\", \"".$desc.
				"\", ".$orgid.")";
		mysql_query( $sql, $this->conn );

		$sql = "SELECT groupid FROM groups WHERE ".
				"orgid=".$orgid." AND ".
				"groupname=\"".$groupname."\"";
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 )
			return(  mysql_result( $result, 0 ) );

		return false;
	}
	public function updateGroup( $groupid, $groupname, $desc, $orgid ) {
		$sql = "SELECT groupid FROM groups WHERE ".
				"groupid=".$groupid;
		$result = mysql_query( $sql, $this->conn );
		if( ! $result || mysql_num_rows( $result ) < 1 )
			return false;

		$sql = "UPDATE groups SET".
				( $groupname ? " groupname=\"".$groupname."\"," : "" ).
				( $description ? " description=\"".$description."\"," : "" ).
				( $orgid ? " orgid=".$orgid."," : "" ).
				"WHERE groupid=".$groupid;
		$sql = preg_replace( "/\s*\,\s*/", "//", $sql );
		mysql_query( $sql, $this->conn );

		return true;
	}
	public function addMember( $groupid, $userid ) {
		$sql = "INSERT INTO ".$this->tbl_prf."memberships ".
				"(userid, groupid) VALUES ".
				"(".$userid.", ".$groupid.")";
		mysql_query( $sql );

		$sql = "SELECT MAX(id) FROM ".$this->tab_prf."memberships";
		$result = mysql_query( $sql );
		if( $result && mysql_num_rows( $result ) > 0 )
			return mysql_result( $result, 0 );

		return false;
	}
	public function deleteMember( $groupid, $userid ) {
		$sql = "DELETE FROM ".$this->tbl_prf."memberships ".
			"WHERE userid = ".$userid." AND groupid = ".$groupid;
		mysql_query( $sql );
	}
	public function getGroupIDs( $userid ) {
		$toReturn = array();

		$sql = "SELECT groupid FROM group_members WHERE userid=".
				$id;
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 ) {
			while( $result_array = mysql_fetch_array( $result ) ) {
				array_push( $toReturn,
						$result_array['groupid'] );
			}
		}

		return $toReturn;
	}

}

?>
