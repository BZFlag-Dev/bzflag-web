<?php

// Visitor authorization management class
// Presently uses globals for $users and $groups, which may not be the
// best solution

class auth_handler {
	private $userid, $username;

	public function logUserIn( $username, $password ) {
		global $users, $groups;

		$userid = $users->getUserID( $username );
		if( $users->getEncryptedPass( $userid ) == md5( $password ) ) {
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
		;
	}
	public function getUsername() {
		;
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
