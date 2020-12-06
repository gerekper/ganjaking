<?php

spl_autoload_register(function ($class) {
    // Do not load unless in plugin domain.
    $namespace = 'PHPSQLParser';
    if (strpos($class, $namespace) !== 0) {
        return;
    }

    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    // Converts Class_Name (class convention) to class-name (file convention).

    $file = dirname(__FILE__) .'/'. $class . '.php';
    if (file_exists($file)) {
        require $file;
    }
});