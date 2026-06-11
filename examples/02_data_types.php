<?php
/**
 * PHP Cheat Sheet - 02: Data Types
 * 
 * Topics covered:
 * - Scalar Types (String, Integer, Float, Boolean)
 * - Compound Types (Array, Object)
 * - Special Types (Resource, NULL)
 * - Type Casting (explicit conversion)
 * - Type Checking Functions (is_string, is_int, etc.)
 */

echo "=== 1. SCALAR TYPES ===\n";

// String (Single or double quoted)
$stringVar = "Hello PHP";
echo "String: $stringVar (Type: " . gettype($stringVar) . ")\n";

// Integer (Whole numbers)
$intVar = 42;
echo "Integer: $intVar (Type: " . gettype($intVar) . ")\n";

// Float (Floating point numbers / double)
$floatVar = 3.14159;
echo "Float: $floatVar (Type: " . gettype($floatVar) . ")\n";

// Boolean (true or false)
$boolVar = true;
echo "Boolean: " . ($boolVar ? "true" : "false") . " (Type: " . gettype($boolVar) . ")\n";


echo "\n=== 2. SPECIAL TYPES ===\n";

// NULL (Represents a variable with no value)
$nullVar = null;
echo "Null value: " . (is_null($nullVar) ? "is null" : "is not null") . " (Type: " . gettype($nullVar) . ")\n";


echo "\n=== 3. TYPE CHECKING ===\n";
// PHP offers helper functions to verify the type of variables.
$checkVal = 100.50;

echo "Is integer? " . (is_int($checkVal) ? "Yes" : "No") . "\n";
echo "Is float? " . (is_float($checkVal) ? "Yes" : "No") . "\n";
echo "Is numeric? " . (is_numeric($checkVal) ? "Yes" : "No") . "\n";
echo "Is string? " . (is_string($checkVal) ? "Yes" : "No") . "\n";
echo "Is boolean? " . (is_bool($checkVal) ? "Yes" : "No") . "\n";


echo "\n=== 4. TYPE CASTING (CONVERSION) ===\n";
// You can explicitly cast a variable to another type.
$original = "15.75";
echo "Original: \"$original\" (Type: " . gettype($original) . ")\n";

$castToInt = (int)$original;
echo "Casted to int: $castToInt (Type: " . gettype($castToInt) . ")\n";

$castToFloat = (float)$original;
echo "Casted to float: $castToFloat (Type: " . gettype($castToFloat) . ")\n";

$castToBool = (bool)$original;
echo "Casted to bool: " . ($castToBool ? "true" : "false") . " (Type: " . gettype($castToBool) . ")\n";


echo "\n=== 5. STRING FUNCTIONS DEMO ===\n";
// Common string operations
$phrase = " PHP Programming ";
echo "Original string: '$phrase'\n";
echo "Length (strlen): " . strlen($phrase) . "\n";
echo "Trimmed (trim): '" . trim($phrase) . "'\n";
echo "Uppercase (strtoupper): " . strtoupper($phrase) . "\n";
echo "Lowercase (strtolower): " . strtolower($phrase) . "\n";
echo "Replace 'PHP' with 'Modern PHP': " . str_replace("PHP", "Modern PHP", $phrase) . "\n";
echo "Substring (substr): '" . substr(trim($phrase), 4, 11) . "'\n";
?>
