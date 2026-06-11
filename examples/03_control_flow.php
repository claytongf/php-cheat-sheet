<?php
/**
 * PHP Cheat Sheet - 03: Control Flow
 * 
 * Topics covered:
 * - Conditional statements (if, elseif, else)
 * - Switch-case statements
 * - Match expression (introduced in PHP 8.0)
 * - Loops (for, while, do-while, foreach)
 * - Break and Continue
 */

echo "=== 1. CONDITIONAL STATEMENTS ===\n";

$score = 85;

if ($score >= 90) {
    echo "Grade: A\n";
} elseif ($score >= 80) {
    echo "Grade: B\n";
} elseif ($score >= 70) {
    echo "Grade: C\n";
} else {
    echo "Grade: F\n";
}

// Ternary operator: condition ? true_val : false_val
$passed = $score >= 60 ? "Yes" : "No";
echo "Passed? $passed\n";

// Null coalescing operator (??): returns left-hand operand if it exists and is not null; otherwise right-hand.
$username = $_GET['user'] ?? 'Guest';
echo "Username: $username\n";


echo "\n=== 2. SWITCH VS MATCH (PHP 8.0) ===\n";

$status = 200;

// Traditional Switch (loose comparison ==)
switch ($status) {
    case 200:
    case 201:
        echo "Switch: Request Succeeded\n";
        break;
    case 404:
        echo "Switch: Not Found\n";
        break;
    default:
        echo "Switch: Unknown status\n";
}

// Modern Match Expression (strict comparison ===, returns a value, no implicit fallthrough)
$resultMsg = match ($status) {
    200, 201 => "Match: Request Succeeded",
    400 => "Match: Bad Request",
    404 => "Match: Not Found",
    default => "Match: Unknown status",
};
echo $resultMsg . "\n";


echo "\n=== 3. LOOPS ===\n";

// Standard For Loop
echo "For Loop: ";
for ($i = 1; $i <= 5; $i++) {
    echo "$i ";
}
echo "\n";

// While Loop
echo "While Loop: ";
$count = 1;
while ($count <= 3) {
    echo "$count ";
    $count++;
}
echo "\n";

// Do-While Loop
echo "Do-While Loop: ";
$val = 10;
do {
    echo "$val "; // Runs at least once
    $val++;
} while ($val < 10);
echo "\n";

// Foreach Loop (with key and value)
$capitals = [
    "Brazil" => "Brasília",
    "USA" => "Washington D.C.",
    "France" => "Paris"
];

echo "Foreach Loop (Key => Value):\n";
foreach ($capitals as $country => $capital) {
    echo "- The capital of $country is $capital\n";
}


echo "\n=== 4. LOOP CONTROL (BREAK/CONTINUE) ===\n";

echo "Printing odd numbers between 1 and 10 (skipping even with continue, stopping at 7 with break):\n";
for ($j = 1; $j <= 10; $j++) {
    if ($j % 2 === 0) {
        continue; // Skip even numbers
    }
    if ($j > 7) {
        break; // Stop loop if greater than 7
    }
    echo "$j ";
}
echo "\n";
?>
