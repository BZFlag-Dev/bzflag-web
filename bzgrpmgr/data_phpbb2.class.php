<?php

// Copyright (c) 1993 - 2007 Tim Riker
//
// This package is free software;  you can redistribute it and/or
// modify it under the terms of the license found in the file
// named COPYING that should have accompanied this file.
//
// THIS PACKAGE IS PROVIDED ``AS IS'' AND WITHOUT ANY EXPRESS OR
// IMPLIED WARRANTIES, INCLUDING, WITHOUT LIMITATION, THE IMPLIED
// WARRANTIES OF MERCHANTIBILITY AND FITNESS FOR A PARTICULAR PURPOSE.

/*
This file contains the user/group data access layer for bzgrpmgr.
It can be adapted to a different database type if/when the global
login database type changes. Presently it is adapted to use a
phpBB-style MySQL database for users and a custom MySQL for
groups.
*/

// FIXME this file is now officialy out-of-date with data.class.php
// FIXME make the return value of a function consistent

require_once( "data.class.php" );

class data_phpbb2 extends data {
	// This class operates two MySQL database connections
	// simultaneously, which can get tricky at times
	private $config;
	private $phpBB2_mysql_connection;
	private $main_mysql_connection;

	public function init_databases( $_config ) {
		$this->config = $_config;

		// Connect to main database
		$this->main_mysql_connection = mysql_connect( $this->config['sql_hostname'],
				$this->config['sql_username'],
				$this->config['sql_password'], true ) or
						die( "Could not connect to MySQL database." );
		mysql_select_db( $this->config['sql_database'], $this->main_mysql_connection ) or
				die( "Could not select MySQL database." );

		// Connect to phpBB2 database
		$this->phpBB2_mysql_connection = mysql_connect(
				$this->config['phpbb_sql_hostname'],
				$this->config['phpbb_sql_username'],
				$this->config['phpbb_sql_password'], true ) or
						die( 'Could not connect to phpBB2 MySQL server.' );
		mysql_select_db( $this->config['phpbb_sql_database'],
				$this->phpBB2_mysql_connection ) or
						die( "Could not select phpBB2 MySQL database." );
	}

	function __destruct() {
		;
	}		

	// Function to retrieve a list of user id's
	public function getUserList() {
		$toReturn = array();

		$sql = "SELECT user_id FROM ".
				$this->config['phpBB2_users_table']."";

		$result = mysql_query( $sql, $this->phpBB2_mysql_connection );
		if( $result && mysql_num_rows( $result ) > 0 ) {
			while( $result_array = mysql_fetch_array( $result ) ) {
				if( $result_array['user_id'] > 0 )
					array_push( $toReturn,
						$result_array['user_id'] );
			}
		}

		return $toReturn;
	}

	// Function to retrieve callsign/username by id
	public function getUsername( $id ) {
		$sql = "SELECT username FROM ".
				$this->config['phpBB2_users_table'].
				" WHERE user_id=".$id;
		$result = mysql_query( $sql, $this->phpBB2_mysql_connection );
		if( $result && mysql_num_rows( $result ) > 0 &&
				$toReturn = mysql_result( $result, 0) )
			return $toReturn;

		return "";
	}

	// Function to create a new group in the specified organizations
	public function createGroup( $groupname, $desc, $orgid ) {
		$sql = "SELECT groupid FROM groups WHERE ".
				"groupname=\"".$groupname."\" AND ".
				"orgid=".$orgid;
		$result = mysql_query( $sql, $this->main_mysql_connection );

		if( $result && mysql_num_rows( $result ) > 0 )
			return false;

		$sql = "INSERT INTO groups (groupname,description,orgid) ".
				"VALUES (\"".$groupname."\",\"".$desc."\",".$orgid.")";

		mysql_query( $sql, $this->main_mysql_connection );

		$sql = "SELECT groupid FROM groups WHERE ".
				"orgid=".$orgid." AND ".
				"groupname=\"".$groupname."\"";
		$result = mysql_query( $sql, $this->main_mysql_connection );
		if( $result && mysql_num_rows( $result ) > 0 &&
				$toReturn = mysql_result( $result, 0 ) )
			return $toReturn;

		return 0;
	}

