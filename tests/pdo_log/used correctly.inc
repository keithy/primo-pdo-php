<?php

global $log;

ok\expect("array_push of array in correct format");

array_push( $log, ['SELECT 1', true, 0.05 ] );

ok\to("output a log of that item");

ok\expect("count() to return 1");
assert( 1 == count($log) );

ok\expect("array_pop to retrieve the last pushed item");

ok\expect("count() to STILL return 1");
assert( 1 == count($log) );
