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
 
2. We may hold a shared array per pdo instance for logging stuff (optional)
 
3. Super-duper unified interface to queries and prepared statements via the run() method. 
   (with added splat operator goodness.)

 ```
 $pdo->run("SELECT name FROM pragma_table_info( '{$table}' )")->fetchAllAsColumn();
 
 #prepared statements
 $pdo->run("SELECT * FROM {$table} WHERE id = ? OR name = ?", $id, $name);
 $pdo->run("SELECT * FROM {$table} WHERE id = :id OR name = :name", [ ':id' => $id, ':name' => $name ] );
 $pdo->run("SELECT * FROM {$table} WHERE id = :id OR name = :name", [ 'id' => $id, 'name' => $name ] );
 ```
 
4. Queries are reconstructed from the bound variables for readable logging
 
5. Choice of specialized `PDOStatement` as a subclass or wrapper.
   The subclass variant doesn't support persistent connections.

6. Bespoke option 'database' obtains a connection to an alternative database (e.g. a backup)
   using the same credentials given in the phinx environment. This also enables an override for 
   a no-database connection i.e. 

 ```
  $pdo = new PDO( $aConfigEnvironment, ['database' => "" ]); // prefer no-database
 ```

7. DBHelpers for ironing out differences between databases
   
 ```
 e.g. CONCAT (mysql) vs. || (sqlite)
 ```