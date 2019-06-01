<?php

namespace Primo\Phinx;

# usage:
# - to read default environment
# $CONFIG = \Primo\Phinx\ConfigReader::readFrom( __SITE__.'/config/db_credentials.php');
# - to read 'testing' environment
# $CONFIG = \Primo\Phinx\ConfigReader::readFrom( __SITE__.'/config/db_credentials.php', 'testing');
# 
# Defaults may be defined in a file, and the path referenced.
# Also Usage: new PDO(\Primo\Phinx\ConfigReader::readFrom('path.php', 'environment'));
#
# Other non-stanard fields:
# 'logging'

use \Primo\PDOLog\LogsTrait;
use \Primo\PDOSubclassed\PDO;

class Environment extends \ArrayObject
{
    use \Primo\PDOLog\LogsTrait;

    static function phinxInDo($configPath, $dir, $aClosure)
    {
        $phinx = new \Phinx\Wrapper\TextWrapper(new \Phinx\Console\PhinxApplication());
        $phinx->setOption('configuration', $configPath);

        $cwd = getcwd();
        try {
            chdir($dir);
            $aClosure($phinx);
        } finally {
            chdir($cwd);
        }
    }

    // if it is present
    function withPhinxDo($aClosure)
    {
        is_dir($this['dir']) ?: mkdir($this['dir']); // ensure existence of working directory

        return static::phinxInDo($this['config'], $this['dir'], $aClosure);
    }
    /*
     * $choice - the database environment to migrate
     *
     * $pathsKey - change directory to the path given in the paths dictionary
     *           - if the db is file based 
     * 
     * $seed - whether to apply seeds (i.e. on initial creation)
     */

    function applyMigrations($seed = false)
    {
        if (!isset($this->logs)) $this->addLog($this['logging']);

        $this->withPhinxDo(function( $phinx ) use ( $seed ) {

            $env = $this;
            $choice = $env['choice'];
            $target = null;
            $seeders = null;

            if (isset($env['migrate'])) {
                $migrate = $env['migrate'];
                if (isset($migrate['target'])) $target = $migrate['target'];
                if (isset($migrate['seeders'])) $seeders = $migrate['seeders'];
            }

            $this->logThis( $phinx->getMigrate($choice, $target), "Migrating");

            if ($seed && false !== $seeders) {
                $this->logThis($phinx->getSeed($choice, $target, $seeders), "Seeding");
            }
        });
    }

    function clobberDatabase()
    {
        PDO::helperFor($this)->clobberDatabase($this);
        return $this;
    }

    function copyDatabaseTo($to)
    {
        PDO::helperFor($this)->copyDatabase($this, $to);
        return $to;
    }

    function ensureCreated()
    {
        $this->applyMigrations(true);
        return $this;
    }

    function logThis($report, $tag)
    {
        if (isset($this->logs)) {
            foreach (explode("\n", $report) as $line) {
                $this->logs->logThis($line, $tag);
            }
        }
    }
}
