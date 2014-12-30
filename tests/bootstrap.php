<?php

// HEADER HERE
// ---------------------------------------------------------------

/**
 * PHP TOC Unit Tests Bootstrap File
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */

//Files to ensure exist
$checkFiles['autoload'] = __DIR__.'/../vendor/autoload.php';
$checkFiles[] = __DIR__.'/../vendor/sunra/php-simple-html-dom-parser/README.md';

//Check 'Em
foreach($checkFiles as $file) {

    if ( ! file_exists($file)) {
        throw new RuntimeException('Install development dependencies to run test suite.');
    }
}

//Away we go
$autoload = require_once $checkFiles['autoload'];

/* EOF: bootstrap.php */