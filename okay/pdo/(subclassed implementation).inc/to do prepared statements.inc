<?php

namespace fixture {

    $log = "";

    $pdo = pdoOnFixture('seeded')->addLog(function($sql)use(&$log) {
        $log = $sql;
    });
}

namespace ok {

    EXPECT("prepared statements with ?s");

    $stmt = $pdo->run('SELECT count(*) FROM user WHERE username = ? AND email = ?', 'admin', 'admin@admin.com');

    _("to return a valid result");

    assert(1 == $stmt->fetchColumn());

    _("and log sql");

    $prepared = "SELECT count(*) FROM user WHERE username = `admin` AND email = `admin@admin.com`";

    assert($log == $prepared, "actual: {$log}");
}

namespace ok {

    EXPECT("prepared statements with :named args");

    $stmt = $pdo->run('SELECT count(*) FROM user WHERE username = :user AND email = :email', [':user' => 'admin', ':email' => 'admin@admin.com']);

    _("to return valid result");

    assert(1 == $stmt->fetchColumn());

    _("and log sql");

    assert($log == $prepared, "actual: {$log}");
}

namespace ok {

    EXPECT("prepared statements with :named args supplied without colons");

    $stmt = $pdo->run('SELECT count(*) FROM user WHERE username = :user AND email = :email', ['user' => 'admin', 'email' => 'admin@admin.com']);

    _("to return valid result");

    assert(1 == $stmt->fetchColumn());

    _("and log sql");

    assert($log == $prepared, "actual; {$log}");
}
 