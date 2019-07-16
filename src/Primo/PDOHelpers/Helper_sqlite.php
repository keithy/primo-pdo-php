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
        return $pdo->run("SELECT name FROM pragma_table_info( '{$table}' )")->fetchAllAsColumn();
    }

    function fileExt($env)
    {
        return isset($env['suffix']) ? $env['suffix'] : '.sqlite3';
    }

    function clobberDatabase($env)
    {
        return array_map('unlink', glob($env['dir'] . DIRECTORY_SEPARATOR . $env['name'] . $this->fileExt($env)));
    }

    function databaseExists($env)
    {
        return file_exists($env['dir'] . DIRECTORY_SEPARATOR . $env['name'] . $this->fileExt($env));
    }

    function copyDatabase($from, $to)
    {
        $fromPath = $from['dir'] . DIRECTORY_SEPARATOR . $from['name'] . $this->fileExt($from);
        PDO::helperFor($to)->copyDatabaseFromSQLiteTo($fromPath, $to);
    }

    function ensureDir($env)
    {
        is_dir($env['dir']) ?: mkdir($env['dir'], 01770, true); // ensure existence 
    }

    function copyDatabaseFromSQLiteTo($fromPath, $to)
    {

        $this->ensureDir($to);
        copy($fromPath, $to['dir'] . DIRECTORY_SEPARATOR . $to['name'] . $this->fileExt($to));
    }

    function copyDatabaseFromMySqlTo($from, $to)
    {
        /* left as an excercise for the reader */
    }

    function per_db($env, $scopeKey)
    {
        $env['name'] = $env['name'] . "_$scopeKey";
    }

    function per_table($env, $scopeKey)
    {
        $env['table_prefix'] = "{$scopeKey}_";
    }

    function initializePDO($pdo)
    {
        if ($pdo) {

            $pdo->sqliteCreateFunction('regexp',
                    function ($pattern, $data, $delimiter = '~', $modifiers = 'isuS') {
                if (isset($pattern, $data) === true) {
                    return (preg_match(sprintf('%1$s%2$s%1$s%3$s', $delimiter, $pattern, $modifiers), $data) > 0);
                }

                return null;
            }
            );
        }
        return $pdo;
    }
}
