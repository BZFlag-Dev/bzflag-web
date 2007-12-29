<?php

// Site database handler class

class data_handler {
	private $conn;
	private $tab_prf;

	function __construct( $config ) {
		// Connect to the MySQL database
		$this->conn = mysql_connect( $cofnig['host'], $config['user'],
				$config['pass'] );
		if( ! $this->conn )
			return false;
		if( ! mysql_select_db( $config['dbname'], $this->conn ) )
			return false;

		$this->tab_prf = $config['prefix'];
	}		
}

?>
