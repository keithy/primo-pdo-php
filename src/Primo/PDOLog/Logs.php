<?php

// Anticipated Usage
// Subclass this and add the trait that adapts it to your logging framework of choice

namespace Primo\PDOLog;

class Logs
{

    protected $logs = [];

    function logAdd($log = null) // default $this log to stderr
    {
        $logs[] = $log ?? $this;
    }

    function logThis($sql, $ms, $result = false)
    {
        foreach ($this->logs as $log) {
            $log->pdoLog(sql, $ms, $result);
        }
    }

    function pdoLog($sql, $result, $ms)
    {
        error_log(sprintf("%4.2fms: %s", $ms, $sql));
    }

}
