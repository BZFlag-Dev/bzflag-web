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
login database type changes.
*/

// Config options specific to given database type

// Function to retrieve callsign/username by id
function getUsername( $id ) {
	return "";
}

// Function to retrieve member group id's by user id
function getGroups( $id ) {
	return 1;
}

// Function to retrieve group name by group id
function getGroupname( $id ) {
	return "";
}

// Function to add a user to a group
function addGroupMember( $id ) {
	return false;
}

// Function to check if a user is an admin of a given group
function isAdmin( $id ) {
	return false;
}

// Function to set a user as a group administrator
// (also adds the user to the group if necessary)
function setGroupAdmin( $id ) {
	return false;
}

// Function to remove a user from a group
// (will fail and return true if the user is the only  admin)
function removeGroupAdmin( $id ) {
	return false;
}

?>