<?php

# If we lack any database snapshots here
# initialise the databases in this directory (snapshots) 
# 
# Otherwise test runs operate on copies in db_fixtures/*
 
return
        [
            'version_order' => 'creation',
            'paths' => [
                'migrations' => __DIR__.'/migrations',
                'seeds' => __DIR__.'/seeds',
            ],
            'environments' => [
                'default_migration_table' => 'phinx',
                'default_database' => 'seeded',
                'empty' => [
                    'adapter' => 'sqlite',
                    'name' => "user_table_empty",
                    'user' => '',
                    'pass' => '',
                 
                ],
                'one_user' => [
                    'adapter' => 'sqlite',
                    'name' => "user_table_and_one_user",
                    'user' => '',
                    'pass' => '',
                
                ],
                'seeded' => [
                    'adapter' => 'sqlite',
                    'name' => "user_table_seeded",
                    'user' => '',
                    'pass' => '',
                 
                ]
            ]
];
