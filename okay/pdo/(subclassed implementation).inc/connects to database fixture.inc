<?php

namespace ok {

    EXPECT("simple query against 'one_user' fixture");

    $log = "";

    $pdo = pdoOnFixture('one_user');

    $pdo->addLog(function ($sql) use (& $log) {
        $log = $sql;
    });

    $result = $pdo->run('SELECT count(*) FROM users')->fetchColumn();

    _("to return 1 result");

    assert($result == 1);

    _("to log sql");

    assert($log == 'SELECT count(*) FROM users', "no");
}

namespace ok {

    EXPECT("Simple query against 'seeded' fixture");

    $log = "";

    $pdo = pdoOnFixture('seeded')->addLog(function($sql)use(&$log) {
        $log = $sql;
    });

    $result = $pdo->run('SELECT * FROM users')->fetchAll();

    _("to return 11 results");

    assert(count($result) == 11, print_r($result,true));
}

namespace ok {

    EXPECT("prepared statements with ?s");

    $stmt = $pdo->run('SELECT count(*) FROM users WHERE username = ? AND email = ?', 'admin', 'admin@admin.com');

    _("to return valid result");

    assert(1 == $stmt->fetchColumn());

    _("and log sql");

    $prepared = "SELECT count(*) FROM users WHERE username = `admin` AND email = `admin@admin.com`";

    assert($log == $prepared);
}

namespace ok {

    EXPECT("prepared statements with :named args");

    $stmt = $pdo->run('SELECT count(*) FROM users WHERE username = :user AND email = :email', [':user' => 'admin', ':email' => 'admin@admin.com']);

    _("to return valid result");

    assert(1 == $stmt->fetchColumn());

    _("and log sql");

    assert($log == $prepared);
}