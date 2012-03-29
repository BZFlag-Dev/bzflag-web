<?php
/**
 * Filesystem Session Handler for the Qore Framework
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

class FileSessionHandler extends \Qore\SessionBase  {
    public function __construct() {
        parent::__construct();
        session_save_path(ROOT . DS . 'tmp' . DS . 'sessions');
    }
    
    public function open($save_path, $sessionid) {
        return(true);
    }
    
    public function close() {
        return true;
    }
    
    public function read($sessionid) {
        $sess_file = session_save_path() . DS . $sessionid . $GLOBALS['cfg']['sessions']['extension'];
        return (string) @file_get_contents($sess_file);
    }
    
    public function write($sessionid, $sessiondata) {
        if ($this->isValid()) {
            $sess_file = session_save_path() . DS . $sessionid . $GLOBALS['cfg']['sessions']['extension'];
            if ($fp = @fopen($sess_file, "w")) {
                $return = fwrite($fp, $sessiondata);
                fclose($fp);
                return $return;
            } else {
                return(false);
            }
        }
    }
    
    public function sessionExists($sessionid) {
        $sess_file = session_save_path() . DS . $sessionid . $GLOBALS['cfg']['sessions']['extension'];
        if (file_exists($sess_file)) {
            return true;
        }
        return false;
    }
    
    public function destroy($sessionid) {
        $sess_file = session_save_path() . DS . $sessionid . $GLOBALS['cfg']['sessions']['extension'];
        return(@unlink($sess_file));
    }
    
    public function gc($maxlifetime) {
        foreach (glob(session_save_path() . DS . "*" . $GLOBALS['cfg']['sessions']['extension']) as $filename) {
            if (filemtime($filename) + $maxlifetime < time()) {
                @unlink($filename);
            }
        }
        return true;
    }
}