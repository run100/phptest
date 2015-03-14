<?php
/**
 * User: Run
 * Date: 下午8:42 15-3-22
 * File: pear.php
 * Desc: 
 */

require_once "System/Daemon.php";
// Bare minimum setup
System_Daemon::setOption("appName", "simple");
System_Daemon::setOption("authorEmail", "kevin@example.com");

// Spawn Deamon!
System_Daemon::start();

// Your PHP Here!
while (true) {
    doTask();
}

// Stop daemon!
System_Daemon::stop();