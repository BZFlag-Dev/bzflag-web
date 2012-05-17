<?php
/**
 * Stats Controller - Responsible for the Home, Servers, and Players pages
 *
 * @author Ian Farr
 */
class BzstatsController extends \Qore\Controller {
    private $curStart;
    private $dayStart;
    private $weekStart;
    private $end;
    private $timezone;
    
    /**
     *
     * @var \Packs\Bzstats\Models\Statsmodel 
     */
    private $db;
    
    /**
     * our constructor - make sure we call the parent's constructor! 
     */
    public function __construct() {
        parent::__construct();
        //see if we have a timezone that was specified
        if ($this->session->varIsSet('bzstats', 'timezone')) {
            $this->timezone = $this->session->get('bzstats', 'timezone');
        } else {
            $this->session->set('bzstats', 'timezone', 'GMT');
            $this->timezone = 'GMT';
        }
        
        //$this->timezone = 'Canada/Eastern';
        $this->timezone = 'GMT';
        
        //build the time strings that we need for all the queries
        //all SQL queries are passed GMT times
        //since the dates are all GMT in the DB
        //we pass $this->timezone setting to the DB, and the DB is responsible for timezone conversion
        date_default_timezone_set('GMT');
        
        $now = time();
        $min5 = 60*5; //5 minutes in seconds
        $min10 = 60*10; //10 minutes in seconds
        $day = 60*60*24; //day in seconds
        $week = 60*60*24*7; //week in seconds
        
        $this->curStart = date("Y-m-d H:i:s", $now-$min5);
        $this->dayStart = date("Y-m-d H:i:s", $now-$day);
        $this->weekStart = date("Y-m-d H:i:s", $now-$week);
        $this->end = date("Y-m-d H:i:s", $now);
        
        //grab a connection to our database
        $this->db = \Qore\IoC::Get('bzstats.statsmodel');
    }
    
    /**
     * allow all public methods to be executed 
     */
    public function __pre() {
        parent::__pre();
        $this->setExecutionState(true);
    }
    
    public function main_public_pre() {
        $this->cachePage();
    }
    /**
     * main stats page
     */
    public function main_public(array $args) {
        //our data container that we will pass to the view
        $data = array();

        //grab the current server count
        $data['curServers'] = $this->db->getServerCount($this->curStart, $this->end);
        //grab the daily server count
        $data['dayServers'] = $this->db->getServerCount($this->dayStart, $this->end);
        //grab the weekly server count
        $data['weekServers'] = $this->db->getServerCount($this->weekStart, $this->end);
        //the active players/servers
        $data['curStats'] = $this->db->getCurrentStats($this->timezone);
        //the most active time today
        $data['dayMostActive'] = $this->db->getPopularTime("most", $this->dayStart, $this->end, $this->timezone);
        //the least active time today
        $data['dayLeastActive'] = $this->db->getPopularTime("least", $this->dayStart, $this->end, $this->timezone);
        //the most active time this week
        $data['weekMostActive'] = $this->db->getPopularTime("most", $this->weekStart, $this->end, $this->timezone);
        //the least active time this week
        $data['weekLeastActive'] = $this->db->getPopularTime("least", $this->weekStart, $this->end, $this->timezone);
        //get the current most popular server
        $data['curPopularServer'] = $this->db->getMostPopularServer($this->curStart, $this->end, $this->timezone);
        //get the days most popular server from today
        $data['dayPopularServer'] = $this->db->getMostPopularServer($this->dayStart, $this->end, $this->timezone);
        //get the weeks most popular server from today
        $data['weekPopularServer'] = $this->db->getMostPopularServer($this->weekStart, $this->end, $this->timezone);
        //get player with most current wins
        $data['curPlayerWins'] = $this->db->getMostWins($this->curStart, $this->end);
        //get player with most day wins
        $data['dayPlayerWins'] = $this->db->getMostWins($this->dayStart, $this->end);
        //get player with most week wins
        $data['weekPlayerWins'] = $this->db->getMostWins($this->weekStart, $this->end);
        //get cur most losses/worst ratio
        $data['curWorstRatio'] = $this->db->getPlayerByRatio("worst", $this->curStart, $this->end);;
        //get day most losses/worst ratio
        $data['dayWorstRatio'] = $this->db->getPlayerByRatio("worst", $this->dayStart, $this->end);
        //get week most losses/worst ratio
        $data['weekWorstRatio'] = $this->db->getPlayerByRatio("worst", $this->weekStart, $this->end);
        //get cur least losses/best ratio
        $data['curBestRatio'] = $this->db->getPlayerByRatio("best", $this->curStart, $this->end);
        //get day least losses/best ratio
        $data['dayBestRatio'] = $this->db->getPlayerByRatio("best", $this->dayStart, $this->end);
        //get week least losses/best ratio
        $data['weekBestRatio'] = $this->db->getPlayerByRatio("best", $this->weekStart, $this->end);
        //get most cur TK
        $data['curTK'] = $this->db->getMostTK($this->curStart, $this->end);
        //get most day TK
        $data['dayTK'] = $this->db->getMostTK($this->dayStart, $this->end);
        //get most week TK
        $data['weekTK'] = $this->db->getMostTK($this->weekStart, $this->end);
        
        echo $this->twig->render('bzstatsMain.html.twig', array('data' => $data, 'timezone' => $this->timezone));
    }
    
