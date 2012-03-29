<?php
/**
 * Database Session Handler for the Qore Framework
 * Copyright (C) 2012  Ian Farr
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace Qore;

/**
 * The database session handler
 * 
 * @author Ian Farr 
 */
class DbSessionHandler extends \Qore\SessionBase {
    private $db;
    
    public function __construct(\Qore\Unreal\iDbSessions $iDBSess) {
        parent::__construct();
        $this->db = $iDBSess;
    }
    
    public function open($save_path, $sessionid) {
        return(true);
    }
    
    public function close() {
        return true;
    }
    
    public function read($sessionid) {
        $result = $this->db->read($sessionid);
        if ($result) {
            return $result['SessionData'];
        } else {
            return '';
        }
    }
    
    public function write($sessionid, $sessiondata) {
        if ($this->isValid()) {
            $this->db->write($sessionid, $sessiondata);
        }
    }
    
    public function sessionExists($sessionid) {
        return $this->db->sessionExists($sessionid);
    }
    
    public function destroy($sessionid) {
        return $this->db->destroy($sessionid);
    }
    
    public function gc($maxlifetime) {
        return $this->db->gc($maxlifetime);
    }
}