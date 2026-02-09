<?php
/**
 * Simple Autoloader
 * Loads classes based on PSR-4 style naming convention
 */

spl_autoload_register(function ($class) {
    // Base directory for the namespace prefix
    $baseDir = __DIR__ . '/';
    
    // Project namespace prefix
    $prefix = '';
    
    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // No, move to the next registered autoloader
        return;
    }
    
    // Get the relative class name
    $relativeClass = substr($class, $len);
    
    // Replace namespace separator with directory separator
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

// Also try direct file loading for our structure
spl_autoload_register(function ($class) {
    // Map class to file for our project structure
    $classMap = [
        'Database' => __DIR__ . '/config/Database.php',
        'Usuario' => __DIR__ . '/models/Usuario.php',
        'Movimiento' => __DIR__ . '/models/Movimiento.php',
        'Inversion' => __DIR__ . '/models/Inversion.php',
        'Message' => __DIR__ . '/models/Message.php',
        'Transfer' => __DIR__ . '/models/Transfer.php',
        'Security' => __DIR__ . '/utils/Security.php',
        'RateLimiter' => __DIR__ . '/utils/Security.php',
    ];
    
    if (isset($classMap[$class]) && file_exists($classMap[$class])) {
        require_once $classMap[$class];
    }
});
