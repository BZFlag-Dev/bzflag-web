<?php
/**
 * Base Controller for the Qore Framework
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
* Include the Twig templating environment for oue views
*/
require ROOT . DS . 'libraries' . DS . 'Twig' . DS . 'Autoloader.php';


/**
 * Base Controller for all Controllers
 *
 * @author Ian Farr
 */
class Controller extends BaseController {
    
    /**
     * 
     * @var Twig_Loader_Filesystem
     */
    protected $loader;
    
    /**
     *
     * @var Twig_Environment 
     */
    protected $twig;
    
    public function __construct() {
        parent::__construct();

        //initialize the twig environment
        \Twig_Autoloader::register();
        
        //create the view path that we should use
        $viewPath = array();
        
        //loop through the registered packs and add it's view direcory
        foreach ($GLOBALS['cfg']['packs'] as $pack) {
            $viewDir = ROOT . DS . 'packs' . DS . $pack . DS . 'views';
            if (file_exists($viewDir)) {
                $viewPath[] = $viewDir;
            }
        }
        
        //$the default view path goes at the end
        $viewPath[] = ROOT . DS . 'views';
        
        //load twig up..
        $this->loader = new \Twig_Loader_Filesystem($viewPath);
        
        //if we aren't in a dev environment, we assume prod
        //for dev we set things up for debugging, and no caching
        if ($GLOBALS['cfg']['environment'] === "dev") {
            $this->twig = new \Twig_Environment($this->loader, array(
                'cache' => false,
                'debug' => true
            ));
            $this->twig->addExtension(new \Twig_Extension_Debug());
        } else {
            $this->twig = new \Twig_Environment($this->loader, array(
                'cache' => ROOT . DS . 'tmp' . DS . 'viewcache'
            ));
        }
    }
}