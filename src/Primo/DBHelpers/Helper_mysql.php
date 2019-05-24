<?php

namespace Primo\DBHelpers;

class Helper_sqlite {

    function dsn($config) {

        $dsn = 'mysql:host=' . $config['host'] . ';port=' . ($config['port'] ?? 3306);
        $dsn .= ';charset=' . ($config['charset'] ?? 'utf8');
        if ('' !== $config['database']) $dsn .= ';dbname=' . $config['database'];

        return $dsn;
    }

    function CONCAT($list) {
        return "CONCAT(" . implode(', ', $list) . ")";
    }

    function columnsOfTable($pdo, $table) {
        return explode(',', $pdo->run("select group_concat(name, ',') from PRAGMA table_info( {$table} )")->fetchColumn());
    }

}
