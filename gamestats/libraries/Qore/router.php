<?php
/**
 * router/dispatcher for the Qore Framework
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
 * Controls where URL's get routed to
 *
 * @author Ian Farr
 */
class Router {
    private $url;
    private $urlArray;
    private $routes;
    private $defaultController;
    private $defaultMethod;
    private $defaultArgs;
    private $requestedController;
    private $requestedMethod;
    private $requestedArgs;
    private $controllerPath;
    private $queryData;
    private $postData;
    
    /**
     * @var \Qore\Basecontroller
     */
    private $controller;
    
    public function __construct($uri, $defaultController, $defaultMethod, array $args = array()) {
        //assign the current URL
        if (SITE_SUBDIR == '') {
            $this->url = strtolower($uri);
        } else {
            //if we are in a subdir - we strip that from the URL, so we don't think that it is the controller
            $this->url = str_replace(SITE_SUBDIR, '', strtolower($uri));
        }
        
        //initialize the routes array
        $this->routes = array();
        
        //initializet the default controller path
        $this->controllerPath = array();
        
        //initialize the default route
        $this->defaultController = $defaultController;
        $this->defaultMethod = $defaultMethod;
        $this->defaultArgs = $args;

        //init other vars
        $this->queryData = array();
        $this->postData = array();
        
        //create the default controller path
        foreach ($GLOBALS['cfg']['packs'] as $pack) {
            $controllerDir = ROOT . DS . 'packs' . DS . strtolower($pack) . DS . 'controllers';
            if (file_exists($controllerDir)) {
                $this->controllerPath[] = $controllerDir;
            }
        }
    }
    
    
    /**
     * Private Method for getting the full path of a controller
     * This will return the first matched controller from the controllerPath
     * 
     * @param string $controllerName
     * @return string valid path for a potential controller
     */
    private function getControllerPath($controllerName) {
        foreach ($this->controllerPath as $path) {
            if (file_exists($path . DS . $controllerName . ".php")) {
                return $path . DS . $controllerName . ".php";
            }
        }
        return false;
    }
    
    /**
     * Private Method for getting the full class name of a given controller 
     * 
     * @param string $controllerName
     * @return string the class name for a potential conroller
     */
    private function getClassName($controllerName) {
        return ucfirst($controllerName) . 'Controller';
    }
    
    /***
     * Private method to clean the urlArray()
     * 
     * remove blank elements and get rid of other nasties in the URL
     * 
     * @param bool $doMatch if we should do the preg_match to check for valid characters in the URL
     *                      default is true.
     */
    private function cleanUrlArray(array &$array, $doMatch = true) {
        $removedEntries = false;
        //iterate throught he array
        foreach ($array as $key => $val) {
            //remove empty elements
            if ($val == '') { 
                unset($array[$key]);
                $removedEntries = true;
            } else {
                if ($doMatch) {
                    //we have data - make sure that we only have allowed characters in the element
                    if (preg_match("/[^" . $GLOBALS['cfg']['allowedUrlChars'] . "]/", $array[$key])) {
                        //we have non-allowed characters in this URL element..get rid of it.
                        unset($array[$key]);
                        $removedEntries = true;
                    }
                }
            }
        }
        if ($removedEntries) {
            //re-index the array so all the indexes ar ein order
            $array = array_values($array);
        }
    }
    
    /**
     * Private method that will prepare and initiate a clean on the URL array
     */
    private function prepareUrlArray() {
        //strip the leading / off the URL - we don't need that for the urlArray
        $path = substr($this->url, 1);
        
        //remove any double slashes - this causes all sorts of weirdness..
       
        //parse the URL into an array, splitting by '/'
        $this->urlArray = explode('/' , $path);
        
        //make sure all the URL elements are clean (no empty elements or other nasties)
        $this->cleanUrlArray($this->urlArray);
    }
    
