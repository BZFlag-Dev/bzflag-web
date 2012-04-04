<?php

/**************************
 * the chatacters that are permitted in a URL segment
 * a segment is split by a forward slash - so everything 
 * inbetween a set of forward slashes in a URL is considered 
 * 1 segment. all segmenst that have illegal characters are 
 * deleted before any controller/logic can even see it.
 * 
 * Please be carefull here!
 * 
 * This is passed to a PCRE - so please escape(\) any regex 'special' characters
 */
$cfg['allowedUrlChars'] = 'a-zA-Z0-9&=\-_\+\.:\%\?';


/**************************
 * Set to 'prod' for production, or 'dev' for development environments
 * 
 * This controls things like template caching and debugging
 */
$cfg['environment'] = "dev";


/**************************
 * DB Variables 
 * 
 * !IMPORTANT!
 *      when adding a new DB identity, please add the appropriate IoC::Regsiter() below it.
 *      One has alreay been added for the "default" identity.
 *      
 *      Example:
 *          if you add: $cfg['db']['MyOtherDBServer']...
 *      
 *          then you would also need to IoC::Register() "MyOtherDBServer"
 *          it is safe to simply copy the default one, and replace all instances
 *          of the word 'default' with your other db-identity.
 */
//$cfg['db']['default']['type']      = "mysql";
//$cfg['db']['default']['host']      = "localhost";
//$cfg['db']['default']['port']      = 3306;
//$cfg['db']['default']['database']  = "bzstats";
//$cfg['db']['default']['user']      = "bzstats";
//$cfg['db']['default']['password']  = "bzstats";

$cfg['db']['default']['type']      = "mysql";
$cfg['db']['default']['host']      = "127.0.0.16";
$cfg['db']['default']['port']      = 3306;
$cfg['db']['default']['database']  = "bzstats";
$cfg['db']['default']['user']      = "bzstats";
$cfg['db']['default']['password']  = "bzstats";

$cfg['db']['sessions']['type']      = "mysql";
$cfg['db']['sessions']['host']      = "127.0.0.16";
$cfg['db']['sessions']['port']      = 3306;
$cfg['db']['sessions']['database']  = "sessions";
$cfg['db']['sessions']['user']      = "bzstats";
$cfg['db']['sessions']['password']  = "bzstats";

/**
 * Each $cfg['db'][<db-identity>] keyname should have it's connection
 * object registered with the Dependency Injection container
 * so that it may be used application wide easily and re-used/shared. 
 */
\Qore\IoC::Register(
    'db.default.connection',
    function () {
        return \Qore\Factory\DbConFactory::build('default');
    },
    true
);
    
\Qore\IoC::Register(
    'db.sessions.connection',
    function () {
        return \Qore\Factory\DbConFactory::build('sessions');
    },
    true
);
    
/**************************
* Sessions
*/
    
//should we use sessions?
$cfg['sessions']['enabled']     = true;

//'file' or 'db' sessions?
//  'file' sessions are written to the applications tmp/sessions folder
$cfg['sessions']['type']        = "db";

//if $cfg['sessions']['type'] = 'file', 
//  what should the file extension be?
$cfg['sessions']['extension']  = ".session";

//if $cfg['sessions']['type'] = 'db', 
//  which db instance should we use?
$cfg['sessions']['dbinstance']  = "sessions";

//the dns name of this site
//  the session cookie will only work with the configured URL
$cfg['sessions']['domain']      = $_SERVER['HTTP_HOST'];

//the name of our applications session cookie
$cfg['sessions']['name']        = "bzstats";

//the path where the session cookie is valid 
//  (/ means the entire site)
$cfg['sessions']['path']        = "/";

//max lifespan of server-side session in seconds
//this is the time since the last update of a session
//everything older will be deleted by the garbage collector
//as well as being rejected if a client attempts to re-establish
//a session that is too old
$cfg['sessions']['lifeTime']    = 7200;

//should we calculate the lifetime from session start
// or from last session update?
// valid values are:
//  lastUpdate      session lifetime is based on the last update timestamp of the session
//  createTime      session lifetime is based on session createTime
//
// for example, if $cfg['sessions']['lifeTime'] = 7200 (2 hours)
//  lastUpdate would mean the the session would have to not be used for a full 2 hours before it is killed
//  createTime would mean that the session only has a 2 hour lifeTime from it's creation.
$cfg['sessions']['lifeTimeCalc'] = 'lastUpdate';

