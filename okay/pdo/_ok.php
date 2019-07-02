<?php

# _ok.php turns a directory of *.inc scripts into a test suite.
# doubles as the one-time setup script
# first time
if (require(__DIR__ . '/../../vendor/okay/okay/_okay.php')) return;

# second time - one-time setup code for this directory

require_once( __DIR__ . "/../../vendor/autoload.php");

function pdoOnFixture($choice)
{
    global $PDO_CLASS;

    $reader = new Primo\Phinx\ConfigReader(__DIR__ . "/../_fixtures/phinx.php");

    $fixture = $reader->choose($choice);
    
    $snapshot = $reader->choose($choice)->which('snapshots');
   
    if (!$snapshot->exists()) throw new Exception("Need to initialize databases via -I option");
 
    return new $PDO_CLASS($snapshot->copyTo($fixture));
}
