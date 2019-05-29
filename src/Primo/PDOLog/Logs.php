<?php

// Anticipated Usage
// Subclass this and add the trait that adapts it to your logging framework of choice

namespace Primo\PDOLog;

class Logs
{

    protected $logs = [];

    function logAdd($log = null) // default $this log to stderr
    {
        $this->logs[] = $log ?? $this;
    }

    function logThis($sql, $ms, $result = false)
    {
        foreach ($this->logs as $log) {
            $log->pdoLog($sql, $ms, $result);
        }
    }

    function pdoLog($sql, $ms, $result)
    {
        error_log(sprintf("%4.2fms: %s", $ms, $sql));
    }

}
