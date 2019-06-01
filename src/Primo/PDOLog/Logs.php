<?php

// Usage: just add your callbacks
// $pdo->addLog( function( $sql, $ms, $result) { ... }; );

namespace Primo\PDOLog;

class Logs
{
    protected $logs = [];
    public $tag;

    function __construct($tag = 'PDO')
    {
        $this->tag = $tag;
    }

    function add($log) // default $this log to stderr
    {
        $this->logs[] = $log;
    }

    function logThis($message, $tag = null, $ms = null, $result = true)
    {
        //**/$i = 1;
        foreach ($this->logs as $log) {
            //**/echo $i++, ": {$message}\n";
            $log($message, $tag, $ms, $result);
        }
    }

    function __invoke($message, $tag, $ms, $result)
    {
        $tag = isset($tag) ? $tag : $this->tag;

        if (null === $ms) $line = "[{$tag}] {$message}";
        else $line = sprintf("[%s] %4.2fms: %s%s", $tag, $ms, $message, ($result ? '' : '[FAILED]'));

        error_log($line);
    }
}
