<?php

/**
 * Simple autoloader that follow the PHP Standards Recommendation #0 (PSR-0)
 * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md for more informations.
 */
function autoload_vex($className) {
    $className = ltrim($className, '\\');
    if (0 === strpos($className, 'Vex')) {
        $fileName = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
        if (is_file($fileName)) {
            require $fileName;

            return true;
        }
    }

    return false;
};

spl_autoload_register('autoload_vex');
