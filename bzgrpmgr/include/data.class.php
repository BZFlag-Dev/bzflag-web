<?php

// Data layer class. This class provides the site with an interface for
// accessing all data.

class data {
	private $conn, $tbl_prf, $dbname, $bbdbname;

	// ============================================================
	// ====================== Initialization ======================
	// ============================================================

	function __construct( $config ) {
		
	}

	// ============================================================
	// ================== Permissions interface ===================
	// ============================================================
	
	// cache all information retrieved from the daemon in these vars because they may be used many times on a single page
	private $group_info, $member_info, $org_info;
	
	public function fillAllUserInfo($uid) {
		global $userstore;
		$this->group_info = array(); $this->member_info = array(); $this->org_info = array();
		
		// get info for the user's group memberships
		foreach($userstore->getMemberInfo($uid) as $memberInfo)
			$this->set_member_info($uid, $memberInfo['ou'], $memberInfo['grp'], $memberInfo['perms']);
			
		//$this->dump_cache();
			
		 // get info for the groups the user is member of
		foreach($userstore->getGroupInfo($this->getGroupsByUser($uid)) as $groupInfo)
			$this->set_group_info($groupInfo['ou'], $groupInfo['grp'], $groupInfo['state'], $groupInfo['perms']);
		
		//$this->dump_cache();
		
		// get info for the orgs the user is the owner of  (except those specified as permissions)
		foreach($userstore->getOrgsOwnedBy($uid) as $org)
			$this->set_org_info($org, $uid, null);
			
		//$this->dump_cache();
		
		// get all groups for all orgs the user is a member of, including those specified in permissions
		$this->fillOrgGroups($this->getOrgsOwnedBy($uid));
	}
	
	public function dump_cache() {
		echo 'group_info: '; var_dump($this->group_info); echo '<br>';
		echo 'member_info: '; var_dump($this->member_info); echo '<br>';
		echo 'org_info: '; var_dump($this->org_info); echo '<br>';
	}
	
	public function fillOrgGroups($org_array) {
		global $userstore;
		foreach($userstore->getOrgGroups($org_array) as $org => $group)
			init_group_info($org, $group, false);
	}
	
	private function init_org_info($ou) {
		if(!isset($this->org_info[$ou])) $this->org_info[$ou] = array( 'owner' => null, 'contact' => null );
		else {
			if(!isset($this->org_info[$ou]['owner'])) $this->org_info[$ou]['owner'] = null;
			if(!isset($this->org_info[$ou]['contact'])) $this->org_info[$ou]['contact'] = null;
		}
	}
	
	private function init_group_info($ou, $grp, $set_null_info = true) {
		if(!isset($this->group_info[$ou])) $this->group_info[$ou] = array( $grp => array( 'state' => null, 'perms' => array() ) );
		else if(!isset($this->group_info[$ou][$grp])) $this->group_info[$ou][$grp] = array( 'state' => null, 'perms' => array() );
		else if($set_null_info) {
			if(!isset($this->group_info[$ou][$grp]['perms'])) $this->group_info[$ou][$grp]['perms'] = array();
			if(!isset($this->group_info[$ou][$grp]['state'])) $this->group_info[$ou][$grp]['state'] = null;
		}
	}
	
	private function init_member_info($uid, $ou, $grp) {
		if(!isset($this->member_info[$uid])) $this->member_info[$uid] = array( $ou => array( $grp => array( 'perms' => array() ) ) );
		else if(!isset($this->member_info[$uid][$ou])) $this->member_info[$uid][$ou] = array( $grp => array( 'perms' => array() ) );
		else if(!isset($this->member_info[$uid][$ou][$grp])) $this->member_info[$uid][$ou][$grp] = array( 'perms' => array() );
		else {
			if(!isset($this->member_info[$uid][$ou][$grp]['perms'])) $this->member_info[$uid][$ou][$grp]['perms'] = array();
		}
	}
	
	public function set_member_info($uid, $ou, $grp, $perms) {
		$this->init_member_info($uid, $ou, $grp);
		$this->member_info[$uid][$ou][$grp]['perms'] = $perms;
	}
	
	public function set_org_info($ou, $owner, $contact) {
		$this->init_org_info($ou);
		$this->org_info[$ou]['owner'] = $owner;
		$this->org_info[$ou]['contact'] = $contact;
	}
	
	public function set_group_info($ou, $grp, $state, $perms) {
		$this->init_group_info($ou, $grp);
		$this->group_info[$ou][$grp]['state'] = $state;
		$this->group_info[$ou][$grp]['perms'] = $perms;
	}
	
