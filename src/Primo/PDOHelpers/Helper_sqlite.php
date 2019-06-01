<?php

namespace Primo\PDOHelpers;

use \Primo\PDOSubclassed\PDO;

class Helper_sqlite
{
    const adapter = 'sqlite';

    function dsn($env)
    {
        return is_string($env) ? $env : 'sqlite:' . $env['dir'] . DIRECTORY_SEPARATOR . $env['database'] . '.sqlite3';
    }

    function CONCAT($list)
    {
        return implode(' || ', $list);
    }

    function columnsOfTable($pdo, $table)
    {
        return $pdo->run("SELECT name FROM pragma_table_info( `{$table}` )")->fetchAllAsColumn();
    }

    function clobberDatabase($env)
    {
        return array_map('unlink', glob($env['dir'] . DIRECTORY_SEPARATOR . $env['name'] . '.sqlite3'));
    }

    function copyDatabase($from, $to)
    {

        PDO::helperFor($to)->copyDatabaseFromSQLiteTo($from, $to);
    }

    function copyDatabaseFromSQLiteTo($from, $to)
    {
        is_dir($to['dir']) ?: mkdir($to['dir']); // ensure existence\

        foreach (glob($from['dir'] . DIRECTORY_SEPARATOR . $from['name'] . '.sqlite3') as $path) {

            copy($path, $to['dir'] . DIRECTORY_SEPARATOR . $to['name'] . '.sqlite3');
        }
    }

    function copyDatabaseFromMySqlTo($from, $to)
    {
        /* left as an excercise for the reader */
    }
}
