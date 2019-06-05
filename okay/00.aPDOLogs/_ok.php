<?php

# _ok.php turns a directory of *.inc scripts into a test suite.
# doubles as the one-time setup script
 
global $OKAY_SUITE;
$OKAY_SUITE = __DIR__;

# first time
if (!defined('__OKAY__')) {
    require(__DIR__ . '/../_okay.php');
    return;
}

# second time - initialisation code - one-time setup for this directory
require_once( __DIR__ . "/../../vendor/autoload.php");
