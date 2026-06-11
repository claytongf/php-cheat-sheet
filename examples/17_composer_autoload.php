<?php
/**
 * PHP Cheat Sheet - 17: Composer and PSR-4 Autoloading
 * 
 * Topics covered:
 * - What is Composer?
 * - Structure of composer.json
 * - PSR-4 Autoloading specification
 * - Programmatic Autoloading under the hood (spl_autoload_register)
 */

echo "=== 1. COMPOSER CONFIGURATION (composer.json sample) ===\n";
echo "Below is an example schema of a modern composer.json file:\n\n";

$composerJsonExample = '{
    "name": "myproject/php-app",
    "description": "A modern PHP application",
    "require": {
        "php": ">=8.1",
        "guzzlehttp/guzzle": "^7.5"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0"
    },
    "autoload": {
        "psr-4": {
            "App\\\\": "src/"
        }
    }
}';
echo $composerJsonExample . "\n\n";


echo "=== 2. UNDERSTANDING PSR-4 AUTOLOADING ===\n";
echo "PSR-4 maps namespace prefixes to base directories. For example:\n";
echo "Namespace: App\\Database\\Connection  ==> Path: src/Database/Connection.php\n\n";


echo "=== 3. UNDER THE HOOD: spl_autoload_register() ===\n";
echo "PHP resolves class names dynamically using autoloader callback registrations.\n";
echo "Let's simulate a custom autoloader resolving namespaces step-by-step:\n\n";

// Register the custom namespace autoloader
spl_autoload_register(function (string $className) {
    // Prefix we are looking for
    $prefix = 'FakeApp\\';
    
    // Base directory mapping
    $baseDir = __DIR__ . '/src/';
    
    // Check if the class name uses our namespace prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $className, $len) !== 0) {
        return; // Move to next registered autoloader
    }
    
    // Get relative class name
    $relativeClass = substr($className, $len);
    
    // Replace namespace separator with directory separator and append .php
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    echo ">> [Autoloader LOG] Attempting to load: $className\n";
    echo "   Mapped Path: " . str_replace(__DIR__ . '/', '', $file) . "\n";
    
    // Normally we check: if (file_exists($file)) { include $file; }
    // For this demonstration, we just show the resolved path!
});

// Triggering the autoloader by referencing a non-existent class inside the namespace
echo "Referencing 'FakeApp\\Database\\SQLiteDriver'...\n";
if (!class_exists('FakeApp\\Database\\SQLiteDriver')) {
    echo "Class search finished.\n";
}

echo "\nReferencing 'FakeApp\\Services\\AuthService'...\n";
if (!class_exists('FakeApp\\Services\\AuthService')) {
    echo "Class search finished.\n";
}
?>
