<?php
namespace Packs\Bzstats\Models;

/**
 * Model wrapper to get our stats dbadapter
 *
 * @author Ian Farr
 */
class StatsModel extends \Qore\BaseModel implements \Packs\Bzstats\Unreal\iDbStats {
    
    /**
     * @param \Packs\Bzstats\Unreal\aDbStats $db
     */
    public function __construct(\Packs\Bzstats\Unreal\aDbStats $db) {
        parent::__construct();
        $this->db = $db;
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
        return $this->db->getServerCount($startDate, $endDate);
    }
    
    /**
     * Returns te most current active players and servers from the database
     * 
     * @return array $result
     * @throws \Exception 
     */
    public function getCurrentStats() {
        return $this->db->getCurrentStats();
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
        return $this->db->getPlayerCounts($startDate, $endDate);
    }
    
    /**
     * Returns the most/least popular(active) time period between two dates
     * 
     * @param string $type "least" for the least popular/active period. "most" for the most popular
     * @param string $startDate
     * @param string $endDate
     * @return array
     * @throws \Exception 
     */
    public function getPopularTime($type, $startDate, $endDate) {
        return $this->db->getPopularTime($type, $startDate, $endDate);
    }
    
   /**
     * Returns the most active/popular server beween two dates
     * 
     * @param string $startDate
     * @param string $endDate
     * @return array
     * @throws \Exception 
     */
    public function getMostPopularServer($startDate, $endDate) {
        return $this->db->getMostPopularServer($startDate, $endDate);
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
        return $this->db->getMostWins($startDate, $endDate);
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
        return $this->db->getPlayerByRatio($type, $startDate, $endDate);
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
        return $this->db->getMostTK($startDate, $endDate);
    }
    
    /**
     * Returns the total player/active server counts between two dates, in a format that is compatible with the API/DyGraph.
     * used for graphing active players/servers between two date
     * 
     * @param string $startDate
     * @param string $endDate
     * @return array
     * @throws \Exception 
     */
    public function getTotalCount($startDate, $endDate) {
        return $this->db->getTotalCount($startDate, $endDate);
    }
    
    /**
     * Returns the total player counts between two dates, in a format that is compatible with the API/DyGraph.
     * used for graphing active players/servers between two date
     * 
     * @param string $startDate
     * @param string $endDate
     * @return array
     * @throws \Exception 
     */
    public function getTotalPlayerCount($startDate, $endDate) {
        return $this->db->getTotalPlayerCount($startDate, $endDate);
    }
    
    /**
     * Returns an array compatible with dygraph for graphing purposes. 
     * It's a SUM of players/observers over time from the server_updates
     * table
     * 
     * @param string $startDate
     * @param string $endDate
     * @return array
     * @throws \Exception 
     */
    public function getSumedPlayerCount($startDate, $endDate) {
        return $this->db->getSumedPlayerCount($startDate, $endDate);
    }
    
    /**
     * Returns the total Server counts between two dates, in a format that is compatible with the API/DyGraph.
     * used for graphing active players/servers between two date
     * 
     * @param string $startDate
     * @param string $endDate
     * @return array
     * @throws \Exception 
     */
    public function getTotalServerCount($startDate, $endDate) {
        return $this->db->getTotalServerCount($startDate, $endDate);
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
        return $this->db->getActiveServerList($startDate, $endDate);
    }
    
    /**
     * Returns all registered servers with descriptions sorted by the LastUpdate timestamp
     * 
     * @return array
     * @throws \Exception 
     */
    public function getServerList() {
        return $this->db->getServerList();
    }
    
    /**
     * Returns the total Player/Observer counts over time for a specific server
     * Format is compatible with API/DyGraphs for graphing purposes
     * 
     * @param string $serverName
     * @param string $startDate
     * @param string $endDate
     * @return array
     * @throws \Exception 
     */
    public function getSpecificServerStats($serverName, $startDate, $endDate) {
        $data = $this->padDates(
                $startDate, 
                $endDate, 
                $this->db->getSpecificServerStats($serverName, $startDate, $endDate), 
                array('Timestamp','Players', 'Observers'),
                1800);
        return $data;
    }
    