    /**
     * Private Method compares the current URL with predefined matches
     * Tries to see which pre-configured controller::method should be chosen for a given URL
     * 
     * @return boolean True on match found, False on no match found
     */
    private function matchRoutes() {
        //check to see if this is the defaut route
        if ($this->url === "/") {
            $this->requestedController = $this->defaultController;
            $this->requestedMethod = $this->defaultMethod . '_public';
            $this->requestedArgs = $this->defaultArgs;
            return true;
        }
        //loop through the routes array, looking for matches
        foreach ($this->routes as $route) {
            //normalize $route['path'] so that it is a valid regex
            //we look for matches at the start of the URL only string only
            $normRegex = strtolower('/^'. str_replace("/", "\/", $route['path']). '/');
            if (preg_match($normRegex, $this->url)) {
                $this->requestedController = $route['controller'];
                $this->requestedMethod = $route['method'] . '_public';
                if ($route['forceArgs']) {
                    $this->requestedArgs = $route['args'];
                } else {
                    //strip the matched URL portion off of the URL
                    $chopUrl = str_replace(strtolower($route['path']), "", $this->url);
                    
                    //convert it to an array
                    //parse the URL into an array, splitting by '/' and make sure it's cleaned
                    $this->urlArray = explode('/' , $chopUrl);
                    $this->cleanUrlArray($this->urlArray);
                    
                    //set that as the $args
                    $this->requestedArgs = $this->urlArray;
                }
                return true;
            }
        }
        return false;
    }
    
    /**
     * Private Function which hands this request over to the proper controller::method
     * or throws on error if no valid controller:match is found for this reuqest.
     * 
     * @param boolean $match
     * @throws Exception on no controller::method match found for given URL 
     */
    private function processController($match) {
        $controllerFile = $this->getControllerPath($this->requestedController);
        
        if ($controllerFile !== false) {
            //load the controller php file
            require($controllerFile);
            
            //check to see if the correct class exists
            $class = $this->getClassName($this->requestedController);
            if (class_exists($class, false)) {
                $this->controller = new $class;
                if ($match) {
                    //the requested url, matches a route - so map it all now
                    if (method_exists($this->controller, $this->requestedMethod)) {
                        //execute the requested method and pass in urlArray as args
                        $this->callController($this->requestedMethod);
                    } else {
                        throw new \Exception("public method: $this->requestedMethod not found in $controllerFile");
                    }
                } else {
                    //the requsted url does not match a predefined route
                    //we don't know if the next request is a method or argument...
                    if (count($this->urlArray)>0) {
                        //we have more elements - see if the next one matches a method name
                        $publicMethod = $this->urlArray[0].'_public';
                        if (method_exists($this->controller, $publicMethod)) {
                            //shift the array over to get at the args
                            array_shift($this->urlArray);
                            
                            //assign the args
                            $this->requestedArgs = $this->urlArray;
                            
                            //execute the method - pass args
                            $this->callController($publicMethod);
                        } else {
                            $method = 'main_public';
                            //method does not exist, execute the 'main_public' method, and pass the urlArray
                            if (method_exists($this->controller, $method)) {
                                $this->requestedArgs = $this->urlArray;
                                $this->callController($method);
                            } else {
                                //no methods found for controller
                                throw new \Exception("No valid methods could be found for class '$class'.");
                            }
                        }
                    } else {
                        //there are no more url elements - just load default method
                        $method = 'main_public';
                        //method does not exist, execute the 'main_public' method
                        if (method_exists($this->controller, $method)) {
                            $this->requestedArgs = array();
                            $this->callController($method);
                        } else {
                            //no methods found for controller
                            throw new \Exception("No valid methods could be found for class '$class'.");
                        }
                    }
                }
            } else {
                throw new \Exception("class '$class' could not be found!");
            }
        } else {
            throw new \Exception("Unable to find the '$this->requestedController' controller!");
        }
    }
    
