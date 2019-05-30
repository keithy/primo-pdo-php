<?php
 
// Usage: just add your callbacks
// $pdo->addLog( function( $sql, $ms, $result) { ... }; );

namespace Primo\PDOLog;

class Logs
{

    protected $logs = [];

    function logAdd($log = null) // default $this log to stderr
    {
        $this->logs[] = isset($log) ? $log : $this;
    }

    function logThis($sql, $ms, $result = false)
    {
        foreach ($this->logs as $log) {
            $log($sql, $ms, $result);
        }
    }

    function __invoke($sql, $ms, $result)
    {
        error_log(sprintf("%4.2fms: %s", $ms, $sql));
    }
}
