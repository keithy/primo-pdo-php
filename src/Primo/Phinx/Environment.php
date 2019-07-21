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
    function databaseIdentifier()
    {
         return PDO::helperFor($this)->databaseIdentifier($this);
    }
    
    function exists()
    {
        return PDO::helperFor($this)->hasBeenInitialized($this);
    }

    function clobber($areYouSure)
    {
        if ($areYouSure) PDO::helperFor($this)->clobberDatabase($this);
        return $this;
    }

    function copyTo($to)
    {
        PDO::helperFor($this)->copyDatabase($this, $to);
        return $to;
    }

    function migrate( $exists = null) // if we already know $exists pass it in
    {
        if (!isset($exists)) $exists = $this->exists(); // test now, it will already be created by the next lines

        if (!$exists) PDO::helperFor($this)->ensureDir($this);

        $migrator = new ApplyPhinx($this);

        $migrator(!$exists);

        return $this;
    }

    function create($fromScratch = true)
    {
        // if clobber is requested, we know $exists will be false.
        $exists = $fromScratch ? false : null;
        $this->clobber($fromScratch)->migrate( $exists );

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
        if (!isset($this[$whichKey][$optionKey])) return $this;

        $clone = clone $this;
        foreach (($this[$whichKey][$optionKey]) as $key => $value) {
            if (is_array($value) && isset($clone[$key])) {
                foreach ($value as $key2 => $value2) {
                    $clone[$key][$key2] = $value2;
                }
            } else $clone[$key] = $value;
        }
        $clone[$whichKey] = $optionKey;
        return $clone;
    }

    function atPutAll($dict)
    {
        return $this->atPut(array_keys($dict), array_values($dict));
    }

    /**
     * Adjusts the base environment for situations where
     * multiple db's are created:
     * 'per client' , 'per user' , or per-scope
     * 
     * @param type $optionKey
     * @return $this
     */
    function atPut($varKeys, $varValues)
    {
        $varKeys = array_map(function($key) {
            return "%%{$key}%%";
        }, (array) $varKeys);
        $varValues = (array) $varValues;

        $clone = clone $this;
        foreach ($clone as $k => $v) {
            if (is_string($v)) {
                $clone[$k] = str_replace($varKeys, $varValues, $v);
            }
        }
        return $clone;
    }

    function pdo($options = [])
    {
        return new PDO($this, $options);
    }
 
}