//garbage collection runtime probability
//if probability is 1, and divisor is 100
//then PHP's session garbaage collection will run
//once for each 100 requests
$cfg['sessions']['gc_probability'] = 1;
$cfg['sessions']['gc_divisor'] = 100;


//the session cookies lifetime in seconds
//   0 means valid until the browser is closed
$cfg['sessions']['cookieLifeTime'] = 0;

//how often, in requests, to regenerate the session id.
//this can help session hijacking attempts, as it allows
//you to tell Qore to transparently regen the sessionID
//every N requests.
//WARNING:  There is an issue with pages that launch multiple
//          GET requests (through javascript for example). 
//          ONLY use the auto-regen feature if you know you 
//          only have 1 GET request per page refresh. See
//          docs for the reason why.
//
//  1  = regenerate sessionID on every request
//  0  = never auto regenerate sessionID
$cfg['sessions']['regenSessionId'] = 0;

//should we redirect users on invalid session?
//it is probably better to let your authentication/authorization component
//redirect based on permissions rather than at the session level
//it true, this will screw up bookmarks, as invalid (expired) sessions
//will be forced/redirect to $cfg['sessions']['redirectURL']
//
// if false, the old expired session is killed, and a new blank
// session is transparently started.
$cfg['sessions']['redirect'] = false;

//if we are redirecting them, which URL should we redirect to?                                     
$cfg['sessions']['redirectURL']     = SITE_ROOT;

/**************************
* Cache Settings
*/
//the amount of time (in seconds) unique calls/data will be cached for
//your controller's $this->cache object will cache things
//for this amount of time (in seconds)
$cfg['cache']['time'] = 300;

//the amount of time (in seconds) that a whole page will be
//cached for. Use $this->cachePage() method in your controller's
//<method>_public_pre()
$cfg['pageCache']['time'] = 300;


/**************************
 *  Error Page Configuration
 * 
 *  This controls the error page options
 */

// DOCS say that for FASTCGI you need "Status: " HTTP headers,
// while normally you need "HTTP/1.0 " HTTP headers.
// This option controls which one is used in \Qore\Error
$cfg['error']['fastcgi'] = false;

// should we have seperate error pages for each type of error
// if this is true, then you will need to create custom
// twig error pages for each error type.
// the error pages that MUST be created are as follows:
//   ...views\qore\error\error200.html.twig //OK
//   ...views\qore\error\error400.html.twig //Bad Request
//   ...views\qore\error\error401.html.twig //Unauthorized
//   ...views\qore\error\error403.html.twig //Forbidden
//   ...views\qore\error\error404.html.twig //Not Found
//   ...views\qore\error\error500.html.twig //Internal Server Error
//   ...views\qore\error\error501.html.twig //Not Implemented
//
// By default, this is false, and Qore uses a single error template
// which displays the error strings that are passed to it, and simply
// changes the HTTP Response code to the appropriate code that was set
// by the code that threw the error (400, 404, etc..).
// 
// the default error template is called "error.html.twig" and exists
// in the default views folder /views/Qore/error/error.html.twig
// if you want to modify the error pages, copy it to one of
// your Packs' views folder (in <Pack>/views/Qore/error/error.html.twig)
// and modify accordingly. you can also create the individual error files
// in one of your Packs' views folder too - if you desire to use multiple.
$cfg['error']['seperatePages'] = true;




/**************************
* The Packs that we will use
*/
$cfg['packs'] = array('bzstats', 'dygraphviews');

/**
 * register all DI maps for all registered packs
 * 
 * do not modify this without knowing exactly what you are doing
 *      as you may break Qore if you do
 * 
 * please note that even if the Pack uses a $cfg entry that has not been 
 *      defined yet, it is safe to pre-register them
 *      as they are only called when needed (lazy loaded) by your controllers/models
 *      later on, and the $cfg array will be filled in by then
 */
foreach ($cfg['packs'] as $pack) {
    $iocFile = ROOT . DS . 'packs' . DS . strtolower($pack) . DS . 'ioc.php';
    if (file_exists($iocFile)) {
        include $iocFile;
    }
}

/**************************
 *  Inidiviual Pack Configurations
 * 
 *  Please read the README for each pack to see what should be
 *  registered in the $cfg array
 */
/**
 * BZstats Configurations 
 */
$cfg['bzstats']['dbinstance'] = 'default';