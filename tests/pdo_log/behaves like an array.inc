<?php

global $log;

$theClass =  get_class($log);
assert( $theClass == 'Primo\PDOLog\Log' , "\$theClass==$theClass");

ok\expect("count() to return 0");

assert( 0 == count($log) );

# Cannot  use empty!
//ok\expect("log empty() to return true");
// assert( empty($log) );
 
ok\expect("\$log[0] to return null");

assert( $log[0] == null );

