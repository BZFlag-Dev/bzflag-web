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

class UserStore {
	private $rootld;
	
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
	
	public function getID($callsign) {
		$conn = $this->getroot();
		
		$attrs = array("uid");
		$result = ldap_search($conn, $this->getuserdn($callsign), "(userPassword=*)", $attrs);

		if (!$result || !ldap_count_entries($conn, $result))
			return false;

		$info = ldap_get_entries($conn, $result);
		return $info[0]["uid"][0];
	}
	
	public function intersectGroups($callsign, $garray) {\
		global $ldap_suffix;
		$g = array();
		if (!count($garray))
			return $g;
		
		$conn = $this->getroot();
			
		$filter = "(&(objectClass=groupOfUniqueNames)(uniqueMember=" . $this->getuserdn($callsign) . ")(|";
		foreach($garray as $group)
			$filter = $filter . "(cn=" . $group . ")";
		$filter = $filter . "))";
		
		$result = ldap_search($conn, $ldap_suffix, $filter);
		if($result) {
			$info = ldap_get_entries($conn, $result);
			for ($i=0; $i<$info["count"]; $i++)
				$g[] = $info[$i]["cn"][0];
		}
		
		return $g;
	}
};

?>