    /**
     * Returns the MAX Players, Observers, and Total Players (players + observers)
     * for a specific server between two dates
     * 
     * @param string $serverName
     * @param string $startDate
     * @param string $endDate
     * @return array
     * @throws \Exception 
     */
    public function getSpecificServerMaxPlayers($serverName, $startDate, $endDate) {
        return $this->db->getSpecificServerMaxPlayers($serverName, $startDate, $endDate);
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
        return $this->db->getSpecificServerAvgPlayers($serverName, $startDate, $endDate);
    }
    
    /**
     * Returns Server Details for a specific server (description, gametype, flags, teams, lastupdate)
     * 
     * @param sring $serverName
     * @return array
     * @throws \Exception 
     */
    public function getSpecificServerDescription($serverName) {
        return $this->db->getSpecificServerDescription($serverName);
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
        return $this->db->getSpecificServerPlayers($serverName, $startDate, $endDate);
    }
    
    /**
     * Returns the Player with the most wins between two dates on a specific server
     * 
     * @param sring $startDate
     * @param string $endDate
     * @return array
     * @throws \Exception 
     */
    public function getSpecificServerMostWins($serverName, $startDate, $endDate) {
        return $this->db->getSpecificServerMostWins($serverName, $startDate, $endDate);
    }
    
    /**
     * Returns the player details for the worst player (by ratio) on a specific server
     * ratio is wins/losses
     * 
     * @param string $type "worst" for the worst ratio. "best" for best ratio
     * @param string $startDate
     * @param string $endDate
     * @return array
     * @throws \Exception
     */
    public function getSpecificServerPlayerByRatio($serverName, $type, $startDate, $endDate) {
        return $this->db->getSpecificServerPlayerByRatio($serverName, $type, $startDate, $endDate);
    }
    
    /**
     * Returns the player with the most TK's between two dates on a specific server
     * 
     * @param string $startDate
     * @param string $endDate
     * @return array
     * @throws \Exception
     */
    public function getSpecificServerMostTK($serverName, $startDate, $endDate) {
        return $this->db->getSpecificServerMostTK($serverName, $startDate, $endDate);
    }
    
    /**
     * Returns the connected players between $startDate and $endDate
     * 
     * @param string $startDate
     * @param string $endDate 
     */
    public function getCurrentPlayers($startDate, $endDate) {
        return  $this->db->getCurrentPlayers($startDate, $endDate);;
    }
    
    /**
     * Returns the first/last seen date/details of a player
     * 
     * @param string $type last|first last = last/most recent time a player was seen, first = oldest time
     * @param string $playerName
     * @return array
     * @throws \Exception 
     */
    public function getPlayerSeenDetails($type, $playerName) {
        return $this->db->getPlayerSeenDetails($type, $playerName);
    }
    
    /**
     * Returns the players best/worst ratio with details
     * 
     * @param string $type best|worst ratio
     * @param string $playerName
     * @return array
     * @throws \Exception 
     */
    public function getPlayerRatioDetails($type, $playerName) {
        return $this->db->getPlayerRatioDetails($type, $playerName);
    }
    
    /**
     * Returns the players highest score (most wins)
     * 
     * @param string $playerName
     * @return array
     * @throws \Exception 
     */
    public function getPlayerMostWinDetails($playerName) {
        return $this->db->getPlayerMostWinDetails($playerName);
    }
    
    /**
     * Returns the players most losses details
     * 
     * @param string $playerName
     * @return array
     * @throws \Exception 
     */
    public function getPlayerMostLossDetails($playerName) {
        return $this->db->getPlayerMostLossDetails($playerName);
    }
    
    /**
    * Returns the players most TK's with details
    * 
    * @param string $playerName
    * @return array
    * @throws \Exception 
    */
   public function getPlayerMostTKDetails($playerName) {
       return $this->db->getPlayerMostTKDetails($playerName);
   }
   
   /**
    * Gets the players top 10 servers
    * 
    * @param string $playerName
    * @return array
    * @throws \Exception 
    */
   public function getPlayerFavoriteServers($playerName) {
       return $this->db->getPlayerFavoriteServers($playerName);
   }
   
