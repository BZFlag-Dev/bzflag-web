<?php
/**
 * Error Controller for the Qore Framework
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
 * Default Error Controller
 *
 * @author Ian Farr
 */
class Error extends Controller {
    private $err;
    private $OK;
    private $BadRequest;
    private $Unauthorized;
    private $Forbidden;
    private $NotFound;
    private $InternalError;
    private $NotImplemented;
    private $httpErrString;
    
    public function __construct() {
        parent::__construct();
        
        $this->OK = "200 OK";
        $this->BadRequest = "400 Bad Request";
        $this->Unauthorized = "401 Unauthorized";
        $this->Forbidden = "403 Forbidden";
        $this->NotFound = "404 Not Found";
        $this->InternalError = "500 Internal Server Error";
        $this->NotImplemented = "501 Not Implemented";
        
        if ($GLOBALS['cfg']['error']['fastcgi']) {
            $this->httpErrString = "Status: ";
        } else {
            $this->httpErrString = "HTTP/1.0 ";
        }
    }
    
    public function displayErr($url, \Exception $e) {
        if ($e->getCode() === 0 ) {
            $this->err = \Qore\Qexception::$InternalError;
        } else {
            $this->err = $e->getCode();
        }

        //builds & sets the http response header for the appropriate error message
        switch ($this->err) {
            case 200: header($this->httpErrString . $this->OK, true, $this->err); break;
            case 400: header($this->httpErrString . $this->BadRequest, true, $this->err); break;
            case 401: header($this->httpErrString . $this->Unauthorized, true, $this->err); break;
            case 403: header($this->httpErrString . $this->Forbidden, true, $this->err); break;
            case 404: header($this->httpErrString . $this->NotFound, true, $this->err); break;
            case 500: header($this->httpErrString . $this->InternalError, true, $this->err); break;
            case 501: header($this->httpErrString . $this->NotImplemented, true, $this->err); break;
            default: header($this->httpErrString . $this->InternalError, true, $this->err); break;
        }
        
        if ($GLOBALS['cfg']['error']['seperatePages']) {
            echo $this->twig->render("qore/error/error$this->err.html.twig", array('url' => $url,'error' => $e->getMessage()));
        } else {
            echo $this->twig->render('qore/error/error.html.twig', array('url' => $url,'error' => $e->getMessage()));
        }
    }
}