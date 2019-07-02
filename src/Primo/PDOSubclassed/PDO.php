<?php

namespace Primo\PDOSubclassed;

use Primo\PDOLog\LogsTrait;

class PDO extends \PDO
{
    use LogsTrait;
    public $helper;

    static function helperFor($env)
    {   // just keep one helper instance around for all pdos
        static $helpers = [];

        $key = $env['helper'];

        if (!isset($helpers[$key])) $helpers[$key] = static::newHelperFor($key);

        return $helpers[$key];
    }

    static function newHelperFor($helperKey)
    {
        $helperClass = "\\Primo\\PDOHelpers\\Helper_{$helperKey}";

        if (!class_exists($helperClass)) {
            throw new \PDOException("helper/adapter '{$helperKey}' invalid or not yet supported by Primo-PDO");
        }

        return new $helperClass();
    }

    function __construct($env, $options = [])
    {
        // 'database' (in $env or $options) overrides 'name'
        if (!isset($env['database'])) {
            if (isset($options['database'])) $env['database'] = $options['database'];
            else $env['database'] = $env['name'];
        }

        if (!isset($env['logging'])) $env['logging'] = true;
        $this->addLog($env['logging'], $env['database']);

        // fix dsn!
        $this->helper = static::helperFor($env);
        $dsn = $this->helper->dsn($env);

        $username = isset($env['user']) ? $env['user'] : trim(get_file_contents($env['user_file']));
        $password = isset($env['pass']) ? $env['pass'] : trim(get_file_contents($env['pass_file']));

        $options = array_replace($this->defaultOptions(), $options);

        //**/ echo "DSN: $dsn options:", json_encode($options) , "\n";
        parent::__construct($dsn, $username, $password, $options); //**/ echo $dsn;
    }

    function defaultOptions()
    {
        return [
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::MYSQL_ATTR_FOUND_ROWS => TRUE,
            \PDO::ATTR_STATEMENT_CLASS => [\Primo\PDOSubclassed\PDOStatement::class, [$this]]
        ];
    }

    function run($sql = null, ...$args)
    {
        if ($sql === null) return $this;

        if (empty($args)) {
            return $this->query($sql);
        }

        try {
            $stmt = $this->prepare($sql);
        } finally {
            if (!isset($stmt)) error_log("PREPARE FAILED: $sql");
        }
        // handle ("sql with ?", val)
        // handle ("sql with ?", [val])
        // handle ("sql with :name", [':name' => 'val'])
        // handle ("sql with :name", ['name' => 'val'])
        $args = is_array($args[0]) ? array_merge(...$args) : $args;

        $success = $stmt->execute($args);

        return $stmt;
    }

    function query($sql)
    {
        $start = microtime(true);
        try {
            $stmt = parent::query($sql);
        } finally {
            if ($this->logs) {
                $ms = microtime(true) - $start;
                $stmt = isset($stmt) ? $stmt : false;
                $this->logs->logThis($sql, null, $ms, ($stmt !== false));
            }
        }
        return $stmt;
    }

    function columnsOfTable($tableName)
    {
        return $this->helper->columnsOfTable($this, $tableName);
    }
}

// REFERENCES
//
// https://phpdelusions.net/pdo/pdo_wrapper
// https://github.com/paragonie/easydb
// https://www.reddit.com/r/PHP/comments/9i74mj/github_paragonieeasydbcache_easydb_with_inmemory/
// https://gist.github.com/rquadling/942253b0ccebd2a0a3c3d6030524fdb0