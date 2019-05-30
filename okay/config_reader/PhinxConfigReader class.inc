<?php

namespace ok {

    use Primo\Phinx\ConfigReader;
    use Primo\Phinx\DbEnvironmentNotFound;


    $file = __DIR__ . "/../_snapshots/phinx.php";


    expect("reading environment named 'empty' from fixture file");

    $config = ConfigReader::readFrom($file, 'empty');

    should("return a dictionary");

    assert(is_array($config));

    should("include the choice itself");

    assert($config['choice'] == 'empty');

    //
    //

    expect("reading environment named 'missing' from fixture file");

    should("raise DBEnvironmentNotFound exception");

    try {
        $caught = false;
        $config = ConfigReader::readFrom($file, 'missing');
    } catch (DbEnvironmentNotFound $ex) {
        $caught = true;
    }
    assert($caught);
}

 
