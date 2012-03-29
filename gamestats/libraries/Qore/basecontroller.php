<?php
/**
 * BaseController for the Qore Framework
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
 * Default empty controller - we will use this controller whenever we do not need twig
 * example (RSS, JSON, XML...)
 *
 * @author Ian Farr
 */
class BaseController {
    /**
     * private property controlling execution permissions of methods
     * by default the constructor initializes this as false
     * this means the no publicly accesable methods have the permission to execute
     */
    protected $allowedExecution;
    
    protected $httpQuery;
    protected $httpQueryData;
    protected $httpPost;
    protected $httpPostData;
    
    /**
     *
     * @var \Zend_Cache_Core 
     */
    protected $cache;
    
    /**
     * @var \Zend_Cache_Frontend_Page 
     */
    private $pCache;
    
    /**
     * @var \Qore\SessionBase
     */
    protected $session;
    
    public function __construct() {
        $this->allowedExecution = false;
        $this->httpQuery = false;
        $this->httpPost = false;
        
        if ($GLOBALS['cfg']['sessions']['enabled']) {
            $this->session = IoC::Get('qore.session.handler');
        }
        
        $this->cache = \Zend_Cache::factory(
                'Core',
                'File',
                array('lifetime' => $GLOBALS['cfg']['cache']['time'], 'automatic_serialization' => true),
                array('cache_dir' => ROOT . DS . 'tmp' . DS . 'cache')
        );
        
        $this->pCache = \Zend_Cache::factory(
                'Page',
                'File',
                array('lifetime' => $GLOBALS['cfg']['pageCache']['time'],
                    'debug_header' => false,
                    'default_options' => array(
                        'cache' => true,
                        'cache_with_session_variables' => true,
                        'cache_with_cookie_variables' => true,
                        'cache_with_get_variables' => true,
                        'cache_with_post_variables' => true,
                        'make_id_with_session_variables' => false,
                        'make_id_with_cookie_variables' => false,
                        'make_id_with_get_variables' => true,
                        'make_id_with_post_variables' => true
                    )
                ),
                array('cache_dir' => ROOT . DS . 'tmp' . DS . 'cache')
        );
    }
    
    /**
     * the main pre hook for the entire controller.
     * Each method will have the pre hook executed before the method is executed 
     */
    public function __pre() {}
    
    /**
     * the post hook for the entire controller.
     * Each method will have the post hook executed after the method executes. 
     */
    public function __post() {}
    
    /**
     * Returns the state of $allowedExecution 
     */
    public function getExecutionState() {
        return $this->allowedExecution;
    }
    
    /**
     * Sets the execution state
     * @param bool $state
     */
    public function setExecutionState($state) {
        $this->allowedExecution = $state;
    }
    
    /**
     * setter for httpQuery
     * 
     * @param bool $bool 
     */
    public function setHttpQuery($bool) {
        $this->httpQuery = $bool;
    }
    
    /**
     * setter for httpPost
     * 
     * @param bool $bool 
     */
    public function setHttpPost($bool) {
        $this->httpPost = $bool;
    }
    
    /**
     * setter for httpQueryData
     * 
     * @param array $data 
     */
    public function setHttpQueryData(array $data) {
        $this->httpQueryData = $data;
    }
    
    /**
     * setter for httpPostData
     * @param array $data 
     */
    public function setHttpPostData(array $data) {
        $this->httpPostData = $data;
    }
    
    public function cachePage() {
        if ($GLOBALS['cfg']['environment']!== 'dev') {
            $this->pCache->start();
        }
    }
    
    /**
     * Redirects the client to the given URL
     * 
     * @param string $url 
     */
    static public function redirectToUrl($url) {
        header("Location: " . $url);
        exit;
    }
    
    /**
     * Redirects the client to the given controller and method
     * the URL will be created for you
     * 
     * @param string $controller 
     * @param string $method 
     * @param string $args 
     */
    static public function redirect($controller, $method, $args = '') {
        if (strlen($args)>0) {
            $location = SITE_ROOT . "/" . $controller . "/" . $method . "/" . $args;
        } else {
            $location = SITE_ROOT . "/" . $controller . "/" . $method;
        }
        
        header("Location: " . $location);
        exit;
    }
}