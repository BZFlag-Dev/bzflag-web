<?php

// Data layer class. This class provides the site with an interface for
// accessing all data.

// taken from http://www.php.net/debug_backtrace
function pretty_backtrace($print=true)
{
	$s = '';
	if (PHPVERSION() >= 4.3) {
   
		$MAXSTRLEN = 64;
   
		$s = '<pre align=left>';
		$traceArr = debug_backtrace();
		array_shift($traceArr);
		$tabs = sizeof($traceArr)-1;
		foreach ($traceArr as $arr) {
			for ($i=0; $i < $tabs; $i++) $s .= ' &nbsp; ';
			$tabs -= 1;
			$s .= '<font face="Courier New,Courier">';
			if (isset($arr['class'])) $s .= $arr['class'].'.';
			$args = array();
			foreach($arr['args'] as $v) {
				if (is_null($v)) $args[] = 'null';
				else if (is_array($v)) $args[] = 'Array['.sizeof($v).']';
				else if (is_object($v)) $args[] = 'Object:'.get_class($v);
				else if (is_bool($v)) $args[] = $v ? 'true' : 'false';
				else {
					$v = (string) @$v;
					$str = htmlspecialchars(substr($v,0,$MAXSTRLEN));
					if (strlen($v) > $MAXSTRLEN) $str .= '...';
					$args[] = $str;
				}
			}
		   
			$s .= $arr['function'].'('.implode(', ',$args).')';
			$s .= sprintf("</font><font color=#808080 size=-1> # line %4d,".
" file: <a href=\"file:/%s\">%s</a></font>",
$arr['line'],$arr['file'],$arr['file']);
			$s .= "\n";
		}   
		$s .= '</pre>';
		if ($print) print $s;
	}
	return $s;
}

class data {
	private $conn, $tbl_prf, $dbname, $bbdbname;

	// ============================================================
	// ====================== Initialization ======================
	// ============================================================

	function __construct( $config ) {
		$this->group_info = array(); $this->member_info = array(); $this->org_info = array();
	}

	// ============================================================
	// ================== Permissions interface ===================
	// ============================================================
	
	// cache all information retrieved from the daemon in these vars because they may be used many times on a single page
	private $group_info, $member_info, $org_info, $user_info;
	
	public function fillAllUserInfo($uid, $fill_orgGroups = true, $fill_memberCount = true) {
		global $userstore;
		
		// get info for the user's group memberships
		foreach($userstore->getMemberInfo($uid) as $memberInfo)
			$this->set_member_info($uid, $memberInfo['ou'], $memberInfo['grp'], $memberInfo['perms']);
			
		//$this->dump_cache();
			
		 // get info for the groups the user is member of
		$member_groups = $this->getGroupsByUser($uid);
		foreach($userstore->getGroupInfo($member_groups) as $groupInfo)
			$this->set_group_info($groupInfo['ou'], $groupInfo['grp'], $groupInfo['state'], $groupInfo['perms']);
		
		if($fill_memberCount)
			foreach($userstore->getMemberCount($member_groups) as $memberCount)
				$this->set_group_memberCount($memberCount['ou'], $memberCount['grp'], $memberCount['count']);
			
		//$this->dump_cache();
		
		// get info for the orgs the user is the owner of  (except those specified as permissions)
		foreach($userstore->getOrgsOwnedBy($uid) as $org)
			$this->set_org_info($org, $uid, null);
			
		//$this->dump_cache();
		
		// get all groups for all orgs the user is a member of, including those specified in permissions
		if($fill_orgGroups)
			$this->fillOrgGroups($this->getOrgsOwnedBy($uid));
		
		//$this->dump_cache();
	}
	
	public function fillGroupInfo($org, $grp) {
		global $userstore;
		foreach($userstore->getGroupMembers(array(array($org, $grp))) as $memberInfo)
			$this->set_member_info($memberInfo['uid'], $memberInfo['ou'], $memberInfo['grp'], $memberInfo['perms']);
		
		foreach($userstore->getUserNames($this->getGroupMembers($org, $grp)) as $uidname)
			$this->set_user_name($uidname['uid'], $uidname['name']);
	}
	
	public function dump_cache() {
		echo 'group_info: '; var_dump($this->group_info); echo '<br>';
		echo 'member_info: '; var_dump($this->member_info); echo '<br>';
		echo 'org_info: '; var_dump($this->org_info); echo '<br>';
	}
	
	public function fillOrgGroups($org_array) {
		global $userstore;
		foreach($userstore->getOrgGroups($org_array) as $org => $group)
			$this->init_group_info($org, $group, false);
	}
	
