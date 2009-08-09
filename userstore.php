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
define("REG_USER_INVALID", 5);
define("REG_PASS_INVALID", 6);
define("REG_MAIL_INVALID", 7);

define("CHINF_SUCCESS", 0x0);
define("CHINF_INVALID_CALLSIGN", 0x1);
define("CHINF_INVALID_EMAIL", 0x2);
define("CHINF_INVALID_PASSWORD", 0x4);
define("CHINF_TAKEN_CALLSIGN", 0x8);
define("CHINF_TAKEN_EMAIL", 0x10);
define("CHINF_OTHER_ERROR", 0x1000);

class UserStore {
	private $rootld;
	private $nextuid;
	
	private function bind($dn, $password) {
		global $ldap_host;
		$ld = ldap_connect($ldap_host);
		ldap_set_option($ld, LDAP_OPT_PROTOCOL_VERSION, 3)
			or die("Failed to set ldap protocol version to 3");
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
	
	public function intersectGroupsNoExplode($callsign, $garray, $all, $ids) {
		if (!count($garray) && !$all)
			return array();
		// NOTE: if callsign = "" this returns all existing groups from the array
		//              if all = true then the groups of $callsign are intersected with all groups i.e returns all groups the user is in
		//             returns the values in the format ":group_name_1:group_name_1..."
		//             or if the ids = true then ":group_id_1:group_id_2.."

		return $this->sendRequest(array_merge(array("intersectGroups", ($all ? "1" : "0") . ($ids ? "1" : "0"), $callsign), $garray));
	}
	
	private function sendRequest($reqs) {
		global $daemon_http_ports;
		$port = $daemon_http_ports[array_rand($daemon_http_ports)];
		$url = "localhost:$port?";
		$first = true;
		foreach($reqs as $req) {
			if(!$first) $url = $url . "&";
			$first = false;
			$url = $url . urlencode($req);
		}
		$ch = curl_init(); if(!$ch) { debug("curl_init failed"); return ""; }
        if(!curl_setopt($ch, CURLOPT_URL, $url)) { debug("opt url failed"); return ""; }
        if(!curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1)) { debug("opt return failed"); return ""; }
		if(!curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2)) { debug("opt timeout failed"); return ""; }
        $output = curl_exec($ch);
        curl_close($ch);
		if(!$output || strlen($output) < 8) { debug("no output" . ($output ? strlen($output) : "")); return ""; }
		return trim(substr($output, 8));
	}
	
	public function registerUser($callsign, $password, $email) {
		$output = $this->sendRequest(array("register", $callsign, $password, $email));
		if($output == "" || !ctype_digit($output)) { debug("ret code wrong: " . $output); return REG_FAIL_GENERIC; }
		return (int)$output;
	}
	
	public function getToken($callsign, $password, $ip, &$token) {
		$token = $this->sendRequest(array("gettoken", $callsign, $password, $ip));
		return $token != "";
	}
	
	public function checkToken($callsign, $ip, $token) {
		$ret = $this->sendRequest(array("checktoken", $callsign, $ip, $token));
		if($ret != "1" && $ret != "2" && $ret != "3") return false;
		return $ret;
	}
	
	public function changeUserInfo($for_user, $to_user, $to_pass, $to_mail) {
		$output = $this->sendRequest( array("chinf", $for_user, $to_user, $to_pass, $to_mail) );
		if($output == "" || !ctype_digit($output)) { debug("ret code wrong: " . $output); return CHINF_OTHER_ERROR; }
		return (int)$output;
	}
	
	public function resetPassword($callsign, $email) {
		return $this->sendRequest(array("resetpass", $callsign, $email));
	}
	
	public function resendActivationMail($callsign, $email) {
		return $this->sendRequest(array("resendactmail", $callsign, $email));
	}
	
	public function activateUser($callsign, $email, $randtext) {
		return $this->sendRequest(array("activate", $callsign, $email, $randtext));
	}
	
};

?>