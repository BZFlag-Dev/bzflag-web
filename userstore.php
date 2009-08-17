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

define("PERM_ADMIN_OF", 1);
define("PERM_ADMIN", 2);
define("PERM_ORG_ADMIN", 3);
define("PERM_GLOBAL_ADMIN", 4);

class UserStore {
	private $rootld;
	private $nextuid;
	
	private function debug($output) {
		echo $output . '<br>';
	}
	
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
	
	public function intersectGroupsNoExplode($callsign, $garray, $all) {
		if (!count($garray) && !$all)
			return "";
		// NOTE: if callsign = "" this returns all existing groups from the array
		//              if all = true then the groups of $callsign are intersected with all groups i.e returns all groups the user is in
		//             returns the values in the format ":group_name_1:group_name_1..."
		//             or if the ids = true then ":group_id_1:group_id_2.."

		return $this->sendRequest(array_merge(array("intersectGroups", ($all ? "1" : "0"), $callsign), $garray));
	}
	
	private function not_empty($value) {
		return empty($value) ? false : true;
	}
	
	public function intersectGroups($callsign, $garray, $all) {
		$list = $this->intersectGroupsNoExplode($callsign, $garray, $all);
		if($list == "")
			return array();
		return $this->explode_noempty(',', $list);
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
		$ch = curl_init(); if(!$ch) { $this->debug("curl_init failed"); return ""; }
        if(!curl_setopt($ch, CURLOPT_URL, $url)) { $this->debug("opt url failed"); return ""; }
        if(!curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1)) { $this->debug("opt return failed"); return ""; }
		if(!curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2)) { $this->debug("opt timeout failed"); return ""; }
        $output = curl_exec($ch);
        curl_close($ch);
		if(!$output || strlen($output) < 8) { $this->debug("no output" . ($output ? strlen($output) : "")); return ""; }
		return trim(substr($output, 8));
	}
	
	public function registerUser($callsign, $password, $email) {
		$output = $this->sendRequest(array("register", $callsign, $password, $email));
		if($output == "" || !ctype_digit($output)) { $this->debug("ret code wrong: " . $output); return REG_FAIL_GENERIC; }
		return (int)$output;
	}
	
	public function getToken($callsign, $password, $ip, &$token) {
		$token = $this->sendRequest(array("gettoken", $callsign, $password, $ip));
		return $token != "";
	}
	
	public function checkToken($callsign, $ip, $token, &$bzid) {
		$output = $this->sendRequest(array("checktoken", $callsign, $ip, $token));
		$arr = $this->explode_noempty(",", $output);
		$ret = $arr[0];
		$bzid = $arr[1];
		if($ret != "1" && $ret != "2" && $ret != "3") return false;
		return $ret;
	}
	
	public function changeUserInfo($for_user, $to_user, $to_pass, $to_mail) {
		$output = $this->sendRequest( array("chinf", $for_user, $to_user, $to_pass, $to_mail) );
		if($output == "" || !ctype_digit($output)) { $this->debug("ret code wrong: " . $output); return CHINF_OTHER_ERROR; }
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
	
	public function getGroupsOwnedBy($callsign) {
		return $this->sendRequest(array("groupsownedby", $callsign));
	}
	
	public function getNumGroups() {
		return (int)$this->sendRequest(array("totalgroups"));
	}
	
	public function getNumOrgs() {
		return (int)$this->sendRequest(array("totalorgs"));
	}
	
	private function explode_noempty($delimiter, $string) {
		return array_filter(explode($delimiter, $string), array($this, 'not_empty'));
	}
	
	private function getPerm($perm_str) {
		$perm = explode(' ', $perm_str);
		if(count($perm) == 0) return false;
		$perm[0] = (int)$perm[0];
		switch($perm[0]) {
			case PERM_ADMIN_OF:
				if(count($perm) != 3) { $this->debug("invalid args in $perm_str"); return false; }
				break;
			default:
				if(count($perm) != 1) { $this->debug("invalid args in $perm_str"); return false; }
		}
		return $perm;
	}
	
	private function getPermsArray($perm_strs) {
		$ret = array();
		if(!$perm_strs || !is_array($perm_strs)) { $this->debug("invalid perm_strs"); var_dump($perm_strs); return $ret; }
		foreach($perm_strs as $perm_str)
			if($perm = $this->getPerm($perm_str))
				$ret[] = $perm;
		return $ret;
	}
	
	private function parseMemberInfo($output) {
		$ret = array();
		foreach($this->explode_noempty(':', $output) as $groupinfo) {
			$arr = explode(',', $groupinfo);
			if(count($arr) == 0) { $this->debug("invalid group info $groupinfo"); continue; }
			$uog = explode(' ', $arr[0]);
			if(count($uog) != 3) { $this->debug("MemberInfo: invalid member info" . $arr[0]); continue; }
			
			if(count($arr) > 1) {
				array_shift($arr);
				$ret[] = array('uid' => $uog[0], 'ou' => $uog[1], 'grp' => $uog[2], 'perms' => $this->getPermsArray($arr));
			} else
				$ret[] = array('uid' => $uog[0], 'ou' => $uog[1], 'grp' => $uog[2], 'perms' => array());
		}
		
		//echo 'memberinfo: '; var_dump($ret); echo '<br>';
		return $ret;
	}
	
	public function getMemberInfo($uid) {
		if(!$uid)
			return array();
		
		return $this->parseMemberInfo($this->sendRequest(array("getmemberinfo", $uid)));
	}

	private function serialize($in_arr) {
		$out_arr = array();
		foreach($in_arr as $row)
			foreach($row as $val)
				$out_arr[]= $val;
		return $out_arr;
	}
	
	public function getGroupInfo($group_array) {
		if(empty($group_array))
			return array();
		
		$output = $this->sendRequest(array_merge(array('getgroupinfo'), $this->serialize($group_array)));
		$ret = array();
		foreach($this->explode_noempty(':', $output) as $groupinfo) {
			$arr = explode(',', $groupinfo);
			if(count($arr) == 0) { debug("empty groupinfos for $ret"); continue; }
			$ogs = explode(' ', $arr[0]);
			if(count($ogs) != 3) { debug("invalid groupinfo $arr[0] for $ret"); continue; }
			
			if(count($arr) > 1) {
				array_shift($arr);
				$ret[] = array('ou' => $ogs[0], 'grp' => $ogs[1], 'state' => $ogs[2], 'perms' => $this->getPermsArray($arr));
			} else
				$ret[] = array('ou' => $ogs[0], 'grp' => $ogs[1], 'state' => $ogs[2], 'perms' => array());
		}
		
		//echo 'getgroupinfo: '; var_dump($ret); echo '<br>';
		return $ret;
	}
	
	public function getOrgGroups($org_array) {
		if(empty($org_array))
			return array();

		$output = $this->sendRequest(array_merge(array("getorggroups"), $org_array));
		$ret = array();
		foreach($this->explode_noempty(',', $output) as $group) {
			$arr = explode(' ', $group);
			if(count($arr) != 2) { $this->debug("OrgGroups: invalid group name $group"); continue; }
			$ret[$arr[0]] = $arr[1];
		}
		return $ret;
	}
	
	public function getOrgsOwnedBy($uid) {
		if(!$uid) return array();
		$ret = $this->explode_noempty(',',$this->sendRequest(array("getorgsownedby", $uid)));
		//echo "getOrgsOwnedBy($uid): "; var_dump($ret); echo '<br>';
		return $ret;
	}
	
	public function getMemberCount($group_array) {
		if(empty($group_array))
			return array();
			
		$ret = array();
		$output = $this->sendRequest(array_merge(array('getmembercount'), $this->serialize($group_array)));
		foreach($this->explode_noempty(',', $output) as $group_count) {
			$arr = explode(' ', $group_count);
			if(count($arr) != 3) { $this->debug("GetMemberCount: invalid member count $group_count in $output"); continue; }
			$ret[]= array('ou' => $arr[0], 'grp' => $arr[1], 'count' => (int)$arr[2]);
		}
		return $ret;
	}
	
	public function getGroupMembers($group_array) {
		if(empty($group_array))
			return array();
		
		return $this->parseMemberInfo($this->sendRequest(array_merge(array("getgroupmembers"), $this->serialize($group_array))));
	}
	
	public function getUserNames($uids) {
		if(empty($uids))
			return array();

		$ret = array();
		$uidnames = $this->explode_noempty(',',$this->sendRequest(array_merge(array("getusernames"), $uids)));
		foreach($uidnames as $uidname) {
			$arr = explode(' ', $uidname);
			if(count($arr) != 2) { $this->debug("invalid uidname $uidname"); var_dump($uidnames); continue; }
			$ret[]= array('uid' => $arr[0], 'name' => $arr[1]);
		}
		return $ret;
	}
};

?>