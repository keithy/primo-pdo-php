<?php

namespace ok {
    global $READER;

    EXPECT("reading environment 'seeded' from fixtures phinx.php");
 
    $env = $READER->choose('seeded');

    _("has inherited a logging setting");

    assert($env['logging'] == true);
}
