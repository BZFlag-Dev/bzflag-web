<?php
//grab the current URI
$url = $_SERVER['REQUEST_URI'];

//create our router with the default route for site root
$router = new \Qore\Router($url, 'bzstats', 'main');

//if you need controllers outside of a pack for whatever reason..
//$router->registerControllerPath(ROOT . DS . 'controllers');

//assign some hardcoded routes (urls mapped to controllers/methods)
//  if $forceArgs is true, then the $args array that you pass will be used for all URLs that start with $web_path
//  if $forceArgs is false, then all URL elements after $web_path  are passed to the method as $args
//$router->registerRoute($forceArgs, $web_path, $controllerName, $method, $args);
//
//example 1 -   the below sends <siteURL>/foo/bar/anything/else/after to controller default, method main, and forces the passed array
//              to be used as the data(argument) sent to the main method (default->main(array(...)):
//$router->registerRoute(true, "/foo/bar", "default", "main", array('this', 'is', 'our', 'data'));
//
//example 2 -   the below sends <siteURL>/foo/bar/anything/else/after to controller default, method main, and uses all the passed URL
//              segments to be used as the data(argument) sent to the main method (default->main(array('anything', 'else' ...)):
//$router->registerRoute(false, "/foo/bar", "default", "main");

//register our /api route to handle ajax requests
$router->registerRoute(false, '/bzstatsapi', 'bzstatsapi', 'dispatcher');

//process the routes
try {
    $router->route();
} catch ( Exception $e ) {
    $errPage = new Qore\Error();
    $errPage->displayErr($url, $e);
}