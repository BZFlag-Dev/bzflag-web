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

// Config options specific to given database type
// (note that this may mean a different MySQL username and password
// than bzgrpmgr uses)
$data_config['sql_hostname'] = "localhost";
$data_config['sql_database'] = "bzgrpmgr_phpbb2";
$data_config['sql_username'] = "bzgrpmgr";
$data_config['sql_password'] = "bzgrpmgr";
$data_config['phpBB2_users_table'] = "phpbb_users";

// MySQL connection for data layer
// Note: this will result in two mysql connections... ideas for
// a better way?
$data_mysql_connection = mysql_connect( $data_config['sql_hostname'],
		$data_config['sql_username'],
		$data_config['sql_password'] ) or
			die( 'Could not connect to DATA MySQL server.' );
mysql_select_db( $data_config['sql_database'] )
		or die( "Could not select DATA MySQL database." );

// Function to retrieve a list of user id's
function getUserList() {
	global $data_mysql_connection, $data_config;
	$toReturn = array();

	$sql = "SELECT user_id FROM ".$data_config['phpBB2_users_table'];
	$result = mysql_query( $sql, $data_mysql_connection );
	if( $result && mysql_num_rows( $result ) > 0 ) {
		while( $result_array = mysql_fetch_array( $result ) ) {
			array_push( $toReturn, $result_array['user_id'] );
		}
	}

	return $toReturn;
}

// Function to retrieve callsign/username by id
function getUsername( $id ) {
	global $data_mysql_connection, $data_config;
	$sql = "SELECT username FROM ".$data_config['phpBB2_users_table'].
			" WHERE user_id = ".$id;
	$result = mysql_query( $sql, $data_mysql_connection );
	if( $result && $toReturn = mysql_result( $result, 0) )
		return $toReturn;

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