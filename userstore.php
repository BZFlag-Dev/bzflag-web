<?php

// userstore.php
//
// Copyright (c) 1993 - 2004 Tim Riker
//
// This package is free software;  you can redistribute it and/or
// modify it under the terms of the license found in the file
// named COPYING that should have accompanied this file.
//
// THIS PACKAGE IS PROVIDED ``AS IS'' AND WITHOUT ANY EXPRESS OR
// IMPLIED WARRANTIES, INCLUDING, WITHOUT LIMITATION, THE IMPLIED
// WARRANTIES OF MERCHANTIBILITY AND FITNESS FOR A PARTICULAR PURPOSE.

/* expected globals:
  $ldap_host
  $ldap_rootdn
  $ldap_rootpass
  $ldap_suffix
  $daemon_http_ports
*/

define("REG_SUCCESS", 0);
define("REG_INVALID_MESSAGE", 1);
define("REG_USER_EXISTS", 2);
define("REG_MAIL_EXISTS", 3);
define("REG_FAIL_GENERIC", 4);

class UserStore {
	private $rootld;
	private $nextuid;
	
	private function bind($dn, $password) {
		global $ldap_host;
		$ld = ldap_connect($ldap_host);
		return ldap_bind($ld, $dn, $password) ? $ld : false;
	}
	
	private function getuserdn($callsign) {
		global $ldap_suffix;
		return 'cn=' . $callsign . ',' . $ldap_suffix;
	}
	
	public function auth($callsign, $password) {
		global $ldap_suffix;
		return $this->bind($this->getuserdn($callsign), $password);
	}
	
	public function getroot($die = true) {
		global $ldap_rootdn, $ldap_rootpass;
		if(!$this->rootld) {
			$this->rootld = $this->bind($ldap_rootdn, $ldap_rootpass);
			if(!$this->rootld && $die)
				die('failed to bind to rootdn');
		}
		return $this->rootld;
	}
	
	private function getIDfromDN($dn) {
		$conn = $this->getroot();
		
		$attrs = array("uid");
		$result = ldap_search($conn, $dn, "(userPassword=*)", $attrs);

		if (!$result || !ldap_count_entries($conn, $result))
			return false;

		$info = ldap_get_entries($conn, $result);
		return $info[0]["uid"][0];
	}
	
	public function getID($callsign) {
		return $this->getIDfromDN($this->getuserdn($callsign));
	}
	
	public function intersectGroupsNoExplode($callsign, $garray) {
		if (!count($garray))
			return array();

		return $this->sendRequest(array_merge(array("intersectGroups", $callsign), $garray));
	}
	
	private function escape($str) {
		str_replace("*", "*1", $str);
		str_replace("&", "*2", $str);
		return $str;
	}
	
	private function sendRequest($reqs) {
		global $daemon_http_ports;
		$port = $daemon_http_ports[array_rand($daemon_http_ports)];
		$url = "localhost:$port?";
		$first = true;
		foreach($reqs as $req) {
			if(!$first) $url = $url . "&";
			$first = false;
			$url = $url . $this->escape($req);
		}
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
		if(!$output || strlen($output) < 8) return "";
		return substr($output, 8);
	}
	
	public function registerUser($callsign, $password, $email) {
		$output = $this->sendRequest(array("register", $callsign, $password, $email));
		if($output == "") return REG_FAIL_GENERIC;
		return (int)$output;
	}
};

?>