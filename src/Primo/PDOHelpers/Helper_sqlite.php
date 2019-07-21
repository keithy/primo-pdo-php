<?php

namespace Primo\PDOHelpers;

use \Primo\PDOSubclassed\PDO;

class Helper_sqlite
{
    const adapter = 'sqlite';

    function dsn($env)
    {
        return is_string($env) ? $env : 'sqlite:' . $this->databasePath($env);
    }

    // identifies this specific database (for use as a cache key)
    function databaseIdentifier($env)
    {
        return $this->databasePath($env);
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
        return array_map('unlink', glob($this->databasePath($env)));
    }

    function databasePath($env)
    {
        return $env['dir'] . DIRECTORY_SEPARATOR . $env['database'] . $this->fileExt($env);
    }

    // dont count if the file exists but is empty
    function hasBeenInitialized($env)
    {
        $exists = true;
        $path = $this->databasePath($env);
        if ($fp = @fopen($path, 'rb')) {
            if (false === fgetc($fp)) $exists = false;
            fclose($fp);
        } else $exists = false;
        return $exists;
    }

    function copyDatabase($from, $to)
    {
        $fromPath = $this->databasePath($from);
        PDO::helperFor($to)->copyDatabaseFromSQLiteTo($fromPath, $to);
    }

    function ensureDir($env)
    {
        is_dir($env['dir']) ?: mkdir($env['dir'], 01770, true); // ensure existence 
    }

    function copyDatabaseFromSQLiteTo($fromPath, $to)
    {

        $this->ensureDir($to);
        $toPath = $this->databasePath($to);
        array_map('unlink', glob($toPath));
        copy($fromPath, $toPath);
    }

    function copyDatabaseFromMySqlTo($from, $to)
    {
        /* left as an excercise for the reader */
    }

//    function per_db($env, $scopeKey)
//    {
//        $env['database'] = $env['database'] . "_$scopeKey";
//    }
//
//    function per_table($env, $scopeKey)
//    {
//        $env['table_prefix'] = "{$scopeKey}_";
//    }

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
