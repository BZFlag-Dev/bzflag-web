<?php
namespace Packs\Bzstats\Unreal;

/**
 * Base interface for all database adapters for bzstats. dbadapters should also implement \Qore\Interfaces\Basedb
 *
 * @author Ian Farr
 */
Interface iDbStats {
   public function getServerCount($startDate, $endDate);
   public function getCurrentStats();
   public function getPlayerCounts($startDate, $endDate);
   public function getPopularTime($type, $startDate, $endDate);
   public function getMostPopularServer($startDate, $endDate);
   public function getMostWins($startDate, $endDate);
   public function getPlayerByRatio($type, $startDate, $endDate);
   public function getMostTK($startDate, $endDate);
   public function getTotalCount($startDate, $endDate);
   public function getTotalPlayerCount($startDate, $endDate);
   public function getSumedPlayerCount($startDate, $endDate);
   public function getTotalServerCount($startDate, $endDate);
   public function getActiveServerList($startDate, $endDate);
   public function getServerList();
   public function getSpecificServerStats($serverName, $startDate, $endDate);
   public function getSpecificServerMaxPlayers($serverName, $startDate, $endDate);
   public function getSpecificServerAvgPlayers($serverName, $startDate, $endDate);
   public function getSpecificServerDescription($serverName);
   public function getSpecificServerPlayers($serverName, $startDate, $endDate);
   public function getSpecificServerMostWins($serverName, $startDate, $endDate);
   public function getSpecificServerPlayerByRatio($servername, $type, $startDate, $endDate);
   public function getSpecificServerMostTK($serverName, $startDate, $endDate);
   public function getCurrentPlayers($startDate, $endDate);
   public function getPlayerSeenDetails($type, $playerName);
   public function getPlayerRatioDetails($type, $playerName);
   public function getPlayerMostWinDetails($playerName);
   public function getPlayerMostLossDetails($playerName);
   public function getPlayerMostTKDetails($playerName);
   public function getPlayerFavoriteServers($playerName);
   public function getPlayerActiveTimes($playerName, $startDate, $endDate);
   public function getPlayerScores($playerName, $startDate, $endDate);
   public function findPlayer($playerName);
}