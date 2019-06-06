<?php

namespace Primo\Phinx;

use Phinx\Migration\Manager;
use Phinx\Config\Config;
use Symfony\Component\Console\Input\StringInput;

class ApplyPhinx
{
    use \Primo\PDOLog\LogsTrait;
    protected $output;
    protected $config;
    protected $env;

    function __construct($env)
    {
        // recreating a phinx config from the supplied environment
        $this->env = $env;
        $config = [];
        $config['paths'] = $env['paths'];
        $config['version_order'] = $env['version_order'];
        $config['environments'][$env['choice']] = (array) $env;
        $config['environments']['default_migration_table'] = $env['default_migration_table'];
        $config['environments'][$env['choice']]['connection'] = $env->pdo([ \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_BOTH ]);
        $this->config = $config;
    }

    function phinxConfigDo($aClosure)
    {
        $this->output = new LogsOutput($this->logs);
        $config = new Config($this->config);
        $dir = $this->env['dir'];

        $phinx = new Manager($config, new StringInput(' '), $this->output);

        $cwd = getcwd();
        try {
            is_dir($dir) ?: mkdir($dir, 01770, true); // ensure existence
            chdir($dir);
            $aClosure($phinx);
        } finally {
            chdir($cwd);
        }
    }

    function __invoke($applySeeders = false)
    {
        $this->phinxConfigDo(function( $phinx ) use ( $applySeeders ) {

            $env = $this->env;
            $choice = $env['choice'];
            $target = null;
            $seeders = null;

            if (isset($env['migrate'])) {
                $migrate = $env['migrate'];
                if (isset($migrate['target'])) $target = $migrate['target'];
                if (isset($migrate['seeders'])) $seeders = $migrate['seeders'];
            }

            $this->output->setTag("Migrating");
            $phinx->migrate($choice, $target);

            if ($applySeeders) {
                if (false !== $seeders) { // not none
                    $this->output->setTag("Seeding");

                    if (true === $seeders) $phinx->seed($choice); // all
                    else {
                        foreach ((array) $seeders as $seeder) { // those listed
                            $phinx->seed($choice, $seeder);
                        }
                    }
                }
            }
        });
    }
}
