<?php

// Data layer class. This class provides the site with an interface for
// accessing all data.

// (will fail and return true if the user is the only  admin)

class data {
	private $conn;
	private $tbl_prf;
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

		$this->tbl_prf = $config['prefix'];
		$this->users_table = $config['users_table'];
	}		

	// ============================================================
	// ================== Permissions interface ===================
	// ============================================================

	// Organization permission functions
	public function isOrgAdminGroup( $groupid, $orgid = 0 ) {
		if( $this->getOrg( $groupid ) == $orgid || ! $orgid ) {
			$sql = "SELECT id FROM ".$this->tbl_prf.
					"permissions WHERE ".
					"groupid=".$groupid." AND ".
					"orgadmin = 1";
			$result = mysql_query( $sql, $this->conn );
			if( $result && mysql_num_rows( $result ) > 0 )
				return true;
		}

		return false;
	}
	public function setOrgAdminGroup( $groupid, $orgid ) {
		if( $this->isOrgAdminGroup( $groupid, $orgid ) )
			return;

		$sql = "INSERT INTO ".$this->tbl_prf."permissions ".
				"(groupid, orgadmin) VALUES ".
				"(".$groupid.", 1)";
		mysql_query( $sql, $this->conn );
	}
	public function unsetOrgAdminGroup( $groupid, $orgid ) {
		$sql = "DELETE FROM ".$this->tbl_prf."permissions WHERE ".
				"groupid = ".$groupid." AND ".
				"orgadmin = 1";
		mysql_query( $sql, $this->conn );
	}

	// Special group permission functions
	public function isSpecialAdminGroup( $groupid, $targetid = 0 ) {
		// If $targetid is unspecified, see if it's a special
		// admin group at all
		$sql = "SELECT id FROM ".$this->tbl_prf.
				"permissions WHERE ".
				"groupid=".$groupid." AND ".
				( $targetid ? "grouptarget = ".$targetid." AND " : "").
				"orgadmin = 0 AND groupadmin = 1";
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 )
			return true;

		return false;
	}
	public function setSpecialAdminGroup( $groupid, $targetid ) {
		if( $this->isSpecialAdminGroup( $groupid, $targetid ) )
			return;

		$sql = "INSERT INTO ".$this->tbl_prf."permissions ".
				"(groupid, group_target) VALUES ".
				"(".$groupid.", ".$targetid.")";
		mysql_query( $sql, $this->conn );
	}
	public function unsetSpecialAdminGroup( $groupid, $targetid ) {
		$sql = "DELETE FROM ".$this->tbl_prf."permissions WHERE ".
				"groupid = ".$groupid." AND ".
				"grouptarget = ".$targetid;
		mysql_query( $sql, $this->conn );
	}

	// Utility permission functions
	public function isOrgAdmin( $userid, $orgid ) {
		$groups = $this->getGroupsByUser( $userid );

		if( ! $groups )
			return false;

		foreach( $groups as $group )
			if( $this->isOrgAdminGroup( $group, $orgid ) )
				return true;

		return false;
	}
	public function isGroupAdmin( $userid, $groupid ) {
		$groups = $this->getGroupsByUser( $userid );

		if( ! $groups )
			return false;

		if( $this->isOrgAdmin( $userid, $this->getOrg( $groupid ) ) )
			return true;

		foreach( $groups as $group )
			if( $this->isSpecialAdminGroup( $group, $groupid ) )
				return true;

		return false;
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
		$sql = "SELECT orgid FROM ".$this->tbl_prf."orgs WHERE 1";
		$result = mysql_query( $sql, $this->conn );
		if( $result )
			return mysql_num_rows( $result );

		return false;
	}
	public function getOrgID( $orgname ) {
		// This should be the only function that deals with
		// organizations by name
		$sql = "SELECT orgid FROM ".$this->tbl_prf."orgs WHERE ".
				"orgname = \"".$orgname."\"";
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 )
			return true;

		return false;
	}

	public function getOrgName( $orgid ) {
		$sql = "SELECT orgname FROM ".$this->tbl_prf."orgs WHERE ".
				"orgid = ".$orgid;
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 ) 
			return mysql_result( $result, 0);

		return false;
	}
	public function getOrgGroups( $orgid ) {
		$groups = array();

		$sql = "SELECT groupid FROM ".$this->tbl_prf."groups WHERE orgid = ".$orgid;
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 )
			while( $result_array = mysql_fetch_array( $result ) )
				array_push( $groups, $result_array['groupid'] );
		return $groups;
	}
	public function getOrg( $groupid ) {
		$sql = "SELECT orgid FROM ".$this->tbl_prf."groups WHERE groupid = ".$groupid;
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 ) 
			return mysql_result( $result, 0);

		return false;
	}

	// Manipulation functions
	public function createOrg( $name, $userid ) {
		// Don't create duplicate groups
		if( getOrgID( $name ) )
			return false;

		// Create the organization
		$sql = "INSERT INTO ".$this->tbl_prf."orgs (orgname,contact) ".
				"VALUES (\"".$name."\",".$userid.")";
		mysql_query( $sql, $this->conn );

		$sql = "SELECT MAX(orgid) FROM ".$this->tbl_prf."orgs";
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 )
			$myOrgID = mysql_result( $result, 0);
		if( ! $myOrgID )
			return false;

		// Create the owner group
		$ownerGroup = $this->createGroup( "moderator",
				"Owner group for the ".
				$this->getOrgName( $myOrgID ).
						" organization.", $myOrgID );
		if( ! $ownerGroup )
			return false;

		// Set the owner group to have admin perms
		setOrgAdminGroup( $adminGroup, $orgid );

		// Add the registrant to the owner group
		$this->addMember( $userid, $ownerGroup );

		return $myOrgID;
	}

	// ============================================================
	// ===================== Group interface ======================
	// ============================================================

	// Info functions
	public function getNumGroups() {
		$sql = "SELECT groupid FROM ".$this->tbl_prf."groups WHERE 1";
		$result = mysql_query( $sql, $this->conn );
		if( $result )
			return mysql_num_rows( $result );

		return false;
	}

	public function getGroupName( $groupid ) {
		$sql = "SELECT groupname FROM ".$this->tbl_prf."groups WHERE ".
				"groupid = ".$groupid;
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 ) 
			return mysql_result( $result, 0);

		return false;
	}
	public function getGroupDesc( $groupid ) {
		$sql = "SELECT description FROM ".$this->tbl_prf.
				"groups WHERE "."groupid = ".$groupid;
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 ) 
			return mysql_result( $result, 0);

		return false;
	}
	public function getGroupOrg( $groupid ) {
		$sql = "SELECT orgid FROM ".$this->tbl_prf.
				"groups WHERE groupid=".$groupid;
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 )
			return mysql_result( $result, 0);

		return false;
	}
	public function getGroupMembers( $groupid ) {
		$members = array();

		$sql = "SELECT userid FROM ".$this->tbl_prf.
				"memberships WHERE groupid=".$groupid;
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 )
			while( $result_array = mysql_fetch_array( $result ) )
				array_push( $members, $result_array['userid'] );
		return $members;
	}
	public function getGroupsByUser( $userid ) {
		if( ! $userid )
			return false;

		$toReturn = array();
		$sql = "SELECT groupid FROM ".$this->tbl_prf."memberships WHERE userid=".$userid;

		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 ) {
			while( $result_array = mysql_fetch_array( $result ) ) {
				array_push( $toReturn,
						$result_array['groupid'] );
			}
		}

		return $toReturn;
	}

	// Manipulation functions
	public function createGroup( $groupname, $desc, $orgid ) {
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
		mysql_query( $sql, $this->conn );

		$sql = "SELECT MAX(id) FROM ".$this->tbl_prf."memberships";
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 )
			return mysql_result( $result, 0 );

		return false;
	}
	public function deleteMember( $groupid, $userid ) {
		if( ! $groupid || ! $userid )
			return false;

		$sql = "DELETE FROM ".$this->tbl_prf."memberships ".
			"WHERE userid = ".$userid." AND groupid = ".$groupid;
		mysql_query( $sql, $this->conn );
	}
}

?>
