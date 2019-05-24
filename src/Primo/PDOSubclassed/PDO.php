<?php

namespace Primo\PDOSubclassed;

class PDO extends \PDO {

    protected $log = false;
    public $helper;

    static function helperFor($adapter) {
        $helperClass = "\\Primo\\DBHelpers\\Helper_{$adapter}";
        if (!class_exists($helperClass)) {
            throw new \PDOException("adapter '{$adapter}' invalid or not yet supported by Primo-PDO");
        }
        return new $helperClass();
    }

    function defaultOptions() {
        return [
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_STATEMENT_CLASS => [\Primo\PDOSubclassed\PDOStatement::class, [$this]]
        ];
    }

    function __construct($config, $options = []) {

        // 'database' (in config or options) overrides 'name'
        $config['database'] = $config['database'] ?? $options['database'] ?? $config['name'];

        // fix dsn!
        $this->helper = static::helperFor($config['adapter']);
        $dsn = $this->helper->dsn($config);

        $username = $username ?? $config['user'] ?? trim(get_file_contents($config['user_file']));
        $password = $password ?? $config['pass'] ?? trim(get_file_contents($config['pass_file']));

        $options = array_replace($this->defaultOptions(), $options);

        parent::__construct($dsn, $username, $password, $options);
    }

    // Return reference to the logging variable
    function &getLog() {
        return $this->log;
    }

    // , optionally set it.
    // $this->log starts as false (no logging)
    // logOn() enables log falling through to set $this->log to [].
    // logOn($myLog) replaces $this->log with reference to an externally provided array.    
    // logOff() resets logging to false
    function logOn(& $log = []) {
        $this->log = & $log;
        return $this;
    }

    function logOff() {
        $this->log = false;
        return $this;
    }

    function run($sql = null, ...$args) {
        if ($sql === null) return $this;

        if (empty($args)) {
            return $this->query($sql);
        }

        $stmt = $this->prepare($sql);

        // handle ("sql with ?", val)
        // handle ("sql with ?", [val])
        // handle ("sql with :name", [':name' => 'val'])
        // handle ("sql with :name", ['name' => 'val'])
        $args = is_array($args[0]) ? $args[0] : $args;

        $success = $stmt->execute($args);

        return $stmt;
    }

    function query($sql) {

        $start = microtime(true);

        $result = parent::query($sql);

        if (false !== $this->log) array_push($this->log, [$sql, ($result !== false), microtime(true) - $start]);
  
        return $result;
    }

    function columnsOfTable($tableName) {
        return $this->helper->columnsOfTable($this, $tableName);
    }

}

// REFERENCES
//
// https://phpdelusions.net/pdo/pdo_wrapper
// https://github.com/paragonie/easydb
// https://www.reddit.com/r/PHP/comments/9i74mj/github_paragonieeasydbcache_easydb_with_inmemory/
// https://gist.github.com/rquadling/942253b0ccebd2a0a3c3d6030524fdb0