	public function get_memberInfo_byUid($uid) {
		if(isset($this->member_info) && isset($this->member_info[$uid]))
			return $this->member_info[$uid];
		else
			return false; // TODO fetch from the daemon
	}
	
	public function get_memberInfo_byUidOrg($uid, $org) {
		if(isset($this->member_info) && isset($this->member_info[$uid]) &&
			isset($this->member_info[$uid][$org]))
			return $this->member_info[$uid][$org];
		else
			return false; // TODO fetch from the daemon
	}
	
	public function get_orgInfo($org) {
		if(isset($this->org_info) && isset($this->org_info[$org]))
			return $this->org_info[$org];
		else
			return false; // TODO fetch from the daemon
	}
	
	private function get_groupInfo_byOrg($org) {
		if(isset($this->group_info) && isset($this->group_info[$org]))
			return $this->group_info[$org];
		else
			return array(); // TODO fetch from the daemon
	}
	
	private function get_groupInfo($ou, $grp) {
		if(isset($this->group_info) && isset($this->group_info[$ou]) && 
			isset($this->group_info[$ou][$grp]))
			return $this->group_info[$ou][$grp];
		else
			return array(); // TODO fetch from the daemon
	}
	
	private function find_perm($perm_id, $in_perms) {
		foreach($in_perms as $perm)
			if($perm[0] == $perm_id)
				return $perm;
		return false;
	}

	// Organization permission functions
	public function isOrgAdminGroup( $org, $group ) {
		if($info = $this->get_groupInfo($org, $group))
			foreach($info['perms'] as $perm)
				if($perm[0] == PERM_ORG_ADMIN)
					return true;

		return false;
	}
	
	public function setOrgAdminGroup( $groupid, $orgid ) {
		if( $this->isOrgAdminGroup( $groupid, $orgid ) )
			return;

		$sql = "INSERT INTO ".$this->tbl_prf."permissions ".
				"(groupid, orgadmin) VALUES ".
				"(".$groupid.", 1)";
		mysql_select_db( $this->dbname );
		mysql_query( $sql, $this->conn );
	}
	
	public function unsetOrgAdminGroup( $groupid, $orgid ) {
		$sql = "DELETE FROM ".$this->tbl_prf."permissions WHERE ".
				"groupid = ".$groupid." AND ".
				"orgadmin = 1";
		mysql_select_db( $this->dbname );
		mysql_query( $sql, $this->conn );
	}

	// Special group permission functions
	public function isSpecialAdminGroup( $org, $group ) {
		if($info = $this->get_groupInfo($org, $group))
			foreach($info['perms'] as $perm)
				if($perm[0] == PERM_ADMIN_OF)
					return true;

		return false;
	}
	
	public function setSpecialAdminGroup( $groupid, $targetid ) {
		if( $this->isSpecialAdminGroup( $groupid, $targetid ) )
			return;

		$sql = "INSERT INTO ".$this->tbl_prf."permissions ".
				"(groupid, group_target) VALUES ".
				"(".$groupid.", ".$targetid.")";
		mysql_select_db( $this->dbname );
		mysql_query( $sql, $this->conn );
	}
	
	public function unsetSpecialAdminGroup( $groupid, $targetid ) {
		$sql = "DELETE FROM ".$this->tbl_prf."permissions WHERE ".
				"groupid = ".$groupid." AND ".
				"grouptarget = ".$targetid;
		mysql_select_db( $this->dbname );
		mysql_query( $sql, $this->conn );
	}

	// Utility permission functions
	public function isOrgAdmin( $uid, $org ) {
		if($orgInfo = $this->get_orgInfo($org) && $orgInfo['owner'] == $uid)
			return true;
		if($memberGroups = $this->get_memberInfo_byUidOrg($uid, $org))
			foreach($memberGroups as $group => $memberInfo) {
				if($this->find_perm(PERM_ORG_ADMIN, $memberInfo['perms']))
					return true;
				if($groupInfo = $this->get_groupInfo($org, $group))
					if($this->find_perm(PERM_ORG_ADMIN, $groupInfo['perms']))
						return true;
			}
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
				"phpbb_users WHERE username = \"".
				$username."\"";
		mysql_select_db( $this->bbdbname );
		$result = mysql_query( $sql, $this->conn );
		if( ! $result )
			return false;

		if( $result && mysql_num_rows( $result ) > 0 )
			return mysql_result( $result, 0 );

		return false;
	}
	
