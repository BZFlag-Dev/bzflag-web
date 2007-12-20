<?php

// ==================== General Site Database Config ====================

// Initialize the site data handler
$config['data'] = array(
		// The MySQL host to connect to
		'host' => "localhost",
		// The MySQL database to connect to
		'dbname' => "",
		// The MySQL username to use
		'user' => "",
		// The MySQL password to use
		'pass' => "",
		// The table prefix for the site to use
		'prefix' => "admin_"
	);

// ==================== User handler setup ====================

// Configure this for a phpBB2 user handler
$config['users'] = array(
		// The MySQL host to connect to
		'host' => "localhost",
		// The MySQL database to connect to
		'dbname' => "",
		// The MySQL username to use
		'user' => "",
		// The MySQL password to use
		'pass' => "",
		// The table prefix that phpBB2 uses
		'prefix' => "phpbb2_"
	);

// ==================== Group handler setup ====================

// Uncomment and configure this for a default MySQL group handler
$config['groups'] = array(
		// The MySQL host to connect to
		'host' => "localhost",
		// The MySQL database to connect to
		'dbname' => "",
		// The MySQL username to use
		'user' => "",
		// The MySQL password to use
		'pass' => "",
		// The table prefix to use
		'prefix' => "groups_"
	);

?>
