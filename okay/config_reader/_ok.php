<?php

# _ok.php turns a directory of *.inc scripts into a test suite.
# doubles as the one-time setup script

global $OKAY_SUITE;
$OKAY_SUITE = __DIR__;

# first time
if (true !== require_once(__DIR__ . '/../_okay.php')) return;

# second time
foreach (glob(__DIR__ . '/../../src/Primo/Phinx/*.php') as $file)
    require_once($file);
  