	public function getUsername( $userid ) {
		$sql = "SELECT username FROM phpbb_users ".
				"WHERE user_id = ".$userid;
		mysql_select_db( $this->bbdbname );
		$result = mysql_query( $sql, $this->conn );
		if( ! $result )
			return false;

		if( $result && mysql_num_rows( $result ) > 0 )
				return mysql_result( $result, 0 );

		return false;
	}
	
	public function getEncryptedPass( $userid ) {
		$sql = "SELECT user_password FROM phpbb_users ".
				"WHERE user_id = ".$userid;
		mysql_select_db( $this->bbdbname );
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
		mysql_select_db( $this->dbname );
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
		mysql_select_db( $this->dbname );
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 )
			return true;

		return false;
	}

	public function getOrgName( $orgid ) {
		$sql = "SELECT orgname FROM ".$this->tbl_prf."orgs WHERE ".
				"orgid = ".$orgid;
		mysql_select_db( $this->dbname );
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 ) 
			return mysql_result( $result, 0);

		return false;
	}
	
	public function getOrgGroups( $org ) {
		$ret = array();
		if(!isset($this->group_info) || !isset($this->group_info[$org])) {
			var_dump(debug_backtrace());
			echo "org=".$org;
		}
		
		foreach($this->group_info[$org] as $group => $groupinfo)
			$ret[] = $group;
		return $ret;
	}
	
	public function getOrg( $groupid ) {
		$sql = "SELECT orgid FROM ".$this->tbl_prf."groups WHERE groupid = ".$groupid;
		mysql_select_db( $this->dbname );
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 ) 
			return mysql_result( $result, 0);

		return false;
	}

	// Manipulation functions
	public function createOrg( $name, $userid ) {
		// Don't create duplicate groups
		if( $this->getOrgID( $name ) )
			return false;

		// Create the organization
		$sql = "INSERT INTO ".$this->tbl_prf."orgs (orgname,contact) ".
				"VALUES (\"".$name."\",".$userid.")";
		mysql_select_db( $this->dbname );
		mysql_query( $sql, $this->conn );

		$sql = "SELECT MAX(orgid) FROM ".$this->tbl_prf."orgs";
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 )
			$myOrgID = mysql_result( $result, 0);
			
		if( ! $myOrgID )
			return false;

		// Create the owner group
		$ownerGroup = $this->createGroup( "moderators",
				"Owner group for the ".
				$this->getOrgName( $myOrgID ).
						" organization.", $myOrgID );
		if( ! $ownerGroup )
			return false;

		// Set the owner group to have admin perms
		$this->setOrgAdminGroup( $ownerGroup, $orgid );

		// Add the registrant to the owner group
		$this->addMember( $userid, $ownerGroup );

		return $myOrgID;
	}

	public function setOrgName( $orgid, $orgname ) {
		$sql = "UPDATE ".$this->tbl_prf."orgs SET ORGNAME = \"".
				$orgname."\" WHERE orgid = ".$orgid;

		mysql_select_db( $this->dbname );
		mysql_query( $sql );
	}
	
	public function getOrgsOwnedBy($uid) {
		$ret = array();
		foreach($this->org_info as $org => $info)
			if($info['owner'] == $uid)
				$ret[] = $org;
		if(!isset($this->member_info) || !isset($this->member_info[$uid]))
			return $ret; // TODO: fillMemberInfo
		
		foreach($this->member_info[$uid] as $org => $group_array)
			foreach($group_array as $group => $member_info)
				foreach($member_info['perms'] as $perm)
					if($perm[0] == PERM_ORG_ADMIN)
						$ret[] = $org;
		return $ret;
	}

	// ============================================================
	// ===================== Group interface ======================
	// ============================================================

	// Info functions
	public function getNumGroups() {
		$sql = "SELECT groupid FROM ".$this->tbl_prf."groups WHERE 1";
		mysql_select_db( $this->dbname );
		$result = mysql_query( $sql, $this->conn );
		if( $result )
			return mysql_num_rows( $result );

		return false;
	}

	public function getGroupName( $groupid ) {
		$sql = "SELECT groupname FROM ".$this->tbl_prf."groups WHERE ".
				"groupid = ".$groupid;
		mysql_select_db( $this->dbname );
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 ) 
			return mysql_result( $result, 0);

		return false;
	}
	public function getGroupDesc( $groupid ) {
		$sql = "SELECT description FROM ".$this->tbl_prf.
				"groups WHERE "."groupid = ".$groupid;
		mysql_select_db( $this->dbname );
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 ) 
			return mysql_result( $result, 0);

		return false;
	}
	public function getGroupOrg( $groupid ) {
		$sql = "SELECT orgid FROM ".$this->tbl_prf.
				"groups WHERE groupid=".$groupid;
		mysql_select_db( $this->dbname );
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 )
			return mysql_result( $result, 0);

		return false;
	}
	public function getGroupState( $groupid ) {
		$sql = "SELECT state FROM ".$this->tbl_prf.
				"groups WHERE groupid=".$groupid;
		mysql_select_db( $this->dbname );
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 )
			return mysql_result( $result, 0);

		return false;
	}

	public function getGroupMembers( $groupid ) {
		$members = array();

		$sql = "SELECT userid FROM ".$this->tbl_prf.
				"memberships WHERE groupid=".$groupid;
		mysql_select_db( $this->dbname );
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 )
			while( $result_array = mysql_fetch_array( $result ) )
				array_push( $members, $result_array['userid'] );
		return $members;
	}
	
	public function isGroupMember( $userid, $groupid ) {
		$members = array();

		$sql = "SELECT userid FROM ".$this->tbl_prf.
				"memberships WHERE userid = ".$userid.
				" AND groupid = ".$groupid;
		mysql_select_db( $this->dbname );
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 )
			return true;

		return false;
	}

	public function getGroupsByUser( $uid ) {
		if( ! $uid )
			return false;

		$ret = array();
		if($membersByUid = $this->get_memberInfo_byUid($uid))
			foreach($membersByUid as $org => $groups)
				foreach($groups as $group => $groupInfo)
					$ret[] = array($org, $group);

		//echo 'groupsbyuser: '; var_dump($ret); echo '<br>';
		return $ret;
	}
	
	public function getGroupsAdministeredBy($uid, $get_org_admin = true) {
		if( ! $uid )
			return false;
			
		$add_ret = create_function('&$ret,$org,$grp', 'if(!isset($ret[$org])) $ret[$org]=array($grp); else $ret[$org][]= $grp;');

		$ret = array();
		if($membersByUid = $this->get_memberInfo_byUid($uid))
			foreach($membersByUid as $org => $groups)
				foreach($groups as $group => $groupInfo)
					foreach($groupInfo['perms'] as $perm)
						if($perm[0] == PERM_ADMIN)
							$add_ret($ret,$org,$group);
						else if($perm[0] == PERM_ADMIN_OF)
							$add_ret($ret,$perm[1],$perm[2]);
		
		if($get_org_admin)
			foreach($this->getOrgsOwnedBy($uid) as $org)
				foreach($this->getOrgGroups($org) as $group)
					$add_ret($ret,$org,$group);

		//echo "getGroupsAdministeredBy($uid, $get_org_admin): "; var_dump($ret); echo '<br>';
		return $ret;
	}

	// Manipulation functions
	public function createGroup( $groupname, $desc, $orgid ) {
		// Don't create duplicate groups
		$sql = "SELECT groupid FROM ".$this->tbl_prf."groups WHERE ".
				"groupname=\"".$groupname."\" AND ".
				"orgid=".$orgid;
		mysql_select_db( $this->dbname );
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 )
			return false;

		$sql = "INSERT INTO ".$this->tbl_prf."groups (groupname, description, orgid) ".
				"VALUES (\"".$groupname."\", \"".$desc.
				"\", ".$orgid.")";
		mysql_query( $sql, $this->conn );

		$sql = "SELECT groupid FROM ".$this->tbl_prf."groups WHERE ".
				"orgid=".$orgid." AND ".
				"groupname=\"".$groupname."\"";
		$result = mysql_query( $sql, $this->conn );
		if( $result && mysql_num_rows( $result ) > 0 )
			return(  mysql_result( $result, 0 ) );

		return false;
	}
	
	public function updateGroup( $groupid, $groupname, $desc, $orgid ) {
		$sql = "SELECT groupid FROM ".$this->tbl_prf."groups WHERE ".
				"groupid=".$groupid;
		mysql_select_db( $this->dbname );
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
	
	public function addMember( $userid, $groupid ) {
		$sql = "INSERT INTO ".$this->tbl_prf."memberships ".
				"(userid, groupid) VALUES ".
				"(".$userid.", ".$groupid.")";
		mysql_query( $sql, $this->conn );

		$sql = "SELECT MAX(id) FROM ".$this->tbl_prf."memberships";
		mysql_select_db( $this->dbname );
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
		mysql_select_db( $this->dbname );
		mysql_query( $sql, $this->conn );
	}
}

?>
