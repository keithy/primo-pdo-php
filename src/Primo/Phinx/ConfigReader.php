<?php

/**
 * ConfigReader reads a Phinx configuration file in the php format.
 * 
 * $reader = new ConfigReader( __DIR__.'db_credentials.php' );
 * 
 * There are a number of enhancements to the basic phinx configuration.
 * 
 * See: https://github.com/keithy/primo-pdo-php/wiki/ConfigReader
 */
namespace Primo\Phinx;

use \Primo\PDOLog\LogsTrait;
use \Primo\PDOSubclassed\PDO;

class ConfigReader
{
    protected $path;
    protected $choice;
    public $data;

    function __construct($path)
    {
        $this->path = $path;
        $this->data = require($path);
    }

    function defaultEnvironment()
    {
        return $this->data['environments']['default_database'];
    }

    function choose($choice = null)
    {
        $choice = isset($choice) ? $choice : $this->defaultEnvironment();

        $env = isset($this->data['environments'][$choice]) ? $this->data['environments'][$choice] : null;

        if (null === $env) throw new EnvironmentNotFound();

        $env = $this->applyDefaultsFile($env, $choice);
        $env = $this->applyContext($env, $choice);

        return new Environment($env);
    }

    //defaults may be defined in a file, and the path referenced.

    function applyDefaultsFile($env)
    {
        if (!isset($this->data['paths']['defaults'])) return $env;

        $path = $this->data['paths']['defaults'];

        return file_exists($path) ? array_replace(include($path), $env) : $env;
    }

    function applyContext($env, $choice)
    {
        //defaults
        $env['choice'] = $choice;
        $env['config'] = $this->path;

        /* 'dir' setting is used to specify the directory to use as the working directory
         * for running phinx. The determines the location of the sqlite databases 
         * (if 'name' is not an absolute path.
         */

 
        if (!isset($this->data['logging'])) $this->data['logging'] = true;

        /*
         * 'helper' is used to select a PDOHelper class, the default being the same as
         * 'adapter'. Helpers implement routines, such as database copying, backups, 
         * managment policies. This allows for subclassing, customizing and selecting
         * helpers
         */
        if (!isset($env['helper'])) $env['helper'] = $env['adapter'];

        // copy up from universal settings - so nothing is lost

        /* The universal paths setting is reproduced in the chosen environment */
        if (isset($this->data['paths'])) {
            if (!isset($env['paths'])) $env['paths'] = $this->data['paths'];
            else $env['paths'] = array_replace($this->data['paths'], $env['paths']);
        }
        /* The universal migrate setting is copied as a defaut to the chosen environment
         * This allows the configuration file to specify the current migration status
         * for all environments, holding the codebase at a position, until the migration 
         * has been tested. It is used when primo-pdo is used to perform the migration.
         */

        if (!isset($env['migrate']) && isset($this->data['migrate']))
                $env['migrate'] = $this->data['migrate'];

        /* The universal 'logging' setting is copied as a default to the chosen environment */
        if (!isset($env['logging'])) $env['logging'] = $this->data['logging'];

        /* The universal 'default_migration_table' setting is copied as a default to the chosen environment */
        if (!isset($env['default_migration_table']) && isset($this->data['environments']['default_migration_table']))
                $env['default_migration_table'] = $this->data['environments']['default_migration_table'];

        /* The universal 'version_order' setting is copied as a default to the chosen environment */
        if (!isset($env['version_order']) && isset($this->data['version_order']))
                $env['version_order'] = $this->data['version_order'];

        // general purpose mechanism - per helper/adapter

        /* The universal 'helper' settings are overlayed onto the chosen environment */
        if (isset($this->data[$env['helper']])) {
            $env = array_replace_recursive($env, $this->data[$env['helper']]);
        }

        if (!isset($env['dir'])) $env['dir'] = dirname($this->path);

        return $env;
    }

    function choices($filterFn = null)
    {
        return array_keys(array_filter($this->data['environments'], function($value) use( $filterFn ) {
                    return is_array($value) && (!isset($filterFn) || $filterFn($value));
                }));
    }
}
