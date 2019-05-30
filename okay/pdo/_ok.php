<?php

# _ok.php turns a directory of *.inc scripts into a test suite.
# doubles as the one-time setup script

global $OKAY_SUITE;
$OKAY_SUITE = __DIR__;

# first time
if (true !== require_once(__DIR__ . '/../_okay.php')) return;

# second time - initialisation code - one-time setup for this directory
require_once( __DIR__ . "/../../vendor/autoload.php");

define('SNAPSHOTS', __DIR__ . "/../_snapshots");
define('FIXTURES', __DIR__ . "/../_fixtures");
define('CONFIG', __DIR__ . "/../_snapshots/phinx.php");

$needsInitializing = empty(glob(SNAPSHOTS . '/*.sqlite3'));

if ($needsInitializing) {

    chdir(SNAPSHOTS);

    $phinx = new \Phinx\Wrapper\TextWrapper(new \Phinx\Console\PhinxApplication());
    $phinx->setOption('configuration', SNAPSHOTS . '/phinx.php');

    echo $phinx->getMigrate('empty', '0001'); // only applies the first migration
    echo $phinx->getMigrate('one_user'); // applies all first migrations

    echo $phinx->getMigrate('seeded');
    echo $phinx->getSeed('seeded', null, 'UserSeeder'); // applies seeds
}

function pdoOnFixture($environment)
{
    global $PDO_CLASS;
    chdir(FIXTURES); // needed?

    $config = Primo\Phinx\ConfigReader::readFrom(CONFIG, $environment);

    ok\delete_all_matching(FIXTURES, '*.sqlite3');
    ok\copy_all(SNAPSHOTS, FIXTURES, "{$config['name']}.sqlite3");

    return new Primo\PDOSubclassed\PDO($config);
}
