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

We hold an "array" per pdo instance for logging stuff

 An array is used as a default because it is "the simplest thing that could possibly work". 
 This "array" defaults to false, to disable logging altogether. 
 This array may be accessed and shared by reference, in order share a single log among pdo instances.
 This array is shared with all PDOStatements returned by PDO.
 This "array" may be replaced by a PDOLog (or subclass) instance for all other logging options!
 ```
 usage:
  $log = [];
  $pdo = new PDO( $aPhinxEnvironment )->logOn( $log );
  $pdo2 = new PDO( $aPhinxEnvironment )->logOn( $log );
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