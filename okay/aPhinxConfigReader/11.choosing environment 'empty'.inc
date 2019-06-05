<?php
 
namespace ok {
    global $READER;
    
    EXPECT("the result of choosing environment 'empty' from fixtures phinx.php");

    $env = $READER->choose('empty');

    _("is an object representing the Environment");
    
    assert($env instanceof \Primo\Phinx\Environment);

    _("is an object that casts to an array");

    assert(is_array((array) $env));
 
    _("includes the choice itself");

    assert($env['choice'] == 'empty');

    _("has logging disabled");

    assert($env['logging'] == false);

    _("may have its own 'paths' settings");

    assert(isset($env['paths']));

    $paths = $env['paths'];

    assert(count($paths) == 3);
}
  