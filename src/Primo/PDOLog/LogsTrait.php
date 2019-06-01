<?php

namespace Primo\PDOLog;

// Usage: just add your callbacks
// $pdo->addLog( function( $sql, $ms, $result) { ... }; );

/*
 * 1) false - turn off logging
 * 2) null  - add default logger to error_log
 * 3) fn    - add callback
 */


trait LogsTrait
{
    public $logs;

    /*
     * true - initialises logs with default callable PDOLog\Logs (error_log)
     * false - disable
     * string - callable
     * fn - callback function
     */

    function addLog($option, $tag = null)
    {
        if (!isset($this->logs)) $this->logs = new Logs($tag);

        switch (true) {
            case (true === $option):
                //**/echo "Add Log: true\n";
                $this->logs->add($this->logs);
                break;
            case (false === $option):
                //**/echo "Add Log: false\n";
                $this->logs = null;
                break;
            case (is_string($option)): // invokable
                //**/echo "Add Log: string\n";
                $this->logs->add(new $option());
                break;
            case (is_callable($option)):
                //**/echo "Add Log: callable\n";
                $this->logs->add($option);
                break;
            default:
                \Exception("Unknown logging option");
        }
        return $this;
    }
}
