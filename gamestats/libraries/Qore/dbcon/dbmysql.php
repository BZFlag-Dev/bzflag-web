<?php
namespace Qore\Dbcon;

class DbMysql implements \Qore\Unreal\iDb {
    private $dbh;
    
    /**
     * connects to the specified database instance, creates the PDO instance
     * 
     * Constructor: takes a dbinstance parameter to figure out which
     * $cfg['db'][<instance>] we should use for connection details
     *
     * @param type $dbInstance
     * @throws \Exception 
     */
    public function __construct($dbInstance) {
        $conString =    "mysql:host=".$GLOBALS['cfg']['db'][$dbInstance]['host'].
                        ";port=".$GLOBALS['cfg']['db'][$dbInstance]['port'].
                        ";dbname=".$GLOBALS['cfg']['db'][$dbInstance]['database'];
        try {
            $this->dbh = new \PDO($conString,$GLOBALS['cfg']['db'][$dbInstance]['user'],$GLOBALS['cfg']['db'][$dbInstance]['password']);
            $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch(\Exception $e) {
            throw new \Exception($e);
        }
    }
    
    /**
     *  kills the PDO instance 
     */
    public function __destruct() {
        $this->dbh = null;
    }
    
    /**
     *  returns a MySQL PDO instance 
     */
    public function getInstance() {
        return $this->dbh;
    }
}