    /**
     * Calls all the appropriate controller methods for the requested controller
     * this function should only ever be called after validating that the controller and method exist
     * 
     * @param BaseController $this->controller
     * @param string $method 
     */
    private function callController($method) {
        $queryDetected = false;
        
        //the urlArray contains arguments (or nothing) at this point
        //so loop through it - see if we have any request qureies (?key=val..)
        if ($this->urlArray) {
            foreach($this->urlArray as $index => $arg) {
                if (preg_match("/\?/", $arg)) {
                    //we have found an httpquery
                    $queryDetected = true;

                    //separate by ? (separates the query string from a base argument)
                    $query = explode('?', $arg);

                    //blank out this index...cleanUrlArray will take care of it later..
                    $this->urlArray[$index] = '';

                    //$query[1] contains a list of key=val pairs - split by & to get them all
                    $keyVals = explode('&', $query[1]);

                    //loop the key val pairs and assign them...
                    foreach($keyVals as $val) {
                        $keyVal = explode('=', $val);

                        //see if we have something in $query[0]
                        //this could be a name we want to associate the url queries with...
                        if ($query[0]) {
                            $this->queryData[$query[0]][$keyVal[0]][] = $keyVal[1];
                        } else {
                            $this->queryData[$keyVal[0]][] = $keyVal[1];
                        }
                    }
                }
            }
        }
        
        //if we have found queries..
        if ($queryDetected) {
            //clean the urlArray again (but don't look for regex - we already did that...
            $this->cleanUrlArray($this->urlArray, false);
            //tell the controller we have found queries
            $this->controller->setHttpQuery(true);
            //give the controller the queries
            $this->controller->setHttpQueryData($this->queryData);
            //re-assign $this->requestedArgs
            $this->requestedArgs = $this->urlArray;
        }
        
        //Check to see if we have any post data..
        if ($_POST) {
            //clean the post array...
            $this->cleanUrlArray($_POST);
            //we have post data - tell the controller and assign it
            $this->controller->setHttpPost(true);
            $this->controller->setHttpPostData($_POST);
        }
        
        //execute the pre methods for the requested controller/method
        $this->controller->__pre();

        //see if the there is a pre method for the requested method, and call it
        $preMethod = $method . '_pre';
        if (method_exists($this->controller, $preMethod)) {
            $this->controller->$preMethod($this->requestedArgs);
        }
        
        //see if we are allowed to execute the method
        if ($this->controller->getExecutionState()) {
            //we are alowed, call it
            $this->controller->$method($this->requestedArgs);
            
            //see if the there is a post method for the requested method, and call it
            $postMethod = $method . '_post';
            if (method_exists($this->controller, $postMethod)) {
                $this->controller->$postMethod();
            }

            //call the controllers post method
            $this->controller->__post();
        } else {
            throw new \Exception("you do not have permissions to execute this page");
        }
    }
    
    /**
     * Public method to register a pre-configured URL -> controller::method mapping
     * 
     * @param bool $forceArgs   true/false, should we use the provided $args (and ignore the ones in the URL) or not
     * @param string $url_path the relative URL to match against (ex: /news)
     * @param string $controllerName the controller to use for the $web_path url.
     * @param string $method which method should be called
     * @param array $args an array of arguments to pass the method
     */
    public function registerRoute($forceArgs, $url_path, $controllerName, $method, array $args = array()) {
        $this->routes[] = array('forceArgs' => $forceArgs, 'path' => $url_path, 'controller' => $controllerName, 'method' => $method, 'args' => $args);
    }
    
    /**
     * Public method to push a new controller search location into the controllerPath array.
     * @param type $path 
     */
    public function registerControllerPath($path) {
        $this->controllerPath[] = $path;
    }
    
    /**
     * Public method which processes the routes, and figures out which controller::method to use for this request 
     */
    public function route() {
        //see if the current URL matches a route rule
        if ($this->matchRoutes()) {
            $this->processController(true);
        } else {
            //no route rule found...prep the URL array for processing
            $this->prepareUrlArray();
            
            //assign the requested controller
            $this->requestedController = $this->urlArray[0];

            //drop the requested controller from the urlArray
            array_shift($this->urlArray);

            //let callController figure out the rest, as we do not know if the
            //remaining elements are arguments or methed calls...
            $this->processController(false);
        }
    }
}