<?php
namespace Packs\Bzstats\Dbadapters;

/**
 * DB Adapter to get all the needed Statistics from the BzStats tables
 *
 * @author Ian Farr
 */
class DbMysqlStats extends \Packs\Bzstats\Unreal\aDbStats {
    
    /**
     * @param \Qore\Unreal\iDb $dbMysql
     */
    public function __construct(\Qore\Unreal\iDb $dbMysql) {
        $this->dbh = $dbMysql->getInstance();
    }
    
    /**
     * Returns the total count of Active Servers between two dates
     * 
     * @param string $startDate
     * @param string $endDate
     * @return array $result
     * @throws \Exception 
     */
    public function getServerCount($startDate, $endDate) {
        $dates = array($startDate, $endDate);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT COUNT(DISTINCT ServerName) AS 'ServerCount' 
                FROM server_names 
                WHERE LastUpdate BETWEEN ? AND ?");
            $this->sth->execute($dates);
            $result = $this->sth->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * Returns te most current active players and servers from the database
     * 
     * @param string $tz
     * @return array $result
     * @throws \Exception 
     */
    public function getCurrentStats($tz) {
        $vals = array($tz);
        try {
            $this->sth = $this->dbh->prepare("
                SELECT Players, Servers as Games, CONVERT_TZ(Timestamp, 'GMT', ?) as Timestamp
                FROM server_totals
                ORDER BY Timestamp DESC LIMIT 1");
            $this->sth->execute($vals);
            $result = $this->sth->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * Returns Average,Min,Max player counts across all servers
     * 
     * @param string $startDate
     * @param string $endDate
     * @return array
     * @throws \Exception 
     */
    public function getPlayerCounts($startDate, $endDate) {
        $dates = array($startDate, $endDate);
        try {
            $this->sth = $this->dbh->prepare("
                SELECT AVG(Players) as 'AveragePlayers', 
		 AVG(Servers) as 'AverageServers',
		 MIN(Players) as 'MinPlayers', 
		 MIN(Servers) as 'MinServers',
		 MAX(Players) as 'MaxPlayers', 
		 MAX(Servers) as 'MaxServers' 
                FROM server_totals 
                WHERE Timestamp BETWEEN ? AND ?");
            $this->sth->execute($dates);
            $result = $this->sth->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * Returns the most/least popular(active) time period between two dates
     * 
     * @param string $type "least" for the least popular/active period. "most" for the most popular
     * @param string $startDate
     * @param string $endDate
     * @param string $tz
     * @return array
     * @throws \Exception 
     */
    public function getPopularTime($type, $startDate, $endDate, $tz) {
        if (strtolower($type) == "least") {
            $order = "ORDER BY Players ASC Limit 1";
        } else {
            $order = "ORDER BY Players DESC Limit 1";
        }
        $dates = array($tz, $startDate, $endDate);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT Players, Servers, CONVERT_TZ(Timestamp, 'GMT', ?) as Timestamp 
                FROM server_totals 
                WHERE Timestamp BETWEEN ? AND ? " . $order);
            $this->sth->execute($dates);
            $result = $this->sth->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * Returns the most active/popular server beween two dates
     * 
     * @param string $startDate
     * @param string $endDate
     * @param string $tz
     * @return array
     * @throws \Exception 
     */
    public function getMostPopularServer($startDate, $endDate, $tz) {
        $dates = array($tz, $startDate, $endDate);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT ServerName, Observers+Players as TotalPlayers, Players, Observers, CONVERT_TZ(Timestamp, 'GMT', ?) as Timestamp 
                FROM server_updates 
                WHERE Timestamp BETWEEN ? AND ? 
                ORDER BY TotalPlayers DESC, ServerName ASC LIMIT 1");
            $this->sth->execute($dates);
            $result = $this->sth->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * Returns the Player with the most wins between two dates
     * 
     * @param sring $startDate
     * @param string $endDate
     * @return array
     * @throws \Exception 
     */
    public function getMostWins($startDate, $endDate) {
        $dates = array($startDate, $endDate);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT PlayerName, ServerName, Team, Wins, Losses, Teamkills 
                FROM player_updates 
                WHERE Timestamp BETWEEN ? AND ? 
                ORDER BY Wins Desc LIMIT 1");
            $this->sth->execute($dates);
            $result = $this->sth->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * Returns the player details for the worst player (by ratio)
     * ratio is wins/losses
     * 
     * @param string $type "worst" for the worst ratio. "best" for the best ratio
     * @param string $startDate
     * @param string $endDate
     * @return array
     * @throws \Exception
     */
    public function getPlayerByRatio($type, $startDate, $endDate) {
        if (strtolower($type) == "worst") {
            $order = "ORDER BY RATIO ASC LIMIT 1";
        } else {
            $order = "ORDER BY RATIO DESC LIMIT 1";
        }
        $dates = array($startDate, $endDate);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT PlayerName, ServerName, Team, Wins, Losses, Teamkills, Wins/Losses AS 'Ratio' 
                FROM player_updates 
                WHERE Wins/Losses > 0 AND (Timestamp BETWEEN ? AND ? )".$order);
            $this->sth->execute($dates);
            $result = $this->sth->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * Returns the player with the most TK's between two dates
     * 
     * @param string $startDate
     * @param string $endDate
     * @return array
     * @throws \Exception
     */
    public function getMostTK($startDate, $endDate) {
        $dates = array($startDate, $endDate);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT PlayerName, ServerName, Team, Wins, Losses, Teamkills 
                FROM player_updates 
                WHERE Timestamp BETWEEN ? AND ? 
                ORDER BY Teamkills DESC LIMIT 1");
            $this->sth->execute($dates);
            $result = $this->sth->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * Returns the total player/active server counts between two dates, in a format that is compatible with the API/DyGraph.
     * used for graphing active players/servers between two date
     * 
     * @param string $startDate
     * @param string $endDate
     * @param string $tz
     * @return array
     * @throws \Exception
     */
    public function getTotalCount($startDate, $endDate, $tz) {
        $dates = array($tz, $startDate, $endDate);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT Date_Format(CONVERT_TZ(Timestamp, 'GMT', ?), '%Y/%m/%d %H:%i:%s') as Timestamp, Players, Servers 
                FROM server_totals 
                WHERE Timestamp BETWEEN ? and ? 
                ORDER BY Timestamp ASC");
            $this->sth->execute($dates);
            $result = $this->sth->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * Returns the total player counts between two dates, in a format that is compatible with the API/DyGraph.
     * used for graphing active players/servers between two date
     * 
     * @param string $startDate
     * @param string $endDate
     * @param string $tz
     * @return array
     * @throws \Exception 
     */
    public function getTotalPlayerCount($startDate, $endDate, $tz) {
            $dates = array($tz, $startDate, $endDate);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT Date_Format(CONVERT_TZ(Timestamp, 'GMT', ?), '%Y/%m/%d %H:%i:%s') as Timestamp, Players 
                FROM server_totals 
                WHERE Timestamp BETWEEN ? and ? 
                ORDER BY Timestamp ASC");
            $this->sth->execute($dates);
            $result = $this->sth->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * Returns an array compatible with dygraph for graphing purposes. 
     * It's a SUM of players/observers over time from the server_updates
     * table
     * 
     * @param string $startDate
     * @param string $endDate
     * @param string $tz
     * @return array
     * @throws \Exception 
     */
    public function getSumedPlayerCount($startDate, $endDate, $tz) {
            $dates = array($tz, $startDate, $endDate);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT Date_Format(CONVERT_TZ(Timestamp, 'GMT', ?), '%Y/%m/%d %H:%i:%s') as Timestamp, SUM(Players) AS Players, SUM(Observers) AS Observers
                FROM server_updates
                WHERE Timestamp BETWEEN ? AND ?
                GROUP BY Timestamp
                ORDER BY Timestamp ASC;");
            $this->sth->execute($dates);
            $result = $this->sth->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * Returns the total Server counts between two dates, in a format that is compatible with the API/DyGraph.
     * used for graphing active players/servers between two date
     * 
     * @param string $startDate
     * @param string $endDate
     * @param string $tz
     * @return array
     * @throws \Exception 
     */
    public function getTotalServerCount($startDate, $endDate, $tz) {
            $dates = array($tz, $startDate, $endDate);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT Date_Format(CONVERT_TZ(Timestamp, 'GMT', ?), '%Y/%m/%d %H:%i:%s') as Timestamp, Servers 
                FROM server_totals 
                WHERE Timestamp BETWEEN ? and ? 
                ORDER BY Timestamp ASC");
            $this->sth->execute($dates);
            $result = $this->sth->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * Returns Max players/Observers states for all active servers between two dates
     * 
     * @param string $startDate
     * @param string $endDate
     * @return array
     * @throws \Exception
     */
    public function getActiveServerList($startDate, $endDate) {
        $dates = array($startDate, $endDate);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT ServerName, MAX(Players) as Players, MAX(Observers) as Observers 
                FROM server_updates 
                WHERE Players > 0 AND (Timestamp BETWEEN ? AND ?)
		GROUP By ServerName
                ORDER BY Players DESC");
            $this->sth->execute($dates);
            $result = $this->sth->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * Returns all registered servers with descriptions sorted by the LastUpdate timestamp
     * 
     * @param string $tz
     * @return array
     * @throws \Exception 
     */
    public function getServerList($tz) {
        $dates = array($tz);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT ServerName, Description, CONVERT_TZ(LastUpdate, 'GMT', ?) as LastUpdate 
                FROM server_names
                ORDER by LastUpdate DESC, ServerName ASC");
            $this->sth->execute($dates);
            $result = $this->sth->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * Returns the total Player/Observer counts over time for a specific server
     * Format is compatible with API/DyGraphs for graphing purposes
     * 
     * @param string $serverName
     * @param string $startDate
     * @param string $endDate
     * @param string $tz
     * @return array
     * @throws \Exception 
     */
    public function getSpecificServerStats($serverName, $startDate, $endDate, $tz) {
        $args = array($tz, $serverName, $startDate, $endDate);
        try {
            $this->sth = $this->dbh->prepare("
                SELECT Date_Format(CONVERT_TZ(Timestamp, 'GMT', ?), '%Y/%m/%d %H:%i:%s') as Timestamp, Players, Observers
                FROM server_updates 
                WHERE servername = ? AND
                    ( Timestamp BETWEEN ? AND ? )
                ORDER BY Timestamp ASC");
            $this->sth->execute($args);
            $result = $this->sth->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * Returns the MAX Players, Observers, and Total Players (players + observers)
     * for a specific server between two dates
     * 
     * @param string $serverName
     * @param string $startDate
     * @param string $endDate
     * @param string $tz
     * @return array
     * @throws \Exception 
     */
    public function getSpecificServerMaxPlayers($serverName, $startDate, $endDate, $tz) {
        $args = array($tz, $serverName, $startDate, $endDate);
        try {
            $this->sth = $this->dbh->prepare("
                SELECT Players, Observers, Players+Observers AS 'Total', CONVERT_TZ(Timestamp, 'GMT', ?) as Timestamp
                FROM server_updates
                WHERE ServerName = ? AND
                    (Timestamp BETWEEN ? AND ?)
                ORDER BY Total DESC
                Limit 1");
            $this->sth->execute($args);
            $result = $this->sth->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * Returns the AVG Players, Observers, and Total Players (players + observers)
     * for a specific server between two dates
     * 
     * @param string $serverName
     * @param string $startDate
     * @param string $endDate
     * @return array
     * @throws \Exception
     */
    public function getSpecificServerAvgPlayers($serverName, $startDate, $endDate) {
        $args = array($serverName, $startDate, $endDate);
        try {
            $this->sth = $this->dbh->prepare("
                SELECT AVG(Players) AS AvgPlayers, AVG(observers) AS AvgObservers, AVG(Players+Observers) AS AvgTotal
                FROM server_updates
                WHERE ServerName = ? AND
                    (Timestamp BETWEEN ? AND ?)");
            $this->sth->execute($args);
            $result = $this->sth->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError) ;
        }
    }
    
    /**
     * Returns Server Details for a specific server (description, gametype, flags, teams, lastupdate)
     * 
     * @param sring $serverName
     * @param string $tz
     * @return array
     * @throws \Exception 
     */
    public function getSpecificServerDescription($serverName, $tz) {
        $args = array($tz, $serverName);
        try {
            $this->sth = $this->dbh->prepare("
                SELECT Description, GameType, GameFlags, Teams, CONVERT_TZ(LastUpdate, 'GMT', ?) as LastUpdate 
                FROM server_names 
                WHERE ServerName = ?");
            $this->sth->execute($args);
            $result = $this->sth->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError) ;
        }
    }
    
    /**
     * Returns all Players w/ details(team, wins/losses..) that have played on a given server between two dates
     * 
     * @param string $serverName
     * @param string $startDate
     * @param string $endDate
     * @return array
     * @throws \Exception 
     */
    public function getSpecificServerPlayers($serverName, $startDate, $endDate) {
        $args = array($serverName, $startDate, $endDate);
        try {
            $this->sth = $this->dbh->prepare("
                SELECT DISTINCT(PlayerName)
                FROM player_updates 
                WHERE ServerName = ? AND
                    (Timestamp BETWEEN ? AND ?)
		ORDER BY PlayerName ASC");
            $this->sth->execute($args);
            $result = $this->sth->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError) ;
        }
    }
    
    /**
     * Returns the Player with the most wins between two dates on a specific server
     * 
     * @param sring $startDate
     * @param string $endDate
     * @param string $tz
     * @return array
     * @throws \Exception 
     */
    public function getSpecificServerMostWins($serverName, $startDate, $endDate, $tz) {
        $args = array($tz, $serverName, $startDate, $endDate);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT PlayerName, Team, Wins, Losses, Teamkills, CONVERT_TZ(Timestamp, 'GMT', ?) as Timestamp
                FROM player_updates 
                WHERE ServerName = ? AND 
                    (Timestamp BETWEEN ? AND ?)
                ORDER BY Wins Desc LIMIT 1");
            $this->sth->execute($args);
            $result = $this->sth->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * Returns the player details for the worst player (by ratio) on a specific server
     * ratio is wins/losses
     * 
     * @param string $type "worst" for the worst ratio. "best" for the best ratio
     * @param string $startDate
     * @param string $endDate
     * @param string $tz
     * @return array
     * @throws \Exception
     */
    public function getSpecificServerPlayerByRatio($serverName, $type, $startDate, $endDate, $tz) {
        if (strtolower($type) == "worst") {
            $order = "ORDER BY Ratio ASC LIMIT 1";
        } else {
            $order = "ORDER BY Ratio DESC LIMIT 1";
        }
        $args = array($tz, $serverName, $startDate, $endDate);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT PlayerName, Team, Wins, Losses, Teamkills, Wins/Losses AS 'Ratio', CONVERT_TZ(Timestamp, 'GMT', ?) as Timestamp  
                FROM player_updates 
                WHERE ServerName = ? AND 
                    (Wins/Losses > 0) AND 
                    (Timestamp BETWEEN ? AND ? ) ".$order);
            $this->sth->execute($args);
            $result = $this->sth->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * Returns the player with the most TK's between two dates on a specific server
     * 
     * @param string $startDate
     * @param string $endDate
     * @param string $tz
     * @return array
     * @throws \Exception
     */
    public function getSpecificServerMostTK($serverName, $startDate, $endDate, $tz) {
        $args = array($tz, $serverName, $startDate, $endDate);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT PlayerName, Team, Wins, Losses, Teamkills, CONVERT_TZ(Timestamp, 'GMT', ?) as Timestamp 
                FROM player_updates 
                WHERE ServerName = ? AND 
                    (Timestamp BETWEEN ? AND ?)
                ORDER BY Teamkills DESC LIMIT 1");
            $this->sth->execute($args);
            $result = $this->sth->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * Returns the connected players between $startDate and $endDate
     * 
     * @param string $startDate
     * @param string $endDate 
     * @param string $tz
     * @return array
     * @throws \Exception
     */
    public function getCurrentPlayers($startDate, $endDate, $tz) {
        $dates = array($tz, $startDate, $endDate);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT PlayerName, ServerName, Team, CONVERT_TZ(Timestamp, 'GMT', ?) as Timestamp, Wins, Losses, Teamkills
                FROM  player_updates
                WHERE Timestamp BETWEEN ? AND ?
                ORDER BY PlayerName ASC, ServerName ASC");
            $this->sth->execute($dates);
            $result = $this->sth->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * Returns the first/last seen date/details of a player
     * 
     * @param string $type
     * @param string $playerName
     * @param string $tz
     * @return array
     * @throws \Exception 
     */
    public function getPlayerSeenDetails($type, $playerName, $tz) {
        if (strtolower($type) == "last") {
            $order = "ORDER BY Timestamp DESC LIMIT 1";
        } else {
            $order = "ORDER BY Timestamp ASC LIMIT 1";
        }
        $args = array($tz, $playerName);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT PlayerName, ServerName, Team, CONVERT_TZ(Timestamp, 'GMT', ?) as Timestamp
                    FROM player_updates
                    WHERE PlayerName = ? ".$order);
            $this->sth->execute($args);
            $result = $this->sth->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * Returns the players best/worst ratio with details
     * 
     * @param string $type best|worst ratio
     * @param string $playerName
     * @param string $tz
     * @return array
     * @throws \Exception 
     */
    public function getPlayerRatioDetails($type, $playerName, $tz) {
        if (strtolower($type) == "best") {
            $order = "ORDER BY Ratio DESC LIMIT 1";
        } else {
            $order = "ORDER BY Ratio ASC LIMIT 1";
        }
        $args = array($tz, $playerName);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT PlayerName, ServerName, Team, Wins, Losses, Teamkills, Wins/Losses AS 'Ratio', CONVERT_TZ(Timestamp, 'GMT', ?) as Timestamp 
                FROM player_updates 
                WHERE PlayerName = ? AND Wins/Losses is not null ".$order);
            $this->sth->execute($args);
            $result = $this->sth->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
    }
    
    /**
     * Returns the players highest score (most wins)
     * 
     * @param string $playerName
     * @param string $tz
     * @return array
     * @throws \Exception 
     */
    public function getPlayerMostWinDetails($playerName, $tz) {
       $args = array($tz, $playerName);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT PlayerName, ServerName, Team, Wins, Losses, Teamkills, CONVERT_TZ(Timestamp, 'GMT', ?) as Timestamp
                FROM player_updates 
                WHERE PlayerName = ? 
                    AND Wins is not null
                ORDER BY WINS DESC
                LIMIT 1");
            $this->sth->execute($args);
            $result = $this->sth->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
   }
   
    /**
     * Returns the players most losses details
     * 
     * @param string $playerName
     * @param string $tz
     * @return array
     * @throws \Exception 
     */
    public function getPlayerMostLossDetails($playerName, $tz){
       $args = array($tz, $playerName);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT PlayerName, ServerName, Team, Wins, Losses, Teamkills, CONVERT_TZ(Timestamp, 'GMT', ?) as Timestamp 
                FROM player_updates 
                WHERE PlayerName = ? 
                    AND Losses is not null
                ORDER BY Losses DESC
                LIMIT 1");
            $this->sth->execute($args);
            $result = $this->sth->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
   }
   
   /**
    * Returns the players most TK's with details
    * 
    * @param string $playerName
    * @param string $tz
    * @return array
    * @throws \Exception 
    */
   public function getPlayerMostTKDetails($playerName, $tz) {
       $args = array($tz, $playerName);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT PlayerName, ServerName, Team, Wins, Losses, Teamkills, CONVERT_TZ(Timestamp, 'GMT', ?) as Timestamp 
                FROM player_updates 
                WHERE PlayerName = ? 
                    AND Teamkills is not null
                ORDER BY Teamkills DESC
                LIMIT 1");
            $this->sth->execute($args);
            $result = $this->sth->fetch(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
   }
   
   /**
    * Gets the players top 10 servers
    * 
    * @param string $playerName
    * @return array
    * @throws \Exception 
    */
   public function getPlayerFavoriteServers($playerName) {
       $args = array($playerName);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT ServerName, Count(ServerName) AS TimesPlayed
                FROM player_updates 
                WHERE PlayerName = ?
                GROUP BY ServerName
                ORDER BY TimesPlayed DESC
                Limit 10");
            $this->sth->execute($args);
            $result = $this->sth->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
   }
   
   /**
    * Returns a list of Timestamps with a value of 100 of active times.
    * This can be used to produce a chart/graph with dygraphs of the players active times
    * 
    * @param string $playerName
    * @param string $startDate
    * @param string $endDate
    * @param string $tz
    * @return array
    * @throws \Exception 
    */
   public function getPlayerActiveTimes($playerName, $startDate, $endDate, $tz){
       $args = array($tz, $playerName, $startDate, $endDate);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT Date_Format(CONVERT_TZ(Timestamp, 'GMT', ?), '%Y/%m/%d %H:%i:%s') as Timestamp, 100 AS Active
                FROM player_updates 
                WHERE PlayerName = ?
                    AND Timestamp BETWEEN ? AND ?
                GROUP BY Timestamp
                ORDER BY Timestamp ASC");
            $this->sth->execute($args);
            $result = $this->sth->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
   }
   
   /**
    * Returns a list of player scores over time.
    * This can be used to produce a chart/graph with dygraphs of the players score history
    * 
    * @param string $playerName
    * @param string $startDate
    * @param string $endDate
    * @param string $tz
    * @return array
    * @throws \Exception 
    */
   public function getPlayerScores($playerName, $startDate, $endDate, $tz){
       $args = array($tz, $playerName, $startDate, $endDate);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT Date_Format(CONVERT_TZ(Timestamp, 'GMT', ?), '%Y/%m/%d %H:%i:%s') as Timestamp, Wins, Losses, Teamkills
                FROM player_updates 
                WHERE PlayerName = ?
                    AND (Timestamp BETWEEN ? AND ?)
                    AND Wins is not null AND Losses is not null
                    AND Team <> 'Observer'
                GROUP BY Timestamp
                ORDER BY Timestamp ASC");
            $this->sth->execute($args);
            $result = $this->sth->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
   }
   
   /**
    * Tries to Find playernames matching $playerName
    * 
    * @param string $playerName
    * @return array
    * @throws \Exception 
    */
   public function findPlayer($playerName) {
       $args = array($playerName);
        
        try {
            $this->sth = $this->dbh->prepare("
                SELECT PlayerName
                FROM player_names 
                WHERE PlayerName like ?");
            $this->sth->execute($args);
            $result = $this->sth->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch(\PDOException $e) {
            throw new \Qore\Qexception($e, \Qore\Qexception::$InternalError);
        }
   }
}