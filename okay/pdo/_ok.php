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

function pdoOnFixture($choice)
{
    global $PDO_CLASS;

    $reader = new Primo\Phinx\ConfigReader(__DIR__ . "/../_fixtures/phinx.php");

    $fixture = $reader->choose($choice);
    $snapshot = $reader->choose($choice)->which('snapshots');

    return new $PDO_CLASS($snapshot->copyTo($fixture));
}
