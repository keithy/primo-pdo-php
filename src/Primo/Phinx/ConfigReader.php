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

class ConfigReader
{
    public $data;

    static function readFrom($path, $choice = null)
    {
        $reader = new static($path);

        $choice = $choice ?? $reader->default();

        return $reader->choose($choice);
    }

    function __construct($path)
    {
        $this->data = require($path);
    }

    function default()
    {
        return $this->data['environments']['default_database'];
    }

    function choose($choice)
    {
        $environment = $this->data['environments'][$choice] ?? null;
        if (null === $environment) throw new DbEnvironmentNotFound();
        
        return $this->applyDefaults($environment, $choice);
    }

    //defaults may be defined in a file, and the path referenced.
    
    function applyDefaults($config, $choice)
    {
        $path = $this->data['paths']['defaults'] ?? "";
        $defaults = file_exists($path) ? require($path) : [];

        $config = array_replace($defaults, $config);

        $config['choice'] = $choice;
        $config['paths'] = $this->data['paths'];
        $config['RESET'] = true; // request pdo handle refresh

        return $config;
    }
  
}