	public function updateGroup( $groupid, $groupname, $description, $orgid ) {
		$sql = "SELECT groupid FROM groups WHERE ".
				"groupid=".$groupid;
		$result = mysql_query( $sql, $this->main_mysql_connection );
		if( ! $result || mysql_num_rows( $result ) < 1 )
			return false;

		$sql = "UPDATE groups SET".
				( $groupname ? " groupname=\"".$groupname."\"," : "" ).
				( $description ? " description=\"".$description."\"," : "" ).
				( $orgid ? " orgid=".$orgid."," : "" ).
				// yeah, laziness to get rid of the last comma
				" groupid=".$groupid." ".
				"WHERE groupid=".$groupid;
		mysql_query( $sql, $this->main_mysql_connection );

		return 0;
	}

	// Function to retrieve member id's by group id
	public function getMembers( $groupid ) {
		$toReturn = array();

		$sql = "SELECT userid FROM group_members WHERE groupid=".
				$groupid;
		$result = mysql_query( $sql, $this->main_mysql_connection );
		if( $result && mysql_num_rows( $result ) > 0 ) {
			while( $result_array = mysql_fetch_array( $result ) ) {
				array_push( $toReturn,
						$result_array['userid'] );
			}
		}

		return $toReturn;
	}

	// Function to retrieve member group id's by user id
	public function getGroups( $id ) {
		$toReturn = array();

		$sql = "SELECT groupid FROM group_members WHERE userid=".
				$id;
		$result = mysql_query( $sql, $this->main_mysql_connection );
		if( $result && mysql_num_rows( $result ) > 0 ) {
			while( $result_array = mysql_fetch_array( $result ) ) {
				array_push( $toReturn,
						$result_array['groupid'] );
			}
		}

		return $toReturn;
	}
	// Function to get group id's by org id
	public function getOrgGroups( $id ) {
		$toReturn = array();

		$sql = "SELECT groupid FROM groups WHERE orgid=".
				$id;
		$result = mysql_query( $sql, $this->main_mysql_connection );
		if( $result && mysql_num_rows( $result ) > 0 ) {
			while( $result_array = mysql_fetch_array( $result ) ) {
				array_push( $toReturn,
						$result_array['groupid'] );
			}
		}

		return $toReturn;
	}

	// Function to retrieve group name by group id
	public function getGroupName( $groupid ) {
		$sql = "SELECT groupname FROM groups WHERE groupid=".$groupid;
		$result = mysql_query( $sql, $this->main_mysql_connection );
		if( $result && mysql_num_rows( $result ) > 0 &&
				$toReturn = mysql_result( $result, 0) )
			return $toReturn;

		return "";
	}

	// Function to retrieve group name by group id
	public function getGroupDescription( $groupid ) {
		$sql = "SELECT description FROM groups WHERE groupid=".$groupid;
		$result = mysql_query( $sql, $this->main_mysql_connection );
		if( $result && mysql_num_rows( $result ) > 0 &&
				$toReturn = mysql_result( $result, 0) )
			return $toReturn;

		return "";
	}

	// Function to add a user to a group
	public function addGroupMember( $userid, $groupid ) {
		if( ! $userid || ! $groupid ) { echo "give me args!<br>\n"; return true; }

		$sql = "INSERT INTO group_members (userid,groupid) VALUES (".
				$userid.",".$groupid.")";
		mysql_query( $sql, $this->main_mysql_connection );

		return false;
	}

	// Function to remove a user from a group
	// (will fail and return true if the user is the only  admin)
	public function removeGroupAdmin( $id ) {
		return false;
	}

