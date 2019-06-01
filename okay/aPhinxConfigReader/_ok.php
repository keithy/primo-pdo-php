<?php

# _ok.php turns a directory of *.inc scripts into a test suite.
# doubles as the one-time setup script

global $OKAY_SUITE;
$OKAY_SUITE = __DIR__;

# first time
if (true !== require_once(__DIR__ . '/../_okay.php')) return;

require_once( __DIR__ . "/../../vendor/autoload.php");

global $CONFIG, $READER;

$READER = new \Primo\Phinx\ConfigReader(__DIR__ . "/../_fixtures/phinx.php");
$CONFIG = $READER->data;
