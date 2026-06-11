<?php
/**
 * PHP Cheat Sheet - 13e: Standard Library - Miscellaneous Functions
 * 
 * This file lists and runs essential built-in variables, types, and runtime helper functions.
 */

function printTitle(string $title): void {
    echo "\n--- $title ---\n";
}

printTitle("1. VARIABLE STATES");

$var1 = "Hello";
$var2 = null;

// isset() - true if variable is set and not null
echo "isset(\$var1) Output: " . (isset($var1) ? "Yes" : "No") . "\n";
echo "isset(\$var2) Output: " . (isset($var2) ? "Yes" : "No") . "\n";

// empty() - true if variable is empty (false, 0, "", null, empty array)
$emptyVal = 0;
echo "empty(\$emptyVal) (value is 0) Output: " . (empty($emptyVal) ? "Yes" : "No") . "\n";

// gettype() - returns type name string
echo "gettype(12.5) Output: " . gettype(12.5) . "\n";

// unset() - destroys a variable
$destroy = "boom";
unset($destroy);
echo "isset(\$destroy) after unset() Output: " . (isset($destroy) ? "Yes" : "No") . "\n";


printTitle("2. TYPE VERIFICATIONS");

$testVal = "42";

echo "is_null(\$testVal) Output: " . (is_null($testVal) ? "Yes" : "No") . "\n";
echo "is_scalar(\$testVal) Output: " . (is_scalar($testVal) ? "Yes" : "No") . "\n"; // True for int, float, string, bool
echo "is_numeric(\$testVal) Output: " . (is_numeric($testVal) ? "Yes" : "No") . "\n"; // True for numeric strings like "42"
echo "is_string(\$testVal) Output: " . (is_string($testVal) ? "Yes" : "No") . "\n";
echo "is_int(\$testVal) Output: " . (is_int($testVal) ? "Yes" : "No") . "\n";
echo "is_callable('is_int') Output: " . (is_callable('is_int') ? "Yes" : "No") . "\n";


printTitle("3. EXECUTION & PROCESS HELPERS");

// uniqid() - generates a unique ID based on microtime
echo "uniqid('prefix_') Output: " . uniqid('prefix_') . "\n";
echo "uniqid('prefix_', true) (high entropy) Output: " . uniqid('prefix_', true) . "\n";

// sleep() / usleep()
$t0 = microtime(true);
usleep(100000); // Sleep for 100,000 microseconds (0.1 seconds)
$t1 = microtime(true);
echo "usleep(100000) execution duration: " . round($t1 - $t0, 4) . " seconds\n";


printTitle("4. DYNAMIC EVALUATION CHECKS");

// function_exists() - checks if function is defined
echo "function_exists('printTitle') Output: " . (function_exists('printTitle') ? "Yes" : "No") . "\n";

// class_exists() - checks if class is defined
echo "class_exists('DateTime') Output: " . (class_exists('DateTime') ? "Yes" : "No") . "\n";

// extension_loaded() - checks if PHP extension is enabled
echo "extension_loaded('pdo') Output: " . (extension_loaded('pdo') ? "Yes" : "No") . "\n";

// defined() - checks if constant exists
const DUMMY_CONST = "hello";
echo "defined('DUMMY_CONST') Output: " . (defined('DUMMY_CONST') ? "Yes" : "No") . "\n";


printTitle("5. RUNTIME INI CONFIGURATION");

// ini_get() - gets the value of a configuration option
echo "ini_get('display_errors') Output: " . ini_get('display_errors') . "\n";
echo "ini_get('memory_limit') Output: " . ini_get('memory_limit') . "\n";

// ini_set() - sets the value of a configuration option (for the duration of the script execution)
ini_set('display_errors', '0');
echo "ini_get('display_errors') after ini_set() Output: " . ini_get('display_errors') . "\n";
?>
