<?php
/**
 * Bootstrap file for the Qore Framework
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
 * Autoloaders for Qore Components
 */
function autoload_Qore($name) {
    //make sure that it is a Qore file that we are trying to include
    if (strpos($name, 'Qore') !== false) {
        //replace the namespace devidor with the directory separator
        $file = ROOT . DS . 'libraries' . DS . str_replace("\\", DS, ucfirst(strtolower($name))).'.php';
        if (file_exists($file)) {
            require($file);
        }
    } else {
        return;
    }
}

/**
 * Autoloader for our Pack's
 */
function autoload_Packs($name) {
    //make sure that it is a Model file that we are trying to include
    if (strpos($name, 'Packs') !== false) {
        //replace the namespace devidor with the proper directory separator
        //replace Model, with models so we know to look into the model dir, and follow the namespace under that properly.
        $file = ROOT . DS . str_replace("\\", DS, strtolower($name)).'.php';
        if (file_exists($file)) {
            require($file);
        }
    } else {
        return;
    }
}

/**
 * autoloader for Zend classes
 */
function autoload_Zend($name) {
    //make sure that it is a Model file that we are trying to include
    if (strpos($name, 'Zend') !== false) {
        //replace the namespace devidor with the proper directory separator
        //replace Model, with models so we know to look into the model dir, and follow the namespace under that properly.
        $file = ROOT . DS . 'libraries' . DS . str_replace("_", DS, $name).'.php';
        if (file_exists($file)) {
            require($file);
        }
    } else {
        return;
    }
}

/**
 * register the autoloaders
 */
spl_autoload_register('autoload_Qore');
spl_autoload_register('autoload_Packs');
spl_autoload_register('autoload_Zend');

/**
 * set the include path for 3rd party libraries 
 */
set_include_path(get_include_path() . PATH_SEPARATOR . ROOT . DS . 'libraries');

/**
 * Load up our D.I. container for the site
 */
require_once ROOT . DS . 'libraries' . DS . 'Qore' . DS . 'ioc.php';

/**
 * Load the configuration file
 */
require_once ROOT . DS . 'config' . DS . 'config.php';

/**
 * our centralized page level cache for the application
 * Please note that it may be easier to just use your 
 * controllers $this->cachePage(); method
 * 
 * Doing it here in the bootstrap file is a little faster
 * and gives you more control of cache settings, but you 
 * must specify all URL's that are cachable here.
 * 
 * Doing it in the controllers <method>__pre(){} allows
 * you to easily control which pages are cached at the
 * controller/method level
 */
//if ($GLOBALS['cfg']['environment'] == 'dev') {
//    $debug = true;
//} else {
//    $debug = false;
//}
//
////at this point, the session does not exist yet, but cookies may be present
//$pageCacheOptions = array(
//    'lifetime' => 300,
//    'debug_header' => $debug,
//    'default_options' => array(
//        'cache' => true,
//        'cache_with_cookie_variables' => true
//    ),
//    'regexps' => array(
//       // cache Front Page
//       '^/$' => array(
//                'cache' => true,
//                'make_id_with_cookie_variables' => false
//           ),
// 
//       // cache bzstatsController
//       //'^/bzstats/' => array('cache' => true)
//    )
//);
//
//$pageCache = \Zend_Cache::factory(
//        'Page',
//        'File',
//        $pageCacheOptions,
//        array('cache_dir' => ROOT . DS . 'tmp' . DS . 'cache')
//);
//
//$pageCache->start();


/**
 * Load up the session handler if enabled
 */

//add the session handler Factories/Functions to our D.I. container
if ($GLOBALS['cfg']['sessions']['enabled']) {
    \Qore\IoC::Register(
        'qore.db.sessions.adapter',
        function ($c) {
            $db = $GLOBALS['cfg']['sessions']['dbinstance'];
            return \Qore\Factory\DbFactory::build('', $db, 'sessions', $c["db.$db.connection"]);
        },
        true
    );

    \Qore\IoC::Register(
        'qore.db.sessions',
        function ($c) {
            return new \Qore\DbSessionHandler($c['qore.db.sessions.adapter']);
        },
        true
    );
        
    //register our session object with our D.I. container
    \Qore\IoC::Register(
        'qore.session.handler',
        function ($c) {
            switch (strtolower($GLOBALS['cfg']['sessions']['type'])) {
                case 'file':
                    return new \Qore\FileSessionHandler();
                    break;
                case 'db':
                    return $c['qore.db.sessions'];
                    break;
                default:
                    return new \Qore\FileSessionHandler();
                    break;
            }
        },
        true
    );
        
     // create the instance of our Session Handler
    $sessionHandler = \Qore\IoC::Get('qore.session.handler');
    
    //register it with PHP
    session_set_save_handler(
        array(&$sessionHandler, "open"),
        array(&$sessionHandler, "close"),
        array(&$sessionHandler, "read"),
        array(&$sessionHandler, "write"),
        array(&$sessionHandler, "destroy"),
        array(&$sessionHandler, "gc")
    );
    
    $sessionHandler->start();
}