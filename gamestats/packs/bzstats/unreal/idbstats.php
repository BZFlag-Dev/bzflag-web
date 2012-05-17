<?php
namespace Packs\Bzstats\Unreal;

/**
 * Base interface for all database adapters for bzstats. dbadapters should also implement \Qore\Interfaces\Basedb
 *
 * @author Ian Farr
 */
Interface iDbStats {
   public function getServerCount($startDate, $endDate);
   public function getCurrentStats($tz);
   public function getPlayerCounts($startDate, $endDate);
   public function getPopularTime($type, $startDate, $endDate, $tz);
   public function getMostPopularServer($startDate, $endDate, $tz);
   public function getMostWins($startDate, $endDate);
   public function getPlayerByRatio($type, $startDate, $endDate);
   public function getMostTK($startDate, $endDate);
   public function getTotalCount($startDate, $endDate, $tz);
   public function getTotalPlayerCount($startDate, $endDate, $tz);
   public function getSumedPlayerCount($startDate, $endDate, $tz);
   public function getTotalServerCount($startDate, $endDate, $tz);
   public function getActiveServerList($startDate, $endDate);
   public function getServerList($tz);
   public function getSpecificServerStats($serverName, $startDate, $endDate, $tz);
   public function getSpecificServerMaxPlayers($serverName, $startDate, $endDate, $tz);
   public function getSpecificServerAvgPlayers($serverName, $startDate, $endDate);
   public function getSpecificServerDescription($serverName, $tz);
   public function getSpecificServerPlayers($serverName, $startDate, $endDate);
   public function getSpecificServerMostWins($serverName, $startDate, $endDate, $tz);
   public function getSpecificServerPlayerByRatio($servername, $type, $startDate, $endDate, $tz);
   public function getSpecificServerMostTK($serverName, $startDate, $endDate, $tz);
   public function getCurrentPlayers($startDate, $endDate, $tz);
   public function getPlayerSeenDetails($type, $playerName, $tz);
   public function getPlayerRatioDetails($type, $playerName, $tz);
   public function getPlayerMostWinDetails($playerName, $tz);
   public function getPlayerMostLossDetails($playerName, $tz);
   public function getPlayerMostTKDetails($playerName, $tz);
   public function getPlayerFavoriteServers($playerName);
   public function getPlayerActiveTimes($playerName, $startDate, $endDate, $tz);
   public function getPlayerScores($playerName, $startDate, $endDate, $tz);
   public function findPlayer($playerName);
}