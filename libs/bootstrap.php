<?php

// Loaded as a dependency
if (file_exists(__DIR__ . '/../../../autoload.php')) {
    return require_once __DIR__ . '/../../../autoload.php';
}

// Loaded in the project itself
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    return require_once __DIR__ . '/../vendor/autoload.php';
}

// Loaded in the project itself, when vendor isn't installed
if (file_exists(__DIR__ . '/../daux.phar')) {
    define('PHAR_DIR', __DIR__ . '/..');

    return require_once 'phar://' . __DIR__ . '/../daux.phar/vendor/autoload.php';
}

throw new Exception('Impossible to load Daux, missing vendor/ or daux.phar');
