<?php
/**
 * Base Session Handler for the Qore Framework
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

abstract class SessionBase  {
    private $id;
    private $userAgent;
    private $started;
    protected $serverSessionExists;
    
    public function __construct() {
        //assign the user agent
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
        
        //initialize $started to false
        $this->started = false;
        
        //initialize $serverSessionExists to false;
        $this->serverSessionExists = false;
        
        //enforce certain PHP session settings
        ini_set('session.auto_start',                   "0");
        ini_set('session.gc_probability',               $GLOBALS['cfg']['sessions']['gc_probability']);
        ini_set('session.gc_divisor',                   $GLOBALS['cfg']['sessions']['gc_divisor']);
        ini_set('session.gc_maxlifetime',               $GLOBALS['cfg']['sessions']['lifeTime']);
        ini_set('session.referer_check',                '');
        ini_set('session.entropy_file',                 '/dev/urandom');
        ini_set('session.entropy_length',               "26");
        ini_set('session.use_cookies',                  "1");
        ini_set('session.use_only_cookies',             "1");
        ini_set('session.use_trans_sid',                "0");
        ini_set('session.hash_function',                "1");
        ini_set('session.hash_bits_per_character',      "4");

        session_cache_limiter('nocache');
        session_set_cookie_params($GLOBALS['cfg']['sessions']['cookieLifeTime'], $GLOBALS['cfg']['sessions']['path'], $GLOBALS['cfg']['sessions']['domain']);
        session_name($GLOBALS['cfg']['sessions']['name']);
    }
    
    public function __destruct() {
        session_write_close();
    }
    
    abstract public function close();
    abstract public function destroy($sessionid);
    abstract public function gc($maxlifetime);
    abstract public function open($save_path, $sessionid);
    abstract public function read($sessionid);
    abstract public function write($sessionid , $sessiondata);
    abstract public function sessionExists($sessionid);
    
    /**
     * Initializes the session and session environment
     */
    public function start() {
        if (!$this->started) {
            //see if the cookie has a valid session ID
            //if not, create a new session
            if (!array_key_exists($GLOBALS['cfg']['sessions']['name'], $_COOKIE)) {
                session_id($this->getNextID());
                session_start();
            } else {
                //see if we have a server-side cache of the requested session
                //if not - kill the session and start a new one
                if (!$this->sessionExists($_COOKIE[$GLOBALS['cfg']['sessions']['name']])) {
                    $this->kill();
                } else {
                    //mark that we have a server-side cache of the session
                    $this->serverSessionExists = true;
                    session_start();
                }
            }

            $this->id = session_id();

            //see if this is a new session
            //new sessions will not have lastSessionUpdate, RegenCount, and fingerprint session vars
            //so set them up
            //fingerprint is the md5sum of the user agent, and the accept-language request header
            //we compare the fingerprint on every request to try and ensure it came from the same browser
            if (!$this->varIsSet('Qore', 'lastSessionUpdate')
                    && !$this->varIsSet('Qore', 'RegenCount')
                    && !$this->varIsSet('Qore', 'fingerprint')) {
                $this->sessionSetup();
            } else {
                //make sure the current session is valid - at least at a basic level
                // compare current fingerprint against the fingerprint store in the session
                // also make sure that the needed session vars exist etc..
                //kill/restart it otherwise
                if ($this->isValid()) {
                     $this->setSessionTimestamp();
                } else {
                    $this->kill();
                }
            }
            
            //mark as started
            $this->started = true;
            
            //make sure we increment our session regeneration counter
            if ($GLOBALS['cfg']['sessions']['regenSessionId'] != 0) {
                $this->set('Qore', 'RegenCount', $this->get('Qore', 'RegenCount') + 1);

                //see if we should regenerate the session id
                if ($this->get('Qore', 'RegenCount') > $GLOBALS['cfg']['sessions']['regenSessionId']) {
                    $this->set('Qore', 'RegenCount', 1);
                    $this->regenerate();
                }
            }
        }
    }
    
    /**
     * clear the current session 
     */
    public function clear() {
        //clear the current session
        $_SESSION = array();
        
        //add the needed session vars
        $this->sessionSetup();
    }
    
    /**
     * Regenerate the Session ID, destroy the old one
     * and copy the old Session contents to the new one
     */
    public function regenerate() {
        if ($this->started) {
            //copy the existing session vars
            $tmpSession = $_SESSION;

            //kill the current session
            // but don't redirect, 
            // or send a deleted cookie to the client
            // this also starts the new session, and sends a new client
            // session cookie with the new session ID
            $this->kill(array('redirect' => false, '$sendDelCookie' => false));

            //restore the session vars into new session
            $_SESSION = $tmpSession;
        }
    }
    
    /**
     * returns the session id
     * @return type 
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * sets the sessions Timestamp
     * Used later to check session validity 
     */
    protected function setSessionTimestamp() {
        $this->set('Qore', 'lastSessionUpdate', time());
    }
    
    /**
     * set up the session with the needed session vars for session security,
     * and sanity checks 
     */
    protected function sessionSetup() {
        $this->set('Qore', 'createTime', time());
        $this->setSessionTimestamp();
        $this->set('Qore', 'RegenCount', 0);
        $this->set('Qore', 'fingerprint', md5($this->userAgent));  
    }
    
    /**
     * check to see if the session is valid by making sure the session timestamp
     * session variable is set, and the session time is less then the sessions
     * max lifetime ($cfg['sessions']['lifetime']), and that this useragent string
     * is the same as the user agent string that created the session, as well as the 
     * Accept-Language string
     * 
     * @return boolean 
     */
    public function isValid() {
        //see if we are checking session lifetime based on createTime or lastUpdat time
        $field = '';
        switch (strtolower($GLOBALS['cfg']['sessions']['lifeTimeCalc'])) {
            case 'lastupdate':
                $field = 'lastSessionUpdate';
                break;
            case 'createtime':
                $field = 'createTime';
                break;
            default:
                $field = 'lastSessionUpdate';
                break;
        }
        
        //make sure the session has all the needed vars, and the fingerprints match
        if ($this->varIsSet('Qore', 'lastSessionUpdate') && $this->varIsSet('Qore', 'createTime')
                && md5($this->userAgent) == $this->get('Qore', 'fingerprint') ) {
            
            //return true if it is session time is good
            $maxLife = $GLOBALS['cfg']['sessions']['lifeTime'];
            $now = time();
            $age = $now - $maxLife;
            $sessionTime = $this->get('Qore', $field);
            
            if ( $sessionTime >= $age ) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * sets a key=>val in current session
     * 
     * @param string $namespace
     * @param string $key
     * @param string $val 
     */
    public function set($namespace, $key, $val) {
        $_SESSION[$namespace][$key] = $val;
    }
    
    /**
     * Deletes session $key in $namespace
     * @param string $namespace
     * @param string $key 
     */
    public function unsetKey($namespace, $key) {
        unset($_SESSION[$namespace][$key]);
    }
    
    /**
     * deletes an entire namespace from the current session
     * 
     * @param type $namespace 
     */
    public function unsetNamespace($namespace) {
        unset($_SESSION[$namespace]);
    }
    
    /**
     * returns a value from the session
     * 
     * @param string $namespace
     * @param string $key
     * @return string on success, bool false on key not found in session 
     */
    public function get($namespace, $key) {
        if (array_key_exists($key, $_SESSION[$namespace])) {
            return $_SESSION[$namespace][$key];
        }
        return false;
    }
    
    /**
     * Checks to see if $key session var is set
     * 
     * @param string $namespace
     * @param string $key
     * @return boolean 
     */
    public function varIsSet($namespace, $key) {
        if (array_key_exists($namespace, $_SESSION)) {
            if (array_key_exists($key, $_SESSION[$namespace])) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    /**
     * Invalidates the sessions client side cookie, 
     * deletes the in memory session,
     * and kills the server side record of the session
     * 
     * @param array $args valid keys:
     *            'redirect' => true or false - default is site config default
     *                          override the default site level config if we should redirect or not
     *            'redirectURL' => URL to redirect to - default is site config default
     *                             orveride the default site level config or where we should redirect to
     *            'sendCookie' => true or false - default is true
     *                            should we send a new 'delete' cookie to the client
     */
    public function kill(array $args = array()) {
        $redirect = $GLOBALS['cfg']['sessions']['redirect'];
        $redirectUrl = $GLOBALS['cfg']['sessions']['redirectURL'];
        $sendDelCookie = true;
        
        //figure out if we will redirect or not
        if (array_key_exists('redirect', $args)) {
            $redirect = $args['redirect'];
        }
        
        //figure out which URL we should redirect to
        if (array_key_exists('redirectURL', $args)) {
            $redirectUrl = $args['redirectURL'];
        }
        
        //if we aren't redirecting...don't send the DelCookie
        if (!$redirect) {
            $sendDelCookie = false;
        }
        
        //see if there is a DelCookie override
        if (array_key_exists('$sendDelCookie', $args)) {
            $sendDelCookie = $args['$sendDelCookie'];
        }
        
        //kill the session array
        $_SESSION = array();
        
        //kill the session cookie on the client
        if ($sendDelCookie) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 500000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        //kill the server's side session cache (if there is one)
        $this->destroy($this->id);
        $this->serverSessionExists = false;
        
        //close the session
        session_write_close();
        
        //redirect the browser to the configured url if redirection is enabled
        if ($redirect) {
            BaseController::redirectToUrl($redirectUrl);
        } else {
            //simply start  new session if we don't redirect
            session_id($this->getNextID());
            session_start();
            $this->id = session_id();
            $this->sessionSetup();
        }
    }
    
    /**
     * Generates a new random string
     * 
     * The random string can have 'sections'. by default there are 4 sections.
     * each section is separated by a hyphen by default.
     * 
     * Each section should not be longer than 275 characters long
     * 
     * the default produces are random string of 70 characters length like so:
     *      731M1c47d95768ucA280o3p9Ve0e51-b6ff28e5d37e-45ew3-fdW704Fc085027G768cK
     * 
     * if you want different lengths of sections (or maybe just 1 section), then
     * you can pass an array and specify 'sections' key:
     *      array('sections'=>array(10))
     *          produces a single section random string of 10 characters length
     * 
     * @param array $args. accpeted keys:
     *                     'separator': the separator to use between sections
     *                                  default is '-'
     *                     'sections': an array of section lengths
     *                                 default is array(30,12,5,20)
     *                              
     * @return string 
     */
    public function genID(array $args=array()) {
        if (!array_key_exists('separator', $args)) {
            $separator = '-';
        } else {
            $separator = $args['separator'];
        }
        
        if (!array_key_exists('sections', $args)) {
            $sections = array(30,12,5,20);
        } else {
            $sections = $args['sections'];
        }
        
        $ret = '';
        
        for($i=0; $i < count($sections); $i++) {
            //creates a random string 275 characters long
            $rnd = str_shuffle(
                md5(mt_rand()) .
                '0123456789' .
                uniqid() .
                'abcdefghijklmnopqrstuvwxyz' .
                sha1(rand()) .
                'ABCDEFGHIJKLMNOPQRSTUVWXYZ' .
                hash('whirlpool', time())
            );
            
            if ($i==0) {
                $ret .= str_shuffle(substr($rnd, rand(0,strlen($rnd)-$sections[$i]), $sections[$i]));
            } else {
                $ret .= $separator . str_shuffle(substr($rnd, rand(0,strlen($rnd)-$sections[$i]), $sections[$i]));
            }
        }
        
        return $ret;
    }
    
    /**
     * Makes sure that the next session ID is unused
     * and returns it to the caller
     * 
     * @return string 
     */
    protected function getNextID() {
        $keepGenerating = true;
        $id = $this->genID();
        while ($keepGenerating) {
            if ($this->sessionExists($id)) {
                $id = $this->genID();
            } else {
                //id is good, exit loop
                $keepGenerating = false;
            }
        }
        return $id;
    }
    
    
    /**
     * records a session flash message that can be displayed on the next page view
     * 
     * @param type $msg 
     */
    public function setFlash($msg) {
        $this->set('Qore', 'Flash', $msg);
    }
    
    /**
     * returns the flash message if it is set
     * 
     * @return string (boolean false if no Flash message
     */
    public function getFlash() {
        if ($this->varIsSet('Qore', 'Flash')) {
            $msg = $this->get('Qore', 'Flash');
            $this->unsetKey('Qore', 'Flash');
            return $msg;
        }
        return false;
    }
    
    /**
     * records a session flash error message that can be displayed on the next page view
     * 
     * @param type $msg 
     */
    public function setError($msg) {
        $this->set('Qore', 'Error', $msg);
    }
    
    /**
     * returns the flash error message if it is set
     * 
     * @return string (boolean false if no Flash message
     */
    public function getError() {
        if ($this->varIsSet('Qore', 'Error')) {
            $msg = $this->get('Qore', 'Error');
            $this->unsetKey('Qore', 'Error');
            return $msg;
        }
        return false;
    }
    
}