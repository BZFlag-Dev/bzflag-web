<?php

// Defines
define( 'GRPSTATE_OPEN', 1 );
define( 'GRPSTATE_CLOSED', 2 );
define( 'GRPSTATE_HIDDEN', 3 );
define( 'GRPSTATE_INACTIVE', 4 );

// Load the site config
require_once( "config.inc" );

// Load utility and class includes
require_once( "functions.inc" );
require_once( "data.class.inc" );
require_once( "auth.class.inc" );

session_start();

// Instantiate (or retrieve from the session) our objects
$data = new data( $config );
if( ! $data )
	graceful_exit( "Sorry, could not instantiate the data handler." );

if( ! $_SESSION['auth'] ) {
	$_SESSION['auth'] = new auth();	// auth uses the global $data
	if( ! $_SESSION['auth'] )
		graceful_exit( "Sorry, could not instantiate the authorization handler." );
}
$auth = $_SESSION['auth'];