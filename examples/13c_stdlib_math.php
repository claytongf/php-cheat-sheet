<?php
/**
 * PHP Cheat Sheet - 13c: Standard Library - Math & Numeric Functions
 * 
 * This file lists and runs essential built-in math and numerical functions in PHP.
 */

function printTitle(string $title): void {
    echo "\n--- $title ---\n";
}

printTitle("1. ROUNDING & ABSOLUTE");

$val = -5.67;
echo "Input: $val\n";
echo "abs() Output: " . abs($val) . "\n";       // Absolute value
echo "ceil() Output: " . ceil($val) . "\n";     // Round fraction up
echo "floor() Output: " . floor($val) . "\n";   // Round fraction down
echo "round() Output: " . round($val) . "\n";   // Round to nearest


printTitle("2. MIN, MAX & POWERS");

echo "min(4, 9, 2, 7) Output: " . min(4, 9, 2, 7) . "\n";
echo "max([4, 9, 2, 7]) Output: " . max([4, 9, 2, 7]) . "\n";
echo "pow(2, 8) Output: " . pow(2, 8) . "\n";   // 2^8
echo "sqrt(144) Output: " . sqrt(144) . "\n";   // Square root


printTitle("3. BASE CONVERSIONS");

$num = 255;
echo "Input decimal: $num\n";
echo "decbin() Output (binary): " . decbin($num) . "\n";
echo "dechex() Output (hex): " . dechex($num) . "\n";
echo "bindec('11111111') Output (decimal): " . bindec('11111111') . "\n";
echo "hexdec('ff') Output (decimal): " . hexdec('ff') . "\n";


printTitle("4. FLOATING POINT & CHECKING");

$f1 = 5.5;
$f2 = 2.0;
echo "fmod(5.5, 2.0) (floating-point remainder) Output: " . fmod($f1, $f2) . "\n";

// is_nan() - checks if value is not a number
$nanVal = acos(1.01); // Invalid range yields NaN
echo "is_nan(acos(1.01)) Output: " . (is_nan($nanVal) ? "Yes (NaN)" : "No") . "\n";

// is_infinite() - checks if value is infinite
$infVal = log(0); // Yields -INF
echo "is_infinite(log(0)) Output: " . (is_infinite($infVal) ? "Yes (Infinite)" : "No") . "\n";


printTitle("5. RANDOMNESS");

// rand() - pseudo-random integer (old, mt_rand is faster)
echo "rand(1, 100) Output: " . rand(1, 100) . "\n";

// mt_rand() - Mersenne Twister pseudo-random integer
echo "mt_rand(1, 100) Output: " . mt_rand(1, 100) . "\n";

// random_int() - cryptographically secure pseudo-random integer
echo "random_int(1000, 9999) Output: " . random_int(1000, 9999) . "\n";

// random_bytes() - cryptographically secure random bytes
$bytes = random_bytes(8);
echo "random_bytes(8) hex Output: " . bin2hex($bytes) . "\n";
?>
