<?php
namespace Qore\Unreal;

interface iDbSessions {
    public function __construct(\Qore\Unreal\iDb $dbcon);
    public function read($sessionid);
    public function write($sessionid, $sessiondata, $serverSessionExists);
    public function sessionExists($sessionid);
    public function destroy($sessionid);
    public function gc($maxlifetime);
}