    public function servers_public_pre() {
        $this->cachePage();
    }
    
    public function servers_public(array $args) {
        //our data container that we will pass the view
        $data = array();
        
        //get the current active server/player stats
        $data['curStats'] = $this->db->getCurrentStats($this->timezone);
        //the daily player/server stats
        $data['dailyServerStats'] = $this->db->getPlayerCounts($this->dayStart, $this->end);
        //the weekly player/server stats
        $data['weeklyServerStats'] = $this->db->getPlayerCounts($this->weekStart, $this->end);
        //get the current server listing
        $data['curServerList'] = $this->db->getActiveServerList($this->curStart, $this->end);
        //get the daily server list
        $data['dailyServerList'] = $this->db->getActiveServerList($this->dayStart, $this->end);
        //get the weekly server list
        $data['weeklyServerList'] = $this->db->getActiveServerList($this->weekStart, $this->end);
        //get the complete server list ordered by lastupsdate time
        $data['serverList'] = $this->db->getServerList($this->timezone);
        
        echo $this->twig->render('bzstatsServers.html.twig', array('data' => $data, 'timezone' => $this->timezone));
    }
    
    public function server_public_pre(array $args) {
        if (!count($args)==1) {
            $this->setExecutionState(false);
            throw new \Qore\Qexception("A Server Name Must be passed!", \Qore\Qexception::$BadRequest);
        } else {
            $this->cachePage();
        }
    }
    
    public function server_public(array $args) {
        //the array we give to our view
        $data = array();
        
        //grab the current connection info for the current server
        $data['serverDetails'] = $this->db->getSpecificServerDescription(urldecode($args[0]), $this->timezone);
        
        //of we can't find the server - throw an error and exit
        if (!is_array($data['serverDetails'])) {
            throw new \Qore\Qexception("Sorry, the server you are looking for can't be found", \Qore\Qexception::$NotFound);
        }
        
        $data['curAveragePlayers'] = $this->db->getSpecificServerAvgPlayers($args[0], $this->curStart, $this->end);
        $data['dayAveragePlayers'] = $this->db->getSpecificServerAvgPlayers($args[0], $this->dayStart, $this->end);
        $data['weekAveragePlayers'] = $this->db->getSpecificServerAvgPlayers($args[0], $this->weekStart, $this->end);
        $data['curMaxPlayers'] = $this->db->getSpecificServerMaxPlayers($args[0], $this->curStart, $this->end, $this->timezone);
        $data['dayMaxPlayers'] = $this->db->getSpecificServerMaxPlayers($args[0], $this->dayStart, $this->end, $this->timezone);
        $data['weekMaxPlayers'] = $this->db->getSpecificServerMaxPlayers($args[0], $this->weekStart, $this->end, $this->timezone);
        $data['curPlayers'] = $this->db->getSpecificServerPlayers($args[0], $this->curStart, $this->end);
        $data['dayPlayers'] = $this->db->getSpecificServerPlayers($args[0], $this->dayStart, $this->end);
        $data['weekPlayers'] = $this->db->getSpecificServerPlayers($args[0], $this->weekStart, $this->end);
        $data['allPlayers'] = $this->db->getSpecificServerPlayers($args[0], '19700101', $this->end);
        $data['curBestPlayerRatio'] = $this->db->getSpecificServerPlayerByRatio($args[0], 'best', $this->curStart, $this->end, $this->timezone);
        $data['dayBestPlayerRatio'] = $this->db->getSpecificServerPlayerByRatio($args[0], 'best', $this->dayStart, $this->end, $this->timezone);
        $data['weekBestPlayerRatio'] = $this->db->getSpecificServerPlayerByRatio($args[0], 'best', $this->weekStart, $this->end, $this->timezone);
        $data['curWorstPlayerRatio'] = $this->db->getSpecificServerPlayerByRatio($args[0], 'worst', $this->curStart, $this->end, $this->timezone);
        $data['dayWorstPlayerRatio'] = $this->db->getSpecificServerPlayerByRatio($args[0], 'worst', $this->dayStart, $this->end, $this->timezone);
        $data['weekWorstPlayerRatio'] = $this->db->getSpecificServerPlayerByRatio($args[0], 'worst', $this->weekStart, $this->end, $this->timezone);
        $data['curMostWins'] = $this->db->getSpecificServerMostWins($args[0], $this->curStart, $this->end, $this->timezone);
        $data['dayMostWins'] = $this->db->getSpecificServerMostWins($args[0], $this->dayStart, $this->end, $this->timezone);
        $data['weekMostWins'] = $this->db->getSpecificServerMostWins($args[0], $this->weekStart, $this->end, $this->timezone);
        $data['curTK'] = $this->db->getSpecificServerMostTK($args[0], $this->curStart, $this->end, $this->timezone);
        $data['dayTK'] = $this->db->getSpecificServerMostTK($args[0], $this->dayStart, $this->end, $this->timezone);
        $data['weekTK'] = $this->db->getSpecificServerMostTK($args[0], $this->weekStart, $this->end, $this->timezone);
        $data['server'] = $args[0];
        
        echo $this->twig->render('bzstatsServer.html.twig', array('data'=>$data, 'timezone' => $this->timezone));
    }
    
