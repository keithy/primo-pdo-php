<?php

$PDO_CLASS = "Primo\PDOWrapped\PDO";

$log = "";

OK\EXPECT("simple query against 'one_user' fixture");

$pdo = pdoOnFixture('one_user')->logAdd( function($sql)use(&$log) { $log = $sql; } );

$result = $pdo->run('SELECT count(*) FROM users')->fetchColumn();

OK\TO("return 1 result");

assert($result == 1);

OK\TO("log sql");

assert($log == 'SELECT count(*) FROM users');

OK\EXPECT("Simple query against 'seeded' fixture");

$pdo = pdoOnFixture('seeded')->logAdd( function($sql)use(&$log) { $log = $sql; } );

$result = $pdo->run('SELECT * FROM users')->fetchAll();

OK\TO("return 11 results");

assert(count($result) == 11);
 
OK\EXPECT("prepared statements with ?s");

$stmt = $pdo->run('SELECT count(*) FROM users WHERE username = ? AND email = ?', 'admin', 'admin@admin.com');

OK\TO("return valid result");

assert( 1 == $stmt->fetchColumn());

OK\_("and log sql");

$prepared = "SELECT count(*) FROM users WHERE username = `admin` AND email = `admin@admin.com`";

assert( $log == $prepared);

OK\EXPECT("prepared statements with :named args");

$stmt = $pdo->run('SELECT count(*) FROM users WHERE username = :user AND email = :email', [':user' => 'admin', ':email' => 'admin@admin.com']);

OK\TO("return valid result");

assert( 1 == $stmt->fetchColumn());

OK\_("and log sql");

assert( $log == $prepared);
