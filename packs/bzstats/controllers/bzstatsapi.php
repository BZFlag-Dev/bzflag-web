<?php
/**
 * API controller for ajax graph requests
 *
 * @author Ian Farr
 */
class BzstatsapiController extends \Qore\BaseController {
    private $db;
    private $printFormat;
    private $data;
    
    //make all the api publicly available
    public function __pre() {
        parent::__pre();
        $this->setExecutionState(true);
    }
    
    public function __construct() {
        parent::__construct();
        $this->db = \Qore\IoC::Get('bzstats.statsmodel');
        $this->printFormat = 'json';
        $this->data = array();
    }
    
    //our dispatcher - where we route api requests to
    public function dispatcher_public(array $args) {
        //see what format we will be printing out
        if (count($args) >= 2) {
            switch (strtolower($args[0])) {
                case 'xml':
                    $this->printFormat = "xml";
                    break;
                case 'json':
                    $this->printFormat = 'json';
                    break;
                case 'csv':
                    $this->printFormat = 'csv';
                    break;
                case 'jsarray':
                    $this->printFormat = 'jsarray';
                    break;
                default:
                    $this->showUsage();
            }
            
            //shift the array over - see what data we should get
            array_shift($args);
            switch (strtolower($args[0])) {
                case 'gettotalcount':
                    array_shift($args);
                    if (count($args)==2) {
                        $this->data = $this->db->getTotalCount($args[0], $args[1]);
                        $this->prnt();
                    } else {
                        $this->showUsage();
                    }
                    break;
                case 'gettotalplayercount':
                    array_shift($args);
                    if (count($args)==2) {
                        $this->data = $this->db->getTotalPlayerCount($args[0], $args[1]);
                        $this->prnt();
                    } else {
                        $this->showUsage();
                    }
                    break;
                case 'getsumedplayercount':
                    array_shift($args);
                    if (count($args)==2) {
                        $this->data = $this->db->getSumedPlayerCount($args[0], $args[1]);
                        $this->prnt();
                    } else {
                        $this->showUsage();
                    }
                    break;
                case 'gettotalservercount':
                    array_shift($args);
                    if (count($args)==2) {
                        $this->data = $this->db->getTotalServerCount($args[0], $args[1]);
                        $this->prnt();
                    } else {
                        $this->showUsage();
                    }
                    break;
                case 'getserverstats':
                    array_shift($args);
                    if (count($args)==3) {
                        $this->data = $this->db->getSpecificServerStats($args[0], $args[1], $args[2]);
                        $this->prnt();
                    } else {
                        $this->showUsage();
                    }
                    break;
                case 'getplayeractivetimes':
                    array_shift($args);
                    if (count($args)==3) {
                        $this->data = $this->db->getPlayerActiveTimes(urldecode($args[0]), $args[1], $args[2]);
                        $this->prnt();
                    } else {
                        $this->showUsage();
                    }
                    break;
                case 'getplayerscores':
                    array_shift($args);
                    if (count($args)==3) {
                        $this->data = $this->db->getPlayerScores(urldecode($args[0]), $args[1], $args[2]);
                        $this->prnt();
                    } else {
                        $this->showUsage();
                    }
                    break;
                default:
                    $this->showUsage();
            }
        } else {
            $this->showUsage();
        }
    }
    
