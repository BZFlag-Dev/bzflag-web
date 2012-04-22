<?php
namespace Qore\Factory;

/**
 * dbfactory loads the appropriate database class
 *
 * @author Ian Farr
 */
class DbFactory {
    public $class;
    public static function build($pack, $dbinstance, $target, $dbh) {
        $type = $GLOBALS['cfg']['db'][$dbinstance]['type'];
        if ($pack == '')  {
            $class = "Qore\\Dbadapters\\Db" . ucfirst(strtolower($type)) . ucfirst(strtolower($target));
        } else {
            $class = "Packs\\$pack\\Dbadapters\\Db" . ucfirst(strtolower($type)) . ucfirst(strtolower($target));
        }
        if (!class_exists($class)) {
                throw new \Qore\Qexception('Missing db class: '.$class, \Qore\Qexception::$InternalError);
            }
        return new $class($dbh);
    }
}