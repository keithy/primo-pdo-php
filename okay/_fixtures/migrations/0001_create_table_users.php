<?php

use Phinx\Migration\AbstractMigration;

class CreateTableUsers extends AbstractMigration {

    /**
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     */
    public function up() {
        $table = $this->table('users');
        $table->addColumn('username', 'string', ['limit' => 255, 'null' => false])
        ->addColumn('password', 'string', ['limit' => 255, 'null' => false])
        ->addColumn('password_salt', 'string', ['limit' => 255, 'null' => false])
        ->addColumn('email', 'string', ['limit' => 255, 'null' => false])
        ->addColumn('verified', 'boolean', ['default' => 0, 'null' => false])
        ->addColumn('active', 'boolean', ['default' => 0, 'null' => false])
        ->addColumn('created', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
        ->addColumn('modified', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
        ->addColumn('first_name', 'string', ['limit' => 255, 'null' => false])
        ->addColumn('last_name', 'string', ['limit' => 255, 'null' => false]);
        
        $table->addIndex('username', ['name' => 'BY_USERNAME', 'unique' => true])
                ->addIndex('email', ['name' => 'BY_EMAIL', 'unique' => true])
                ->addIndex('active', ['name' => 'BY_ACTIVE', 'unique' => false]);
        $table->create();
    }

}

 