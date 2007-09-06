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
This file contains the base data layer class. This class should be extended
and its methods overloaded by the applicable data framework.
*/

abstract class data {
	// Function to retrieve a list of user id's
	abstract protected function getUserList();

	// Function to retrieve callsign/username by id
	abstract protected function getUsername( $id );

	// Function to retrieve member group id's by user id
	abstract protected function getGroups( $id );

	// Function to retrieve group id's by org id
	abstract protected function getOrgGroups( $id );

	// Function to retrieve group name by group id
	abstract protected function getGroupname( $id );

	// Function to add a user to a group
	abstract protected function addGroupMember( $userid, $groupid );

	// Function to set a user as a group administrator
	// (also adds the user to the group if necessary)
//	abstract protected function setGroupAdmin( $id );

	// Function to remove a user from a group
	// (will fail and return true if the user is the only admin)
	abstract protected function removeGroupAdmin( $id );

	// Function to retrieve the orgid for a given groupid
	abstract protected function getOrg( $id );

	// Function to retrieve an organization's name by id
	abstract protected function getOrgName( $id );

	// Functions to check whether a user can admin an ORGANIZATION
	// in the respective capacity
	abstract protected function isUserAdmin( $orgid, $id );
	abstract protected function isGroupAdmin( $orgid, $id );
	abstract protected function isAdmin( $orgid, $id );
}

?>
