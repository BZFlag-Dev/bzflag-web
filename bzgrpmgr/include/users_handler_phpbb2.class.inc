<?php

require_once( "users_handler.class.inc" );

// Users handling class adapted to work with a phpbb2 table

class users_handler_phpbb2 {
	private $conn;
	private $tab_prf;

	function __construct( $config ) {
		// Connect to the MySQL database
		$this->conn = mysql_connect( $config['host'], $config['user'],
				$config['pass'] );
		if( ! $this->conn )
			return false;
		if( ! mysql_select_db( $config['dbname'], $this->conn ) )
			return false;

		$this->tab_prf = $config['prefix'];
	}		

	// Function to retrieve the userid for the given username
	// (this should be the only function that takes a NAME as
	// an argument)
	public function getUserID( $username ) {
		$sql = "SELECT user_id FROM ".$this->tab_prf.
				"users WHERE username = \"".$username."\"";
		$result = mysql_query( $sql, $this->conn );
		if( ! $result )
			return false;

		if( $result && mysql_num_rows( $result ) > 0 )
				return mysql_result( $result, 0 );

		return false;
	}

	// Function to retrieve callsign/username by id
	public function getUsername( $userid ) {
		$sql = "SELECT username FROM ".$this->tab_prf.
				"users WHERE user_id = ".$userid;
		$result = mysql_query( $sql, $this->conn );
		if( ! $result )
			return false;

		if( $result && mysql_num_rows( $result ) > 0 )
				return mysql_result( $result, 0 );

		return false;
	}

	// Function to retrieve the encrypted password for a given user
	public function getEncryptedPass( $userid ) {
		$sql = "SELECT user_password FROM ".$this->tab_prf.
				"users WHERE user_id = ".$userid;
		$result = mysql_query( $sql, $this->conn );
		if( ! $result )
			return false;

		if( $result && mysql_num_rows( $result ) > 0 )
				return mysql_result( $result, 0 );

		return false;
	}
}

?>