	// Function to create an organization by name
	public function createOrg( $name, $userid ) {
		// Don't create duplicate groups
		if( $this->orgExists( $name ) )
			return true;

		// Create the organization
		$sql = "INSERT INTO organizations (orgname,contact) ".
				"VALUES (\"".$name."\",".$userid.")";
		mysql_query( $sql, $this->main_mysql_connection );

		$sql = "SELECT orgid FROM organizations WHERE ".
				"orgname=\"".$name."\"";
		$result = mysql_query( $sql, $this->main_mysql_connection );
		if( $result && mysql_num_rows( $result ) > 0 )
			$myOrgid = mysql_result( $result, 0);

		// Create the owner group
		$ownerGroup = $this->createGroup( "owner", "Owner group for the ".
				$this->getOrgName( $myOrgid )." organization.", $myOrgid );

		// Add the registrant to the owner group
		$this->addGroupMember( $userid, $ownerGroup );

		// Make the group an organization admin group
		$this->setAdminGroup( $ownerGroup, true );

		return $myOrgid;
	}

	// Function to retrieve the orgid for a given groupid
	public function getOrg( $id ) {
		$sql = "SELECT orgid FROM groups WHERE groupid=".$id;
		$result = mysql_query( $sql, $this->main_mysql_connection );
		if( $result && mysql_num_rows( $result ) > 0 &&
				$toReturn = mysql_result( $result, 0) )
			return $toReturn;

		return "";
	}

	// Function to check if a given organization exists
	public function orgExists( $name ) {
		$sql = "SELECT orgid FROM organizations WHERE ".
				"orgname=\"".$name."\"";
		$result = mysql_query( $sql, $this->main_mysql_connection );
		if( $result && mysql_num_rows( $result ) > 0 )
			return true;

		return false;
	}

	// Function to retrieve an organization's name by id
	public function getOrgName( $id ) {
		$sql = "SELECT orgname FROM organizations WHERE orgid=".$id;
		$result = mysql_query( $sql, $this->main_mysql_connection );
		if( $result && mysql_num_rows( $result ) > 0 &&
				$toReturn = mysql_result( $result, 0) )
			return $toReturn;

		return "";
	}

	// Function to return a list of organizations a user may admin
	// This is dead... going to use other methods for this now
/*	public function getOrgAdminships( $id ) {
		$orgs = array();

		$sql = "SELECT groupid FROM group_members WHERE userid=".
				$id;
		$result = mysql_query( $sql, $this->main_mysql_connection );
		if( $result && mysql_num_rows( $result ) > 0 ) {
			while( $result_array = mysql_fetch_array( $result ) ) {
				$sql2 = "SELECT orgid FROM groups WHERE ".
						"groupid=".$result_array['groupid']." AND (".
						"adminusers=1 OR admingroups=1 OR admin=1)";
				$result2 = mysql_query( $sql2, $this->main_mysql_connection );
				if( $result2 && mysql_num_rows( $result2 ) > 0 ) {
					while( $result_array2 = mysql_fetch_array( $result2 ) ) {
						// Have to use array KEYS here, to avoid duplicates
						if( ! $adminOrgs[$result_array2['orgid']] )
							$orgs[$result_array2['orgid']] = 1;
					}
				}
			}
		}

		return array_keys( $orgs );
	} */

	// Function to check whether a given user can admin users in
	// the specified organization (kind of a reverse of the above)
	public function isUserAdmin( $orgid, $userid ) {
		// Get a list of groups
		$sql = "SELECT groupid FROM group_members WHERE userid=".
				$userid;
		$result = mysql_query( $sql, $this->main_mysql_connection );
		if( $result && mysql_num_rows( $result ) > 0 ) {
			while( $result_array = mysql_fetch_array( $result ) ) {
				// Check the organizations
				// Full admins can, of course, admin users as well
				$sql2 = "SELECT groupid FROM groups WHERE ".
						"groupid = ".$result_array['groupid']." AND ".
						"orgid = ".$orgid." AND ".
						"(admin=1 OR adminusers=1)";
				$result2 = mysql_query( $sql2, $this->main_mysql_connection );

				if( $result2 && mysql_num_rows( $result2 ) > 0 &&
						$toReturn = mysql_result( $result2, 0) )
					return $toReturn;
			}
		}

		return false;
	}

