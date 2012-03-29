<?php
/**
 * Main application entry point for the Qore Framework
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

/**
 * Defines Used By The Application
 */
//the directory sepaator used by this system
define('DS', DIRECTORY_SEPARATOR);

//the project root directory file system location (parent directory of webroot)
define('ROOT', dirname(dirname(__FILE__)));

//the webroot directory file system location
define('WEBDIR_ROOT', ROOT . DS . 'webroot');

//the virtual directory/alias that we are in (if any)
//this should contain a leading slash (ex: /mysite)
define('SITE_SUBDIR', '');

//detect if we are in https or now
if (array_key_exists('HTTPS', $_SERVER)) {
    define('HTTP_PROTO', 'https://');
} else {
    define('HTTP_PROTO', 'http://');
}

//the URL used to access the website publicly
define('SITE_ROOT', HTTP_PROTO . $_SERVER['HTTP_HOST'] . SITE_SUBDIR);

/**
 * bootstrap the system 
 */
require ROOT . DS . 'config' . DS . 'bootstrap.php';

/**
 * load up the router, and hand over control
 */
require ROOT . DS . 'config' . DS . 'routes.php';