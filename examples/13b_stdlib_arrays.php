<?php
/**
 * PHP Cheat Sheet - 13b: Standard Library - Array Functions
 * 
 * This file lists and runs essential built-in array functions in PHP.
 */

function printTitle(string $title): void {
    echo "\n--- $title ---\n";
}

printTitle("1. VERIFICATION & SIZE");

$fruits = ["apple", "banana", "orange"];
echo "count(\$fruits) Output: " . count($fruits) . "\n";
echo "is_array(\$fruits) Output: " . (is_array($fruits) ? "Yes" : "No") . "\n";
echo "in_array('banana', \$fruits) Output: " . (in_array("banana", $fruits) ? "Yes" : "No") . "\n";

$assoc = ["name" => "Clayton", "role" => "Admin"];
echo "array_key_exists('role', \$assoc) Output: " . (array_key_exists("role", $assoc) ? "Yes" : "No") . "\n";
echo "array_keys(\$assoc) Output: ";
print_r(array_keys($assoc));
echo "array_values(\$assoc) Output: ";
print_r(array_values($assoc));


printTitle("2. STACK & QUEUE OPERATIONS");

$stack = ["A", "B"];
echo "Initial stack: "; print_r($stack);

// array_push() - appends elements to end
array_push($stack, "C", "D");
echo "After array_push('C', 'D'): "; print_r($stack);

// array_pop() - pops element from end
$popped = array_pop($stack);
echo "After array_pop() (Popped '$popped'): "; print_r($stack);

// array_shift() - shifts element from start
$shifted = array_shift($stack);
echo "After array_shift() (Shifted '$shifted'): "; print_r($stack);

// array_unshift() - prepends elements to start
array_unshift($stack, "Z");
echo "After array_unshift('Z'): "; print_r($stack);


printTitle("3. SUBSETS & MANIPULATION");

$colors1 = ["red", "green"];
$colors2 = ["blue", "yellow"];

// array_merge() - merges arrays
print_r(array_merge($colors1, $colors2));

// array_combine() - creates array using one for keys and other for values
print_r(array_combine(["a", "b"], [1, 2]));

// array_slice() - extracts slice of array (offset, length)
$numbers = [10, 20, 30, 40, 50];
echo "array_slice(1, 3) Output: ";
print_r(array_slice($numbers, 1, 3));

// array_splice() - removes & replaces part of array (mutates original)
array_splice($numbers, 1, 2, [99, 99]);
echo "After array_splice(1, 2, [99, 99]) Output: ";
print_r($numbers);

// array_chunk() - splits array into chunks
$chars = ["a", "b", "c", "d", "e"];
echo "array_chunk(2) Output: ";
print_r(array_chunk($chars, 2));

// array_unique() - removes duplicate values
$dupes = [1, 2, 2, 3, 1, 4];
echo "array_unique() Output: ";
print_r(array_unique($dupes));

// array_reverse() - reverses array
echo "array_reverse() Output: ";
print_r(array_reverse($colors2));

// array_flip() - exchanges keys with values
$flipped = array_flip(["x" => 1, "y" => 2]);
echo "array_flip() Output: ";
print_r($flipped);


printTitle("4. SEARCH & ITERATION");

$searchArr = ["first", "second", "third"];
echo "array_search('second') Output: " . array_search("second", $searchArr) . "\n";
echo "array_key_first() Output: " . array_key_first($searchArr) . "\n";
echo "array_key_last() Output: " . array_key_last($searchArr) . "\n";

// array_map() - maps callback over elements
$mapped = array_map(fn($x) => $x * 10, [1, 2, 3]);
echo "array_map() Output: "; print_r($mapped);

// array_filter() - filters elements
$filtered = array_filter([1, 2, 3, 4], fn($x) => $x % 2 === 0);
echo "array_filter() Output: "; print_r($filtered);

// array_reduce() - reduces array to single value
$sum = array_reduce([1, 2, 3, 4], fn($carry, $item) => $carry + $item, 0);
echo "array_reduce() Sum Output: $sum\n";


printTitle("5. SET OPERATIONS");

$setA = [1, 2, 3, 4];
$setB = [3, 4, 5, 6];

// array_diff() - elements in setA that are not in setB
echo "array_diff(A, B) Output: ";
print_r(array_diff($setA, $setB));

// array_intersect() - elements common to both arrays
echo "array_intersect(A, B) Output: ";
print_r(array_intersect($setA, $setB));


printTitle("6. SORTING FUNCTIONS");

$unsorted = [30, 10, 20, 50, 40];
echo "Original unsorted: " . implode(", ", $unsorted) . "\n";

// sort() - sorts values in ascending order (mutates original)
sort($unsorted);
echo "sort() Output: " . implode(", ", $unsorted) . "\n";

// rsort() - reverse sort values
rsort($unsorted);
echo "rsort() Output: " . implode(", ", $unsorted) . "\n";

$assocUnsorted = ["c" => 3, "a" => 1, "b" => 2];

// asort() - sorts associative values ascending, maintaining key association
asort($assocUnsorted);
echo "asort() Output: "; print_r($assocUnsorted);

// arsort() - sorts associative values descending, maintaining key association
arsort($assocUnsorted);
echo "arsort() Output: "; print_r($assocUnsorted);

// ksort() - sorts associative array keys ascending
ksort($assocUnsorted);
echo "ksort() Output: "; print_r($assocUnsorted);

// krsort() - sorts associative array keys descending
krsort($assocUnsorted);
echo "krsort() Output: "; print_r($assocUnsorted);

// usort() - sorts using a user-defined comparison function
$records = [
    ["name" => "Clayton", "age" => 30],
    ["name" => "Alice", "age" => 25],
    ["name" => "Bob", "age" => 35]
];
usort($records, fn($x, $y) => $x["age"] <=> $y["age"]);
echo "usort() by age Output:\n";
print_r($records);
?>
