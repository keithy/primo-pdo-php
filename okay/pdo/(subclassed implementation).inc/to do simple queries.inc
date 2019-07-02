<?php

namespace fixture {
    
    $log = "";

    $pdo = pdoOnFixture('one_user');

    $pdo->addLog(function ($sql) use (& $log) {
        $log = $sql;
    });
    
}

namespace ok {

    EXPECT("simple query against 'one_user' fixture");

    $result = $pdo->run('SELECT count(*) FROM user')->fetchColumn();

    _("to return 1 as result");

    assert($result == 1);

    _("to log sql");

    assert($log == 'SELECT count(*) FROM user', "actual; {$log}");
}

namespace fixture {

    $log = "";

    $pdo = pdoOnFixture('seeded')->addLog(function($sql)use(&$log) {
        $log = $sql;
    });
}

namespace ok {

    EXPECT("Simple query against 'seeded' fixture");

    $log = "";


    $result = $pdo->run('SELECT * FROM user')->fetchAll();

    _("to return 6 results");

    assert(count($result) == 6, print_r($result, true));
}
 