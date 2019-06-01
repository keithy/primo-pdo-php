<?php

namespace ok {
    global $READER;

    EXPECT("choosing environment 'missing' from fixtures phinx.php");

    _("will raise EnvironmentNotFound exception");

    try {
        $caught = false;
        $env = $READER->choose('missing');
    } catch (\Primo\Phinx\EnvironmentNotFound $ex) {
        $caught = true;
    }
    assert($caught);
}
 