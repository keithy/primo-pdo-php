<?php

// delete all matching files
array_map('unlink', glob(__DIR__ . '/*.sqlite3'));
 