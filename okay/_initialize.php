<?php
 
require_once( __DIR__ . "/../vendor/autoload.php");

$reader = new Primo\Phinx\ConfigReader(__DIR__ . "/_fixtures/phinx.php");

// To reflect the travis-ci test environment correctly
// ensure that the tmp-dir does not exist prior

shell_exec('rm -rf "/tmp/primo-pdo" || true');

foreach( $reader->choices() as $choice )
{
    $reader->choose($choice)->clobber( true );
    $reader->choose($choice)->which('snapshots')->create( true );
}
 