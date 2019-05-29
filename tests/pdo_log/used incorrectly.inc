<?php

global $log;

ok\expect("array_push of array in wrong format");

ok\to("raise an AssertionError");

ok\asserts(false);
try {
    $caught = false;
    
    $log[] = "hello";    
    
} catch (AssertionError $ex) {
    $caught = true;
}
ok\asserts(true);

assert( $caught );
