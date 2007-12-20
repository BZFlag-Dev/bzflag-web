<?php

// Load the site config
require_once( "config.inc" );

// Load utility and class includes
require_once( "functions.inc" );
require_once( "class/data_handler.class.inc" );
require_once( "class/users_handler_phpbb2.class.inc" );
require_once( "class/groups_handler_mysql.class.inc" );
require_once( "class/auth_handler.class.inc" );

session_start();

// Instantiate (or retrieve from the session) our objects
$data = new data_handler( $config['data'] );
if( ! $data )
	graceful_exit( "Sorry, could not instantiate the data handler." );

$users = new users_handler_phpbb2( $config['users'] );
if( ! $users )
	graceful_exit( "Sorry, could not instantiate the users handler." );

$groups = new groups_handler_mysql( $config['groups'] );
if( ! $groups )
	graceful_exit( "Sorry, could not instantiate the groups handler." );

if( ! $_SESSION['auth'] ) {
	$_SESSION['auth'] = new auth_handler( $users, $groups );
	if( ! $_SESSION['auth'] )
		graceful_exit( "Sorry, could not instantiate the authorization handler." );
}
$auth = $_SESSION['auth'];