<?php
namespace Qore\Factory;

/**
 * dbfactory loads the appropriate database class
 *
 * @author Ian Farr
 */
class DbConFactory {
    public $class;
    public static function build($dbinstance, $pack = '') {
        $type = $GLOBALS['cfg']['db'][$dbinstance]['type'];
        if ($pack == '') {
            $class = "Qore\\Dbcon\\Db" . ucfirst(strtolower($type));
        } else {
            $class = "Packs\\$pack\\Dbcon\\Db" . ucfirst(strtolower($type));
        }
        if (!class_exists($class)) {
            throw new \Qore\Qexception('Missing db class: '.$class, \Qore\Qexception::$InternalError);
        }
        return new $class($dbinstance);
    }
}