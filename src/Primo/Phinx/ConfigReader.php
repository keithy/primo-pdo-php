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

class ConfigReader
{
    protected $path;
    protected $choice;
    public $data;

    static function readFrom($path, $choice = null, $pathsKey = null)
    {
        $reader = new static($path);

        return $reader->choose($choice, $pathsKey);
    }

    function __construct($path)
    {
        $this->path = $path;
        $this->data = require($path);
    }

    function defaultEnvironment()
    {
        return $this->data['environments']['default_database'];
    }

    function choose($choice = null, $pathsKey = null)
    {
        $choice = isset($choice) ? $choice : $this->defaultEnvironment();

        $env = isset($this->data['environments'][$choice]) ? $this->data['environments'][$choice] : null;

        if (null === $env) throw new EnvironmentNotFound();

        $env = $this->applyDefaults($env, $choice);
        $env = $this->applyContext($env, $choice, $pathsKey);

        return new Environment($env);
    }

    //defaults may be defined in a file, and the path referenced.

    function applyDefaults($env)
    {
        if (!isset($this->data['paths']['defaults'])) return $env;

        $path = $this->data['paths']['defaults'];

        return file_exists($path) ? array_replace(include($path), $env) : $env;
    }

    function applyContext($env, $choice, $pathsKey)
    {
        $env['choice'] = $choice;
        $env['config'] = $this->path;
        $env['dir'] = isset($pathsKey) ? $this->data['paths'][$pathsKey] : dirname($this->path);

        if (!isset($env['helper'])) $env['helper'] = $env['adapter'];
        if (!isset($env['paths']) && isset($this->data['paths']))
                $env['paths'] = $this->data['paths'];
        if (!isset($env['migrate']) && isset($this->data['migrate']))
                $env['migrate'] = $this->data['migrate'];

        if (!isset($this->data['logging'])) $this->data['logging'] = true;
        if (!isset($env['logging'])) $env['logging'] = $this->data['logging'];

        return $env;
    }

    function choices($filterFn = null)
    {
        return array_keys(array_filter($this->data['environments'], function($value) use( $filterFn ) {
                    return is_array($value) && (!isset($filterFn) || $filterFn($value));
                }));
    }
}
