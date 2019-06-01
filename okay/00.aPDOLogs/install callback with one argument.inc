<?php

namespace ok {

    global $LOGS;

    EXPECT("using callback");

    $log = "";

    $LOGS->add(function( $a ) use (&$log) {
        $log = $a;
    });

    _("works");
    $LOGS->logThis(1);

    assert($log == "1");
}

namespace ok {

    global $LOGS;

    EXPECT("adding additional callback");

    $log2 = "";

    $LOGS->add(function( $a ) use (&$log2) {
        $log2 = $a;
    });

    _("also works");
    $LOGS->logThis(2);

    assert($log == "2");
    assert($log2 == "2");
} 
