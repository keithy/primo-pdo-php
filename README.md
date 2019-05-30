[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Build Status](https://travis-ci.org/keithy/primo-pdo-php.svg?branch=master)](https://travis-ci.org/keithy/primo-pdo-php)
[![GitHub issues](https://img.shields.io/github/issues/keithy/primo-pdo-php.svg)](https://github.com/keithy/primo-pdo-php/issues)
[![Latest Version](https://img.shields.io/github/release/keithy/primo-pdo-php.svg)](https://github.com/keithy/primo-pdo-php/releases)
[![PHP from Travis config](https://img.shields.io/travis/php-v/keithy/primo-pdo-php.svg)](https://travis-ci.org/keithy/primo-pdo-php)

## primo-pdo-php

Primary Goal: to provide a PDO interface that can use Sqlite and Mysql interchangeably
in order that unit/acceptance testing can be made against fast file-based SQLite fixtures.

To achieve this Phinx is used to provide migrations and data seeding for fixtures and upgrades.
PDO instanciation uses a Phinx compatible environment configuration rather than the usual "dsn".

Secondary Goal: Include the best and most concise features of other similar libraries.

Third Goal: Logging as standard

### Features

1. Unified interface to the DSN string

 To improve consistency between different databases and phinx
 we accept a config array instead.

 The array provides 'adapter' 'host' 'name', 'charset', 'user' & 'pass'
 similar to phinx environments. 
 
 Usefully where secrets are in a file (i.e. docker secrets)
 we also accept 'user_file' and 'pass_file'.
 
 (have suggested this improvement to phinx)
 
2. Logging is built in (Zero overhead if not enabled) 

 The default callback writes the re-constructed SQL to error_log.

Register additional callbacks as you wish.
 ```
 usage:
  $pdo = new PDO( $aPhinxEnvironment )->logOff();
  $pdo2 = new PDO( $aPhinxEnvironment )->logOn( fn($sql) => error_log($sql) );
 ```

3. Super-duper unified interface to queries and prepared statements via the run() method. 
   (With added splat operator goodness.)

 ```
 #simple query
 $pdo->run("SELECT name FROM pragma_table_info( '{$table}' )")->fetchAllAsColumn();
 
 #prepared statements
 $pdo->run("SELECT * FROM {$table} WHERE id = ? OR name = ?", $id, $name);
 $pdo->run("SELECT * FROM {$table} WHERE id = :id OR name = :name", [ ':id' => $id, ':name' => $name ] );
 $pdo->run("SELECT * FROM {$table} WHERE id = :id OR name = :name", [ 'id' => $id, 'name' => $name ] );
 ```
 
4. If logging is enabled, queries are reconstructed from the bound variables
 
5. Choice of specialized `PDOStatement` as a subclass or wrapper.
   The subclass variant doesn't support persistent connections.

6. Bespoke option 'database' obtains a connection to an alternative database (e.g. a backup)
   using the same credentials given in the given phinx environment. This also enables an override for 
   a no-database connection i.e. 

 ```
  $pdo = new PDO( $aConfigEnvironment, ['database' => "" ]); // override, preferring no-database
 ```

7. DBHelpers for ironing out differences between databases
   
 ```
 e.g. CONCAT (mysql) vs. || (sqlite)
 ```