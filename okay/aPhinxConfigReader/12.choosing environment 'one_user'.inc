<?php

namespace ok {
    global $READER;

    EXPECT("reading environment 'one_user' from fixtures phinx.php");

    $env = $READER->choose('one_user');

   _("is an object representing the Environment");

    assert($env instanceof \Primo\Phinx\Environment);

    _("is an object that casts to an array");

    assert(is_array((array) $env));
 
    _("includes the choice itself");

    assert($env['choice'] == 'one_user');

    _("has a different logging setting to the other environments");

    assert($env['logging'] == 'Primo\PDOLog\Logs');

    _("has a copy of the top level 'paths' setting");

    assert(isset($env['paths']));
    assert(isset($env['paths']['migrations']));
    assert(isset($env['paths']['seeds']));
}