   /**
    * Returns a list of Timestamps with a value of 100 of active times.
    * This can be used to produce a chart/graph with dygraphs of the players active times
    * 
    * @param string $playerName
    * @param string $startDate
    * @param string $endDate
    * @return array
    * @throws \Exception 
    */
   public function getPlayerActiveTimes($playerName, $startDate, $endDate){
       return $this->padDates(
               $startDate, 
               $endDate, 
               $this->db->getPlayerActiveTimes($playerName, $startDate, $endDate),
               array('Timestamp', 'Active'),
               1800);
   }
   
   /**
    * Returns a list of player scores over time.
    * This can be used to produce a chart/graph with dygraphs of the players score history
    * 
    * @param string $playerName
    * @param string $startDate
    * @param string $endDate
    * @return array
    * @throws \Exception 
    */
   public function getPlayerScores($playerName, $startDate, $endDate){
       return $this->padDates(
               $startDate, 
               $endDate, 
               $this->db->getPlayerScores($playerName, $startDate, $endDate),
               array('Timestamp', 'Wins', 'Losses', 'Teamkills'),
               1800);
   }
   
   /**
    * Tries to Find playernames matching $playerName
    * 
    * @param string $playerName
    * @return array
    * @throws \Exception 
    */
   public function findPlayer($playerName) {
       $playerName = '%' . $playerName . '%';
       return $this->db->findPlayer($playerName);
   }
    
    /**
     * The data that is returned from the database will have time gaps in some queries
     * this example will pad those results - filling in the gaps with timestamps and 
     * corresponding values of 0 across all columns. 
     * 
     * Usefull only to queries whose first column is a timestamp column, and all other
     * columns are numeric based (exmaple: any querie that is graphable with DyGraphs)
     * 
     * @param string $startDate
     * @param string $endDate
     * @param array $data
     * @param array #sampleColumnNames What the column names should be in case there is no
     *                                  data returned - we can build a complete 0 filled array
     * @param int $step the smallest frequency in seconds that a gap should be within.
     *                  60 minutes (3600 seconds) should be pretty good for most casses
     *                  Please be aware that smaller frequencies over larger time periods
     *                  can become unusable quickly (Ex: 10minute periods over 2 years)
     * @return array
     */
    private function padDates($startDate, $endDate, array $data, array $sampleColumnNames, $step) {
        $padArr = array();
        $startUnix = strtotime($startDate);
        $endUnix = strtotime($endDate);
        $rows = count($data);
        $currentRow = 0;
        
        //get the column names
        $columnNames = array_values($sampleColumnNames);
        $totalColumns = count($columnNames);
        $stop = false;

        while($startUnix <= $endUnix) {
            //we force doPad to false each loop
            $doPad = false;
            
            //do we have any more data in the array?
            if ($rows>0 && $currentRow < $rows) {
                //we (still) have data - detect gaps and pad if needed
                
                //get the current rows datestamp
                $rowTime = strtotime($data[$currentRow][$columnNames[0]]);

                //is this row's timestamp less then the $startUnix stamp + step?
                if ($rowTime <= ($startUnix+$step) ) {
                    //the current row is within <step> minnutes, assign it to the array, and move on
                    $padArr[] = $data[$currentRow];
                    $currentRow++;
                    
                    //check the next row - if it's time is also less then the current interval, don't increment
                    if ($currentRow < $rows) {
                        $nextRowTime = strtotime($data[$currentRow][$columnNames[0]]);

                        if( $nextRowTime >=($startUnix+$step)) {
                            $startUnix += $step;
                        }
                    }
                } else {
                    //we have detected a gap - pad the array, and increment $startTime by <step> minutes
                    $doPad = true;
                }
            } else {
                //we don't have any (more) data, but we are still within the date range
                //pad this period and loop
                $doPad = true;
            }
            
            if ($doPad) {
                //pad the current time period by building a zero padded array 
                //    and assining it to the corrent time slot
                $tmp = array();
                for ($i=0;$i<$totalColumns;$i++) {
                    if ($i == 0) {
                        //the first column is the date column - add the date
                        $tmp[$columnNames[$i]] = date('Y/m/d H:i:s', $startUnix);
                    } else {
                        $tmp[$columnNames[$i]] = 0;
                    }
                }
                
                $padArr[] = $tmp;
                
                //increment the startUnix timestamp to the next step
                $startUnix += $step;
            }
        }
        return $padArr;
    }
}