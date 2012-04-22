<?php
namespace Qore\Dbadapters;

class DbMysqlSessions implements \Qore\Unreal\iDbSessions {
    private $dbh;
    
    /**
     * @param \Qore\Unreal\iDb $iDbConn
     */
    public function __construct(\Qore\Unreal\iDb $iDbConn) {
        $this->dbh = $iDbConn->getInstance();
    }
    
    /**
     * Returns the $sessionid session data column from the database
     * 
     * @param string $sessionid 
     * @return array or false on session_id not found
     */
    public function read($sessionid) {
        $args = array($sessionid);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT session_data as SessionData 
                FROM sessions
                WHERE session_id = ?");
            $this->sth->execute($args);
            $result = $this->sth->fetch(\PDO::FETCH_ASSOC);
            $rowCount = $this->sth->rowCount();
            if ($rowCount == 1) {
                return $result;
            } else {
                return false;
            }
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * Writes $sessionid and $sessiondata to the database
     * Upates the timestamp for that sessionid
     * 
     * @param string $sessionid
     * @param string $sessiondata
     * @return array
     */
    public function write($sessionid, $sessiondata, $serverSessionExists) {
        if ($serverSessionExists) {
            try {
                $this->sth = $this->dbh->prepare("
                  UPDATE sessions
                  SET session_data = ?
                  WHERE session_id = ?");
                $this->sth->execute(array($sessiondata, $sessionid));
                $rowCount = $this->sth->rowCount();
                if ($rowCount == 1) {
                    return true;
                } else {
                    return false;
                }
            } catch(\PDOException $e) {
                throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
            }
        } else {
            try {
                $this->sth = $this->dbh->prepare("
                    INSERT INTO sessions (session_id, session_data, create_time) VALUES (?, ?, NOW())");
                $this->sth->execute(array( $sessionid, $sessiondata));
                $rowCount = $this->sth->rowCount();
                if ($rowCount == 1) {
                    return true;
                } else {
                    return false;
                }
            } catch(\PDOException $e) {
                throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
            }
        }
    }
    
    /**
     * Returns true if $sessionid exists in database
     *  false otherwise
     * 
     * @param string $sessionid
     * @return bool true is sessionExits, false otherwise
     */
    public function sessionExists($sessionid) {
        if ($this->read($sessionid)) {
            return true;
        }
        return false;
    }
    
    /**
     * Deletes $sessionid row from database
     * 
     * @param string $sessionid
     * @return bool true if no error, false otherwise
     */
    public function destroy($sessionid) {
        $args = array($sessionid);
        
        try {
            $this->sth = $this->dbh->prepare("
                DELETE FROM sessions WHERE session_id = ?");
            $this->sth->execute($args);
            $rowCount = $this->sth->rowCount();
            if ($rowCount == 1) {
                return true;
            } else {
                return false;
            }
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * PHP session garbage collection
     * deletes all sessions older then 
     * the defined max session life
     * 
     * @param string $maxlifetime 
     */
    public function gc($maxlifetime) {
        $oldTimeStamp = date("Y-m-d H:i:s", time() - $maxlifetime);
        try {
            $this->sth = $this->dbh->prepare("
                DELETE FROM sessions WHERE last_update <= ?");
            $this->sth->execute(array($oldTimeStamp));
            return true;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
}