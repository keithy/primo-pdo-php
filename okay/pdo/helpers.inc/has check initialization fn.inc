<?php

namespace fixture {

    global $PDO_CLASS;

    $HELPER = $PDO_CLASS::newHelperFor('sqlite');
    
    $reader = new \Primo\Phinx\ConfigReader(__DIR__ . "/../../_fixtures/phinx.php");
}

namespace ok {

    EXPECT('database file deleted');
    // pick a fixture and ensure it doesnt exist!
    $fixture = $reader->choose('one_user')->clobber(true);

    _('to not exist');
    assert(false === file_exists($HELPER->databasePath($fixture)));

    _('to not be initialised');
    assert(false === $HELPER->hasBeenInitialized($fixture));
}

namespace ok {

    EXPECT('opening it anyway');
 
    // open it anyway
    $pdo = new $PDO_CLASS($fixture);
    _('to create file');
    assert(true === file_exists($pdo->helper->databasePath($fixture)));

    _('still not initialised');
    assert(false === $pdo->helper->hasBeenInitialized($fixture));
}