    private function showUsage() {
        echo "
        <html><body>
        <h1>BzstatsWeb - API</h1>
        <p>
            Each API request must contain a couple of paramaters, and they must all be supplied!<br />
            Each request should be formatted as follows: /api/{outputFormat}/{command}/{args}/{args}/{args}... <br />
            <ul>
                <li>{outputFormat} : should be one of jsarray, xml, csv, json</li>
                <li>{command} : should be a valid comand - see below</li>
                <li>{args} : one of more arguments for the command, each command accepts different arguments - so check below</li>
                <li>{startDate} and {endDate} are all formatted as YYYYMMDDHHMMSS
            </ul>
            Valid Commands:
            <ul>
                <li>getTotalCount/{startDate}/{endDate}</li>
                <li>getTotalPlayerCount/{startDate}/{endDate}</li>
                <li>getTotalServerCount/{startDate}/{endDate}</li>
                <li>getServerStats/{startDate}/{endDate}</li>
                <li>getSumedPlayerCount/{startDate}/{endDate}</li>
                <li>getPlayerActiveTimes/{playername}/{startDate}/{endDate}</li>
                <li>getPlayerScores/{playername}/{startDate}/{endDate}</li>
            </ul>
        </p>
        <p>Examples:</p>
        <ul>
            <li>/bzstatsapi/jsarray/getTotalCount/20120130121537/20120202235959</li>
            <li>/bzstatsapi/xml/getTotalCount/20120130121537/20120202235959</li>
            <li>/bzstatsapi/csv/getTotalCount/20120130121537/20120202235959</li>
            <li>/bzstatsapi/json/getTotalcount/20120130121537/20120202235959</li>
            <li>/bzstatsapi/xml/getTotalPlayerCount/20120130121537/20120202235959</li>
            <li>/bzstatsapi/json/getTotalServerCount/20120130121537/20120202235959</li>
            <li>/bzstatsapi/csv/getServerStats/{serverName}/20120130121537/20120202235959</li>
            <li>/bzstatsapi/csv/getSumedPlayerCount/20120130121537/20120202235959</li>
            <li>/bzstatsapi/xml/getPlayerActiveTimes/{playername}/20120130121537/20120202235959</li>
            <li>/bzstatsapi/xml/getPlayerScores/{playername}/20120130121537/20120202235959</li>
        </ul>
        </body></html>
        "; die;
    }
    
    private function prnt() {
        $prntFunc = 'prnt'.$this->printFormat;
        if (method_exists($this, $prntFunc)) {
            $this->$prntFunc();
        } else {
            echo "can't find printer: ".$prntFunc;
            die;
        }
    }
    
    /**
     * expects an array like
     * array(
     *      [0] => array ( 'Column1' => $value, 'Column2' => $value, ... )
     *      ...      
     */
    private function prntxml() {
        $xml = new SimpleXMLElement('<data/>');
        
        foreach($this->data as $id => $row){
            if (is_array($row)) {
                $curRow = $xml->addChild("row");
                $curRow->addAttribute("id", $id);
                foreach($row as $column => $value) {
                    $curRow->addChild($column, $value);
                }
            } else {
                $xml->addChild($id, $row);
            }
        }
        echo $xml->asXML();
    }
    
    private function prntjson() {
        echo json_encode($this->data);
    }
    
    /**
     * expects an array like
     * array(
     *      [0] => array ( 'Column1' => $value, 'Column2' => $value, ... )
     *      ...      
     */
    private function prntcsv() {
        $csv = "";
        $totalRows = count($this->data);
        $r = 1;
        foreach($this->data as $id => $row){
            if (is_array($row)) {
                //get the column names
                if ($r === 1) {
                    $numCols = count($row);
                    $c = 1;
                    foreach(array_keys($row) as $columnName) {
                        if ($c < $numCols) {
                            $csv .= $columnName . ",";
                        } else {
                            $csv .= $columnName . "\n";
                        }
                        $c++;
                    }
                }
                
                $i = 1;
                foreach($row as $column => $value) {
                   if ( $i < $numCols) {
                        $csv .= $value . ",";
                    } else {
                        $csv .= $value . "\n";
                    }
                    $i++;
                }
                $r++;
            }
        }
        echo $csv;
    }
    
    /**
     * expects an array like
     * array(
     *      [0] => array ( 'Timestamp' => $value, 'Column2' => $value, ... )
     *      ...      
     */
    private function prntjsarray() {
        $jsArray = "[\n";
        $totalRows = count($this->data);
        $r = 1;
        foreach($this->data as $id => $row){
            if (is_array($row)) {
                $i = 1;
                $numCols = count($row);
                foreach($row as $column => $value) {
                    if ($i == 1) {
                        $jsArray .= '    [ new Date("' . $value . '")';
                    } elseif ( $i < $numCols) {
                        $jsArray .= "," . $value;
                    } else {
                        $jsArray .= "," . $value . "]";
                    }
                    $i++;
                }
                if ($r < $totalRows) {
                    $jsArray .= ",\n";
                } else {
                    $jsArray .= "\n";
                }
                $r++;
            }
        }
        $jsArray .= "]\n";
        echo $jsArray;
    }
}