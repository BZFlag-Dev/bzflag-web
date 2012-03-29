<?php
/**
 * Dependency Injection Wrapper for the Qore Framework
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
 * Load our Dependency Injector Container, and configure it
 */
require_once ROOT . DS . 'libraries' . DS . 'Pimple' . DS . 'pimple.php';

//the Dependency Injectrion Static Class that is globally available
abstract class IoC {
    private static $container;

    public static function init() {
        if (is_null(self::$container)) {
            self::$container = new \Pimple();
        }
    }

    public static function Register($key, \Closure $c, $share = false) {
        if ($share) {
            self::$container[$key] = self::$container->share($c);
        } else {
            self::$container[$key] = $c;
        }
    }

    public static function Get($key) {
        return self::$container[$key];
    }
}

//init the IoC Container to make it available everywhere and ready to use
IoC::init();