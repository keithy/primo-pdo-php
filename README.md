[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE.md)
[![Build Status](https://travis-ci.com/keithy/primo-pdo-php.svg?branch=master)](https://travis-ci.com/keithy/primo-pdo-php)
[![GitHub issues](https://img.shields.io/github/issues/keithy/primo-pdo-php.svg)](https://github.com/keithy/primo-pdo-php/issues)
[![Latest Version](https://img.shields.io/github/release/keithy/primo-pdo-php.svg)](https://github.com/keithy/primo-pdo-php/releases)
[![PHP from Travis config](https://img.shields.io/travis/php-v/keithy/primo-pdo-php.svg)](https://travis-ci.com/keithy/primo-pdo-php)

## primo-pdo-php

### Cool Stuff With added Phinx

https://github.com/keithy/primo-pdo-php/wiki/ConfigReader-returns-an-Environment

### Primary Goal:

*provide a PDO interface that can use Sqlite and Mysql interchangeably 
in order that specs/unit-tests/acceptance-tests can be made against fast file-based SQLite fixtures.*

PDO instanciation uses a Phinx compatible environment configuration, rather than the usual "dsn".
The Phinx configuration file supports multiple 'environments' and additional options, including: 
specification of named fixtures, migrations, logging configuration, and backup locations.

Phinx may be used to provide migrations and data seeding for fixtures and production upgrades.
The library includes support for creating databases from migrations, snapshotting/backups and creating fixtures from snapshots.

### Secondary Goal: 

*Include the best and most concise features of other similar libraries.*

```
#example:
$pdo->run("SELECT * FROM {$table} WHERE id = ? OR name = ?", $id, $name);
```
Collate all the best knowledge available (i.e. https://phpdelusions.net/pdo )

### Third Goal: 

Logging as standard, to multiple targets, with sensible defaults and zero-overhead when disabled,

## Features

### Unified interface to the DSN string

To improve consistency between different databases and phinx
we also accept an array of values as well as the standard dsn.

The array provides 'adapter' 'host' 'name', 'charset', 'user' & 'pass'
as used by the Phinx tool.

Usefully where secrets are in a file (i.e. docker secrets)
we also accept 'user_file' and 'pass_file'.

(have suggested this improvement to phinx)
 
### Logging is built in (zero overhead if not enabled) 

The default callback writes re-constructed SQL queries to error_log.

Can be enabled/disabled in the config file, universally (top level), or per environment.
```
logging = false
```
Can be set to a callable class in the config.
```
logging = "\Primo\PDOLog\Logs"
```

Register additional callbacks at runtime as you wish.
 ```
 usage:
  $pdo = new PDO( $aPhinxEnvironment )->addLog(false); // disables logging
  $pdo2 = new PDO( $aPhinxEnvironment )->addLog( fn($sql) => error_log($sql) );
 ```

### Super-duper unified interface to queries and prepared statements

*via the `run()` method - with added splat operator goodness!*

#### Prepared statements can't get any easier than this!

```
$pdo->run("SELECT * FROM {$table} WHERE id = ? OR name = ?", $id, $name);
```

```
#simple query
$pdo->run("SELECT name FROM pragma_table_info( '{$table}' )")->fetchAllAsColumn();

#prepared statement with named parameters
$pdo->run("SELECT * FROM {$table} WHERE id = :id OR name = :name", [ ':id' => $id, ':name' => $name ] );
$pdo->run("SELECT * FROM {$table} WHERE id = :id OR name = :name", [ 'id' => $id, 'name' => $name ] );
```

### Also 

1. If logging is enabled, queries are reconstructed from the bound variables
 
2. Choice of specialized `PDOStatement` as a subclass or wrapper.
   The subclass variant doesn't support persistent connections.

3. Bespoke option 'database' obtains a connection to an alternative database (e.g. a backup)
   using the same credentials given in the given phinx environment. This also enables an override for 
   a no-database connection i.e. 

 ```
  $pdo = new PDO( $aConfigEnvironment, ['database' => "" ]); // override, preferring no-database
 ```

4. Helper classes for ironing out differences between databases
 ```
 e.g. CONCAT (mysql) vs. || (sqlite)
 ```
Helpers provide a framework for copying databases from place to place.

* from a single environment to backup location based upon the same environment.

* from one environment to another on the same database

* from one environment to another on different databases (not yet coded)

You can subclass helpers, providing your own utilities and call them up by adding a 'helper' option to the config.