<?php
 
require_once( __DIR__ . "/../vendor/autoload.php");

$reader = new Primo\Phinx\ConfigReader(__DIR__ . "/_fixtures/phinx.php");
 
foreach( $reader->choices() as $choice )
{
    $reader->choose($choice)->clobber();
    $reader->choose($choice)->which('snapshots')->recreate();
}
 