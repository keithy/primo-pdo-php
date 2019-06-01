<?php

# If we lack any database snapshots here
# initialise the databases in this directory (snapshots) 
# 
# Otherwise test runs operate on copies in db_fixtures/*

return
        [
            'logging' => true,
            /* 'migrate' => '0002' */ // hold all at specific version
            'version_order' => 'creation',
            'paths' => [
                'migrations' => __DIR__ . '/migrations',
                'seeds' => __DIR__ . '/seeds',
                'snapshots' => __DIR__ . '/snapshots'
            ],
            'environments' => [
                'default_migration_table' => 'phinx',
                'default_database' => 'seeded',
                'empty' => [
                     'logging' => false,
                    'adapter' => 'sqlite',
                    'helper' => 'sqlite', /* an example, defaults to adapter */
                    'name' => "user_table_empty",
                    'user' => '',
                    'pass' => '',
                    'migrate' => [
                        'target' => '0001',
                        'seeders' => false
                    ],
                    'paths' => [
                        'migrations' => __DIR__ . '/migrations',
                        'snapshots' => __DIR__ . '/snapshots'
                    ],
                ],
                'one_user' => [
                    'adapter' => 'sqlite',
                    'name' => "user_table_and_one_user",
                    'user' => '',
                    'pass' => '',
                    'logging' => 'Primo\PDOLog\Logs',
                    'migrate' => [
                        'seeders' => false
                    ]
                ],
                'seeded' => [
                    'adapter' => 'sqlite',
                    'name' => "user_table_seeded",
                    'user' => '',
                    'pass' => '',
                    'migrate' => [
                        'seeders' => 'UserSeeder'
                    ]
                ]
            ]
];
