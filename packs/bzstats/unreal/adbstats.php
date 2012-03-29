<?php
namespace Packs\Bzstats\Unreal;

/**
 * Baseclass for all bzstats database adapters. This ensures that other adapters written for other
 * database systems will contain the needed classes
 * 
 * @author Ian Farr 
 */
abstract class aDbStats implements \Packs\Bzstats\Unreal\iDbStats {
    /**
     * @var PDO $dbh
     */
    protected $dbh;

    /**
     * @var PDOStatement  $sth
     */
    protected $sth;
}