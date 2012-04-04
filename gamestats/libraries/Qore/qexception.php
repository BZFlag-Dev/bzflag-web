<?php
/**
 * Base Model for the Qore Framework
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
 * The Qore Exception object
 * All exceptions thrown should use this class
 * as it will map to HTTP errors which \Qore\Error displays
 *
 * @author Ian Farr
 */
class Qexception extends \Exception {
    static $OK = 200;
    static $BadRequest = 400;
    static $Unauthorized = 401;
    static $Forbidden = 403;
    static $NotFound = 404;
    static $InternalError = 500;
    static $NotImplemented = 501;
    
    //we must contain an error message and an error code
    public function __construct($message, $code, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}