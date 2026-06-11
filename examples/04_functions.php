<?php
/**
 * PHP Cheat Sheet - 04: Functions
 * 
 * Topics covered:
 * - Basic Functions & Parameter Defaults
 * - Strict Types directive
 * - Type Declarations (Return & Parameter Types)
 * - PHP 8.0 features: Union types, Named arguments
 * - Anonymous Functions & Closures (use keyword)
 * - Arrow Functions (PHP 7.4+)
 * - Variadic functions (Splat operator)
 */

// Enable strict type checking (must be the very first statement in the file if used, but we demonstrate here)
// declare(strict_types=1);

echo "=== 1. TYPE DECLARATIONS & DEFAULT VALUES ===\n";

// A function with type hints, default values, and a return type declaration
function calculateTotal(float $price, float $taxRate = 0.1, int $quantity = 1): float {
    return ($price * $quantity) * (1 + $taxRate);
}

// Basic call
$total = calculateTotal(10.0, 0.15, 2);
echo "Total (10.0 * 2 with 15% tax): $total\n";

// Using default parameters
echo "Total with defaults (100.0, default 10% tax, default 1 qty): " . calculateTotal(100.0) . "\n";


echo "\n=== 2. PHP 8.0 FEATURES ===\n";

// 2a. Union Types (Accept multiple types)
function printValue(int|string|float $val): void {
    echo "Value is ($val) of type: " . gettype($val) . "\n";
}

printValue("Hello world");
printValue(45);
printValue(2.718);

// 2b. Named Arguments (Order-independent calls)
echo "Using named arguments:\n";
$namedTotal = calculateTotal(
    quantity: 3, 
    price: 50.0, 
    taxRate: 0.08
);
echo "Named Arguments Total: $namedTotal\n";


echo "\n=== 3. ANONYMOUS FUNCTIONS & CLOSURES ===\n";

$factor = 10;

// Anonymous function using the external variable via 'use'
$multiplier = function (int $number) use ($factor): int {
    return $number * $factor;
};

echo "Anonymous multiplier (5 * 10): " . $multiplier(5) . "\n";


echo "\n=== 4. ARROW FUNCTIONS (PHP 7.4+) ===\n";
// Arrow functions automatically capture variables from the parent scope by-value.
$exponent = 3;
$cube = fn(int $num) => $num ** $exponent;

echo "Arrow function (4^3): " . $cube(4) . "\n";


echo "\n=== 5. VARIADIC FUNCTIONS (...SPLAT OPERATOR) ===\n";

function sumNumbers(int ...$numbers): int {
    $sum = 0;
    foreach ($numbers as $num) {
        $sum += $num;
    }
    return $sum;
}

echo "Sum of 1, 2, 3, 4: " . sumNumbers(1, 2, 3, 4) . "\n";

// Splat operator can also unpack arrays into arguments
$params = [5, 10, 15];
echo "Sum unpacked array [5, 10, 15]: " . sumNumbers(...$params) . "\n";
?>