	// Function to check whether a given user can admin groups in
	// the specified organization
	public function isGroupAdmin( $orgid, $userid ) {
		// Get a list of groups
		$sql = "SELECT groupid FROM group_members WHERE userid=".
				$userid;
		$result = mysql_query( $sql, $this->main_mysql_connection );
		if( $result && mysql_num_rows( $result ) > 0 ) {
			while( $result_array = mysql_fetch_array( $result ) ) {
				// Check the organizations
				// Full admins can, of course, admin groups as well
				$sql2 = "SELECT groupid FROM groups WHERE ".
						"groupid = ".$result_array['groupid']." AND ".
						"orgid = ".$orgid." AND ".
						"(admin=1 OR admingroups=1)";
				$result2 = mysql_query( $sql2, $this->main_mysql_connection );

				if( $result2 && mysql_num_rows( $result2 ) > 0 &&
						$toReturn = mysql_result( $result2, 0) )
					return $toReturn;
			}
		}

		return false;
	}

	// Function to check whether a given user is a super-admin in
	// the specified organization
	public function isAdmin( $orgid, $userid ) {
		// Get a list of groups
		$sql = "SELECT groupid FROM group_members WHERE userid=".
				$userid;
		$result = mysql_query( $sql, $this->main_mysql_connection );
		if( $result && mysql_num_rows( $result ) > 0 ) {
			while( $result_array = mysql_fetch_array( $result ) ) {
				// Check the organizations
				// Full admins can, of course, admin groups as well
				$sql2 = "SELECT groupid FROM groups WHERE ".
						"groupid = ".$result_array['groupid']." AND ".
						"orgid = ".$orgid." AND ".
						"admin=1";
				$result2 = mysql_query( $sql2, $this->main_mysql_connection );

				if( $result2 && mysql_num_rows( $result2 ) > 0 &&
						$toReturn = mysql_result( $result2, 0) )
					return $toReturn;
			}
		}

		return false;
	}

	public function isUserAdminGroup( $groupid ) {
		$sql = "SELECT groupid FROM groups WHERE groupid=".$groupid.
				" AND adminusers=1";
		$result = mysql_query( $sql, $this->main_mysql_connection );
		if( $result && mysql_num_rows( $result ) > 0 )
			return true;

		return false;
	}

	public function isGroupAdminGroup( $groupid ) {
		$sql = "SELECT groupid FROM groups WHERE groupid=".$groupid.
				" AND admingroups=1";
		$result = mysql_query( $sql, $this->main_mysql_connection );
		if( $result && mysql_num_rows( $result ) > 0 )
			return true;

		return false;
		;
	}

	public function isAdminGroup( $groupid ) {
		$sql = "SELECT groupid FROM groups WHERE groupid=".$groupid.
				" AND admin=1";
		$result = mysql_query( $sql, $this->main_mysql_connection );
		if( $result && mysql_num_rows( $result ) > 0 )
			return true;

		return false;
		;
	}

	public function setUserAdminGroup( $groupid, $value ) {
		if( $value != true && $value != false )
			return true;

		$sql = "UPDATE groups SET adminusers=".$value." WHERE ".
				"groupid=".$groupid;
		mysql_query( $sql, $this->main_mysql_connection );
	}

	public function setGroupAdminGroup( $groupid, $value ) {
		if( $value != true && $value != false )
			return true;

		$sql = "UPDATE groups SET admingroups=".$value." WHERE ".
				"groupid=".$groupid;
		mysql_query( $sql, $this->main_mysql_connection );
	}

	public function setAdminGroup( $groupid, $value ) {
		if( $value != true && $value != false )
			return true;

		$sql = "UPDATE groups SET admin=".$value." WHERE ".
				"groupid=".$groupid;
		mysql_query( $sql, $this->main_mysql_connection );
	}
}

?>
