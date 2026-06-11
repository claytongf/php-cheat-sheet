<?php
/**
 * PHP Cheat Sheet - 05: Arrays
 * 
 * Topics covered:
 * - Indexed Arrays & Associative Arrays
 * - Multidimensional Arrays
 * - Array Destructuring
 * - Common Array Helper Functions
 * - Array Map, Filter, and Reduce
 */

echo "=== 1. ARRAY TYPES ===\n";

// Indexed Array (using short syntax [])
$colors = ["Red", "Green", "Blue"];
$colors[] = "Yellow"; // Add to end
echo "Second color: " . $colors[1] . "\n";
print_r($colors);

// Associative Array (key-value pairs)
$user = [
    "name" => "Clayton",
    "email" => "clayton@example.com",
    "role" => "Developer"
];
echo "User role: " . $user["role"] . "\n";


echo "\n=== 2. ARRAY DESTRUCTURING (PHP 7.1+) ===\n";

// Index array destructuring
[$first, $second] = $colors;
echo "Destructured colors: First='$first', Second='$second'\n";

// Associative array destructuring (PHP 7.1+)
["name" => $userName, "role" => $userRole] = $user;
echo "Destructured user: Name='$userName', Role='$userRole'\n";


echo "\n=== 3. HELPER FUNCTIONS ===\n";

$fruits = ["Apple", "Banana", "Orange"];

// Check if value exists (in_array)
echo "Has Banana? " . (in_array("Banana", $fruits) ? "Yes" : "No") . "\n";

// Check if key exists (array_key_exists)
echo "Has 'email' key in user? " . (array_key_exists("email", $user) ? "Yes" : "No") . "\n";

// Get array keys & values
print_r(array_keys($user));
print_r(array_values($user));

// Merge arrays
$vegetables = ["Carrot", "Potato"];
$food = array_merge($fruits, $vegetables);
echo "Merged food array:\n";
print_r($food);


echo "\n=== 4. ARRAY MAP, FILTER & REDUCE ===\n";

$numbers = [1, 2, 3, 4, 5, 6];
echo "Original numbers: " . implode(", ", $numbers) . "\n";

// 1. Array Map (Transforms elements)
$squared = array_map(fn($n) => $n * $n, $numbers);
echo "Squared numbers (array_map): " . implode(", ", $squared) . "\n";

// 2. Array Filter (Filters elements based on condition)
$evens = array_filter($numbers, fn($n) => $n % 2 === 0);
echo "Even numbers (array_filter): " . implode(", ", $evens) . "\n";

// 3. Array Reduce (Reduces array to a single value)
$sum = array_reduce($numbers, fn($carry, $n) => $carry + $n, 0);
echo "Sum of numbers (array_reduce): $sum\n";
?>
