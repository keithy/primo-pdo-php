<?php

namespace fixture {

    global $PDO_CLASS;

    $PDO_CLASS = "Primo\PDOSubclassed\PDO";

    $log = "";

    $pdo = pdoOnFixture('one_user');

    $pdo->addLog(function ($sql) use (& $log) {
        $log = $sql;
    });
}

namespace ok {

    EXPECT("asking for columnNamesOfTable('user')");

    $data = $pdo->columnsOfTable("user");

    _("to return array of 11 names");
    
    assert(11 == count($data), print_r($data, true));
}