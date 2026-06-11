<?php
/**
 * PHP Cheat Sheet - 01: Basics
 * 
 * Topics covered:
 * - Basic syntax and tagging
 * - Comments (single-line and multi-line)
 * - Outputs (echo, print, var_dump, print_r)
 * - Variables (declaration, rules)
 * - Constants (define vs const)
 */

// --- 1. PHP Tags ---
// PHP code starts with the '<?php' tag. The closing tag is optional (and omitted in pure PHP files).

echo "=== 1. OUTPUT METHODS ===\n";

// --- 2. Output Methods ---
// 'echo' can take multiple arguments, does not return a value, and is slightly faster than print.
echo "Hello from echo!\n";
echo "Echo ", "can ", "take ", "multiple ", "parameters.\n";

// 'print' takes only one argument and always returns 1.
print "Hello from print!\n";

// 'var_dump()' displays structured information about expressions, including type and value.
echo "\nvar_dump example:\n";
var_dump("PHP is awesome!");
var_dump(12345);

// 'print_r()' prints human-readable information about a variable.
echo "\nprint_r example:\n";
print_r(["PHP", "JavaScript", "Python"]);
echo "\n";


echo "\n=== 2. COMMENTS ===\n";
// --- 3. Comments ---
// This is a single-line shell-style comment
# This is also a single-line comment
/*
  This is a multi-line comment.
  It can span multiple lines.
*/
echo "Comments are ignored by the PHP interpreter. See the source code for details!\n";


echo "\n=== 3. VARIABLES ===\n";
// --- 4. Variables ---
// Variables start with '$', must begin with a letter or underscore, and are case-sensitive.
$greeting = "Hello";
$name = "Clayton";
$age = 30;

// Double quotes parse variables (variable interpolation), single quotes do not.
echo "Double quotes: $greeting, $name! You are $age years old.\n";
echo 'Single quotes: $greeting, $name! You are $age years old.' . "\n"; // Outputs literal $greeting...


echo "\n=== 4. CONSTANTS ===\n";
// --- 5. Constants ---
// Constants are identifiers for simple values. Once set, they cannot be changed.
// Option A: Using 'define()' - evaluated at runtime.
define("SITE_URL", "https://github.com");
echo "SITE_URL constant: " . SITE_URL . "\n";

// Option B: Using 'const' keyword - evaluated at compile time.
const APP_VERSION = "1.0.0";
echo "APP_VERSION constant: " . APP_VERSION . "\n";

// Magic constants change depending on where they are used.
echo "Current File Path: " . __FILE__ . "\n";
echo "Current Line Number: " . __LINE__ . "\n";
?>
