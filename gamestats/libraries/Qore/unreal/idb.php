<?php
namespace Qore\Unreal;

interface iDb {
    /* connects to the specified database instance, creates the PDO instance */
    public function __construct($dbInstance);
    
    /* kills the PDO instance */
    public function __destruct();
    
    /* returns a MySQL PDO instance */
    public function getInstance();
}