<?php

namespace ok {
    
    EXPECT("that the config");

    global $CONFIG, $READER;

    _("reads in ok");

    _("has a 'logging' setting");

    assert($CONFIG['logging'] == true);

    _("has some 'paths' settings");

    assert(count($CONFIG['paths']) == 3);
}

namespace ok {
    
    EXPECT("all config paths");

    _("to be real and absolute");

    foreach ($CONFIG['paths'] as $path) {
        assert(file_exists($path));
        assert(realpath($path) == $path);
    }
}
