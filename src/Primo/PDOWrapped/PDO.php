<?php

namespace Primo\PDOWrapped;

use Primo\PDOWrapped\PDOStatement;

class PDO extends \Primo\PDOSubclassed\PDO {

    function defaultOptions() {
        return [
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ];
    }
    function query($sql)
    {
        return new PDOStatement($this, parent::query($sql));
    }
    
    function prepare($sql, $options = [])
    {
        return new PDOStatement($this, parent::prepare($sql, $options));        
    }
}
