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
use \Primo\PDOWrapped\PDO;
use Phinx\Migration\Manager;

class Environment extends \ArrayObject
{

    function exists()
    {
        return PDO::helperFor($this)->databaseExists($this);
    }

    function clobber( $areYouSure )
    {
        if ($areYouSure) PDO::helperFor($this)->clobberDatabase($this);
        return $this;
    }

    function copyTo($to)
    {
        PDO::helperFor($this)->copyDatabase($this, $to);
        return $to;
    }

    function migrate()
    {
        $exists = $this->exists(); // test now, it will already be created by the next lines
        
        if (!$exists) PDO::helperFor($this)->ensureDir($this);
        
        $migrator = new ApplyPhinx($this);
        
        $migrator( !$exists );

        return $this;
    }

    function create( $fromScratch = true )
    {
        $this->clobber( $fromScratch )->migrate();

        return $this;
    }

    /**
     *  Adjusts the base environment for situations where
     *  multiple but similar db's are accessed:
     * 'snapshots' , 'backups' , or 'fixtures'
     * 
     * Alternative values are provided - per helper/adapter
     * 
     * @param type $optionKey
     * @return $this
     */
    function which($optionKey, $whichKey = 'which')
    {
        $clone = clone $this;

        if (isset($clone[$whichKey][$optionKey])) {
            foreach (($clone[$whichKey][$optionKey]) as $key => $value) {
                if (is_array($value) && isset($clone[$key])) {

                    foreach ($value as $key2 => $value2) {
                        $clone[$key][$key2] = $value2;
                    }
                } else $clone[$key] = $value;
            }
        }
        return $clone;
    }

    /**
     * Adjusts the base environment for situations where
     * multiple db's are created:
     * 'per client' , 'per user' , or per-scope
     * 
     * @param type $optionKey
     * @return $this
     */
    function atPut($varKey, $scopeKey)
    {
        $per = "%%{$varKey}%%";

        $clone = clone $this;
        foreach ($clone as $k => $v) {
            if (is_string($v)) {
                $clone[$k] = str_replace($per, $scopeKey, $v);
            }
        }
        return $clone;
    }

    function pdo( $options = [] )
    {
        return new PDO( $this, $options );
    }
}
