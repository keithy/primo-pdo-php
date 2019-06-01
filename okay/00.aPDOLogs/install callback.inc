<?php

namespace ok {

    global $LOGS;

    $log = "";

    EXPECT("using callback");

    $LOGS->add(function( $a, $b, $c ) use (&$log) {
        $log = $a . $b . $c;
    });

    _("works");
    $LOGS->logThis(1, 2, 3);

    assert($log == "123");
} 