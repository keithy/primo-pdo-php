<?php

namespace ok {
    EXPECT("it to return a list of available environments");

    global $CONFIG, $READER;

    assert(empty(array_diff($READER->choices(), ['empty', 'one_user', 'seeded'])), print_r($READER->choices(), true));
}
