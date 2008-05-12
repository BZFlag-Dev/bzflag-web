<?php

// Visitor authorization management class
// Presently uses globals for $data, which may not be the
// best solution

class auth {
	private $userID, $username;

	public function logUserIn( $username, $password ) {
		global $data;

		$userID = $data->getUserID( $username );
		if( ! $userID )
			return false;
		if( $data->getEncryptedPass( $userID ) == md5( $password ) ) {
			$this->userID = $userID;
			$this->username = $username;
			return true;
		}

		return false;
	}
	public function logUserOut() {
		unset( $this->userID );
		unset( $this->username );
	}

	public function getUserID() {
		return $this->userID;
	}
	public function getUsername() {
		return $this->username;
	}

	public function isLoggedIn() {
		if( $this->userID )
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