    public function players_public_pre() {
        $this->cachePage();
    }
    
    public function players_public(array $args) {
        $data = array();
        
        //the current players
        $data['curPlayers'] = $this->db->getCurrentPlayers($this->curStart, $this->end, $this->timezone);
        
        if (count($args)>0) {
            if ($args[0] == 'playernotfound') {
                $data['error']['playernotfound'] = true;
            }
        }
        
        echo $this->twig->render('bzstatsPlayers.html.twig', array('data' => $data, 'timezone' => $this->timezone));
    }
    
    public function player_public_pre(array $args) {
        if (!count($args)==1) {
            $this->setExecutionState(false);
            throw new \Qore\Qexception("A Player Name Must be passed!", \Qore\Qexception::$BadRequest);
        } else {
            $this->cachePage();
        }
    }
    
    public function player_public(array $args) {
        //the array we give to our view
        $data = array();
        $data['PlayerNameEncoded'] = $args[0];
        $data['PlayerName'] = urldecode($args[0]);
        
        //get the player's last seen date/details
        $data['PlayerLastSeen'] = $this->db->getPlayerSeenDetails('last', $data['PlayerName'], $this->timezone);
        
        //see if we have any data for the player - if not, error out...
        if (!is_array($data['PlayerLastSeen'])) {
            throw new \Qore\Qexception("Sorry, the player you are looking for can't be found", \Qore\Qexception::$NotFound);
        }
        
        //set the players first seen details
        $data['PlayerFirstSeen'] = $this->db->getPlayerSeenDetails('first', $data['PlayerName'], $this->timezone);
        
        //get the players best/worst ratio details
        $data['PlayerBestRatio'] = $this->db->getPlayerRatioDetails('best', $data['PlayerName'], $this->timezone);
        $data['PlayerWorstRatio'] = $this->db->getPlayerRatioDetails('worst', $data['PlayerName'], $this->timezone);
        
        //get the players most wins
        $data['PlayerMostWins'] = $this->db->getPlayerMostWinDetails($data['PlayerName'], $this->timezone);
        
        //get the players most losses
        $data['PlayerMostLosses'] = $this->db->getPlayerMostLossDetails($data['PlayerName'], $this->timezone);
        
        //get the players most TK's
        $data['PlayerMostTK'] = $this->db->getPlayerMostTKDetails($data['PlayerName'], $this->timezone);
        
        //get the players favorite servers
        $data['PlayerFavServers'] = $this->db->getPlayerFavoriteServers($data['PlayerName']);
        
        echo $this->twig->render('bzstatsPlayer.html.twig', array('data' => $data, 'timezone' => $this->timezone));
    }
    
    public function playersearch_public_pre() {
        if (!$this->httpPost) {
            $this->setExecutionState(false);
            throw new \Qore\Qexception("Sorry, this page only accepts POST Requests", \Qore\Qexception::$BadRequest);
        }
    }
    
    public function playersearch_public() {
        $data = array();
        if (count($this->httpPostData)>0) {
            //see if we can find players that match the requested player
            $playerSearch = $this->db->findPlayer($this->httpPostData['PlayerSearch']);

            if (count($playerSearch)>1) {
                //show a list of matching players
                $data['players'] = $playerSearch;
                echo $this->twig->render('bzstatsPlayerSearch.html.twig', array('data' => $data));
            } elseif (count($playerSearch) == 1) {
                //redirect to matching players page
                $this::redirect('bzstats', 'player', $playerSearch[0]['PlayerName']);
            } else {
                //no match found error..
                $this::redirect('bzstats', 'players', 'playerNotFound');
            }
        } else {
            $this::redirect('bzstats', 'players', 'playerNotFound');
        }
    }
}