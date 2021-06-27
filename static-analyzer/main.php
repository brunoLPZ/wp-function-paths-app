<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/util/constants.php';
require_once __DIR__ . '/scanner/Scanner.php';

ini_set('xdebug.max_nesting_level', 3000);

// Scan entrypoint
$scanner = new Scanner($argv[1]);
$scanner->scan();
