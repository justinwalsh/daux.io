<?php
/** Autoloading */
chdir(dirname(__DIR__));
$basePath = getcwd();

if (file_exists("$basePath/vendor/autoload.php")) {
    require_once "$basePath/vendor/autoload.php";
}else{
    echo 'Error: I cannot find the autoloader. Did you run \'composer.phar install\' ?' . PHP_EOL;
    exit(2);
}