<?php

namespace fixture {

    $log = "";

    $pdo = pdoOnFixture('seeded')->addLog(function($sql)use(&$log) {
        $log = $sql;
    });
    
}

namespace ok {

    EXPECT("query returning Objects");

    // normal query
    $data = $pdo->run("SELECT * FROM user")->fetchAll(\PDO::FETCH_CLASS);
    assert(6 == count($data));

    // prepared statement
    $data = $pdo->run("SELECT * FROM user WHERE username != :user", [':user' => 'admin'])->fetchAll(\PDO::FETCH_CLASS);

    assert(5 == count($data), count($data));

    // simplified prepared statement
    $stmt = $pdo->run("SELECT * FROM user WHERE username = ?", 'admin')->asObjects();

    $data = $stmt->fetchAll();
    assert(1 == count($data), count($data));
}

