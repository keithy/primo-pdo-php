<?php

namespace Primo\PDOHelpers;

use Primo\PDOSubclassed\PDO;
use \Primo\PDOSubclassed\PDO;

class Helper_mysql
{
    const adapter = 'mysql';

    function dsn($env)
    {
        if (is_string($env)) return $env;
       
        $dsn = 'mysql:host=' . $env['host'] . ';port=' . (isset($env['port']) ? $env['port'] : 3306);
        $dsn .= ';charset=' . (isset($env['charset']) ? $env['charset'] : 'utf8');
        if ('' !== $env['database']) $dsn .= ';dbname=' . $env['database'];

        return $dsn;
    }

    function CONCAT($list)
    {
        return "CONCAT(" . implode(', ', $list) . ")";
    }

    function columnsOfTable($pdo, $table)
    {
        return explode(',', $pdo->run("select group_concat(name, ',') from PRAGMA table_info( {$table} )")->fetchColumn());
    }

    function clobberDatabase($env)
    {
        $pdo = new PDO($env, [database => ""]);
        return $pdo->query("DROP DATABASE {$env['name']}");
    }

    function copyDatabase($from, $to)
    {
        PDO::helperFor(to)->copyDatabaseFromMySqlTo($from, $to);
    }

    function copyDatabaseFromSQLiteTo($from, $to)
    {
        /* left as an excercise for the reader */
    }

    function copyDatabaseFromMySqlTo($from, $to)
    {
        /* left as an excercise for the reader */
    }
}