	private function init_org_info($ou) {
		if(!isset($this->org_info[$ou])) $this->org_info[$ou] = array( 'owner' => null, 'contact' => null );
		else {
			if(!isset($this->org_info[$ou]['owner'])) $this->org_info[$ou]['owner'] = null;
			if(!isset($this->org_info[$ou]['contact'])) $this->org_info[$ou]['contact'] = null;
		}
	}
	
	private function init_user_info($uid) {
		if(!isset($this->user_info[$uid])) $this->user_info[$uid] = array( 'name' => null );
		else {
			if(!isset($this->user_info[$uid]['name'])) $this->user_info[$uid]['name'] = null;
		}
	}
	
	private function init_group_info($ou, $grp, $set_null_info = true) {
		if(!isset($this->group_info[$ou])) $this->group_info[$ou] = array( $grp => array( 'state' => null, 'perms' => array() ) );
		else if(!isset($this->group_info[$ou][$grp])) $this->group_info[$ou][$grp] = array( 'state' => null, 'perms' => array() );
		else if($set_null_info) {
			if(!isset($this->group_info[$ou][$grp]['perms'])) $this->group_info[$ou][$grp]['perms'] = array();
			if(!isset($this->group_info[$ou][$grp]['state'])) $this->group_info[$ou][$grp]['state'] = null;
			if(!isset($this->group_info[$ou][$grp]['member_count'])) $this->group_info[$ou][$grp]['member_count'] = null;
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
	
	private function set_member_info($uid, $ou, $grp, $perms) {
		$this->init_member_info($uid, $ou, $grp);
		$this->member_info[$uid][$ou][$grp]['perms'] = $perms;
	}
	
	private function set_user_name($uid, $name) {
		$this->init_user_info($uid);
		$this->user_info[$uid]['name'] = $name;
	}
	
	private function set_org_info($ou, $owner, $contact) {
		//echo "$ou $owner $contact<br>";
		$this->init_org_info($ou);
		$this->org_info[$ou]['owner'] = $owner;
		$this->org_info[$ou]['contact'] = $contact;
	}
	
	private function set_group_info($ou, $grp, $state, $perms) {
		$this->init_group_info($ou, $grp);
		$this->group_info[$ou][$grp]['state'] = $state;
		$this->group_info[$ou][$grp]['perms'] = $perms;
	}
	
	private function set_group_memberCount($ou, $grp, $count) {
		$this->init_group_info($ou, $grp);
		$this->group_info[$ou][$grp]['member_count'] = $count;
	}
	
	private function get_memberInfo_byUid($uid) {
		if(isset($this->member_info) && isset($this->member_info[$uid]))
			return $this->member_info[$uid];
		else
			return false;
	}
	
	private function get_memberInfo_byUidOrg($uid, $org) {
		if($byUid = $this->get_memberInfo_byUid($uid))
			if(isset($byUid[$org]))
				return $byUid[$org];
		return false;
	}
	
	private function get_memberInfo($uid, $org, $grp) {
		if($byUidOrg = $this->get_memberInfo_byUidOrg($uid, $org))
			if(isset($byUidOrg[$grp]))
				return $byUidOrg[$grp];
		return false;
	}
	
	private function get_orgInfo($org) {
		if(isset($this->org_info) && isset($this->org_info[$org]))
			return $this->org_info[$org];
		else
			return false;
	}
	
	private function get_userInfo($uid) {
		if(isset($this->user_info) && isset($this->user_info[$uid]))
			return $this->user_info[$uid];
		else
			return false;
	}
	
	private function get_groupInfo_byOrg($org) {
		if(isset($this->group_info) && isset($this->group_info[$org]))
			return $this->group_info[$org];
		else
			return false;
	}
	
	private function get_groupInfo($ou, $grp) {
		if(isset($this->group_info) && isset($this->group_info[$ou]) && 
			isset($this->group_info[$ou][$grp]))
			return $this->group_info[$ou][$grp];
		else
			return false;
	}
	
	private function find_perm($perm_ids, $in_perms, $with_org = false, $with_grp = false, $find_all = false, &$arr = array()) {
		$ret = false;
		foreach($in_perms as $perm) {
			if(in_array($perm[0], $perm_ids)) {
				if($with_org && $with_grp)
					$ret = array('perm' => $perm, 'ou' => $with_org, 'grp' => $with_grp);
				else
					$ret = $perm;
				
				if($find_all)
					$arr[]= $ret;
				else
					return $ret;
			}
		}

		return $ret;
	}
	
	private function find_perm_grp($perm_ids, $org, $grp, $with_details = false, $find_all = false, &$arr = array()) {
		if($groupInfo = $this->get_groupInfo($org, $grp)) {
			$perm = $this->find_perm($perm_ids, $groupInfo['perms'], 
				($with_details ? $org : null), ($with_details ? $grp : null), $find_all, $arr);
			if($find_all == false) return $perm;
		}
		return false;
	}
	
	private function find_perm_uid($perm_ids, $uid, $with_details = false, $find_all = false) {
		$ret = ($find_all ? array() : false);
		if($membersByUid = $this->get_memberInfo_byUid($uid)) {
			foreach($membersByUid as $org => $groups) {
				foreach($groups as $group => $memberInfo) {
					$perm = $this->find_perm($perm_ids, $memberInfo['perms'],
						($with_details ? $org : null), ($with_details ? $group : null), $find_all, $ret);
					if($find_all == false && $perm) return $perm;
					$perm = $this->find_perm_grp($perm_ids, $org, $group, $with_details, $find_all, $ret);
					if($find_all == false && $perm) return $perm;
				}
			}
		}
		return $ret;
	}
	
	private function find_perm_uid_org($perm_ids, $uid, $org) {
		if($memberGroups = $this->get_memberInfo_byUidOrg($uid, $org))
			foreach($memberGroups as $group => $memberInfo)
			if($perm = $this->find_perm($perm_ids, $memberInfo['perms']) ||
				$perm = $this->find_perm_grp($perm_ids, $org, $group))
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
		if($orgInfo = $this->get_orgInfo($org))
			if($orgInfo['owner'] == $uid)
				return true;
		if($perm = $this->find_perm_uid_org(array(PERM_ORG_ADMIN), $uid, $org))
			return true;
		return false;
	}
	
	public function isGroupAdmin( $uid, $org, $grp ) {
		$groups = $this->getGroupsByUser( $uid );

		if( ! $groups )
			return false;
			
		if($memberInfo = $this->get_memberInfo($uid, $org, $grp))
			if($this->find_perm(array(PERM_ADMIN), $memberInfo['perms']))

		if( $this->isOrgAdmin( $uid, $org ) )
			return true;

		foreach($this->find_perm_uid(array(PERM_ADMIN_OF), $uid, true, true) as $arr)
			if($ret['perm'][1] == $org && $ret['perm'][2] == $grp)
				return true;

		return false;
	}

	// ============================================================
	// ====================== User interface ======================
	// ============================================================

	public function getUserID( $username ) {
		todo();
		return false;
	}
	
	public function getUsername( $uid ) {
		if($info = $this->get_userInfo($uid))
			return $info['name'];
		return false;
	}
	
	public function getEncryptedPass( $userid ) {
		todo();
		return false;
	}

	// ============================================================
	// ================== Organization interface ==================
	// ============================================================

	public function getNumOrgs() {
		global $userstore;
		return $userstore->getNumOrgs();
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
		
		foreach($this->find_perm_uid(array(PERM_ORG_ADMIN), $uid, true, true) as $arr)
			$ret[]= $arr['ou'];

		return $ret;
	}

	// ============================================================
	// ===================== Group interface ======================
	// ============================================================

	// Info functions
	public function getNumGroups() {
		global $userstore;
		return $userstore->getNumGroups();
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
	
	public function getGroupState( $org, $group ) {
		if($info = $this->get_groupInfo($org, $group))
			return $info['state'];
		return false;
	}

	public function getGroupMembers( $org, $group ) {
		$ret = array();
		if(!isset($this->member_info)) return $ret;
		foreach(array_keys($this->member_info) as $uid)
			if($this->isMemberOf($uid, $org, $group))
				$ret[]= $uid;
		return $ret;
	}
	
	public function getGroupMemberCount( $org, $group ) {
		if($info = $this->get_groupInfo($org, $group))
			return $info['member_count'];
		return false;
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
	
	public function isMemberOf($uid, $org, $grp) {
		if($membersByUid = $this->get_memberInfo_byUid($uid))
			if(isset($membersByUid[$org]) && isset($membersByUid[$org][$grp]))
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
	
	public function getGroupsAdministratedBy($uid, $get_org_admin = true) {
		if( ! $uid )
			return false;
			
		$ret = array();
		$add_ret = create_function('&$ret,$org,$grp', 
			'if(!isset($ret[$org])) $ret[$org] = array($grp => 1);
			else if(!isset($ret[$org][$grp])) $ret[$org][$grp] = 1;');

		foreach($this->find_perm_uid(array(PERM_ADMIN, PERM_ADMIN_OF), $uid, true, true) as $arr) {
			$perm = $arr['perm'];
			if($perm[0] == PERM_ADMIN)
				$add_ret($ret,$arr['ou'],$arr['grp']);
			else if($perm[0] == PERM_ADMIN_OF)
				$add_ret($ret,$perm[1],$perm[2]);
		}
		
		if($get_org_admin)
			foreach($this->getOrgsOwnedBy($uid) as $org)
				foreach($this->getOrgGroups($org) as $group)
					$add_ret($ret,$org,$group);

		//echo "getGroupsAdministratedBy($uid, $get_org_admin): "; var_dump($ret); echo '<br>';
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
