<?php

// Visitor authorization management class
// Presently uses globals for $data, which may not be the
// best solution

class auth {
	private $userid, $username;

	public function logUserIn( $username, $password ) {
		global $data;

		$userid = $data->getUserID( $username );
		if( ! $userid )
			return false;
		if( $data->getEncryptedPass( $userid ) == md5( $password ) ) {
			$this->userid = $userid;
			$this->username = $username;
			return true;
		}

		return false;
	}
	public function logUserOut() {
		unset( $this->userid );
		unset( $this->username );
	}

	public function getUserID() {
		return;
	}
	public function getUsername() {
		return $this->username;
	}

	public function isLoggedIn() {
		if( $this->userid )
			return true;
		else
			return false;
	}
	public function isAdmin() {
		if( $this->isLoggedIn() )
			return true;
		else
			return false;
	}
}
