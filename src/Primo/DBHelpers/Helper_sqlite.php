<?php

namespace Primo\DBHelpers;

class Helper_sqlite {
    
    function dsn($config) {
        
        $dsn = $config;
        if (is_array($config)) {
            $dsn = 'sqlite:' . $config['database'] . '.sqlite3';
        }

        return $dsn;
    }

    function CONCAT($list) {
        return implode(' || ', $list);
    }

    function columnsOfTable($pdo, $table) {
        return $pdo->run("SELECT name FROM pragma_table_info( '{$table}' )")->fetchAllAsColumn();
    }

}
