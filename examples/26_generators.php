<?php
/**
 * PHP Cheat Sheet - 26: Generators & Memory Efficiency
 * 
 * Topics covered:
 * - What are Generators?
 * - The 'yield' keyword
 * - Memory usage comparison: standard arrays vs. generators
 * - Yielding Key => Value pairs
 * - Sending values back into generators (Co-routines via Generator::send)
 */

echo "=== 1. MEMORY USAGE COMPARISON ===\n";

// A function that builds a large array in memory
function getLargeArray(int $limit): array {
    $array = [];
    for ($i = 1; $i <= $limit; $i++) {
        $array[] = $i;
    }
    return $array;
}

// A generator function that yields values one by one
function getLargeGenerator(int $limit): Generator {
    for ($i = 1; $i <= $limit; $i++) {
        yield $i;
    }
}

$limit = 100000;

// Test A: Memory usage of standard array
$startMemoryA = memory_get_usage();
$arrayData = getLargeArray($limit);
$endMemoryA = memory_get_usage();
$memoryUsedA = $endMemoryA - $startMemoryA;

echo "Standard Array (Limit: $limit):\n";
echo "- Memory Allocated: " . round($memoryUsedA / 1024 / 1024, 2) . " MB\n";

// Free memory
unset($arrayData);
gc_collect_cycles(); // Force garbage collection

// Test B: Memory usage of Generator
$startMemoryB = memory_get_usage();
$generatorData = getLargeGenerator($limit);
$endMemoryB = memory_get_usage();
$memoryUsedB = $endMemoryB - $startMemoryB;

echo "Generator (Limit: $limit):\n";
echo "- Memory Allocated: " . round($memoryUsedB / 1024, 2) . " KB (almost zero!)\n";

// Iterating over the generator (values are generated on-the-fly)
$sum = 0;
foreach ($generatorData as $number) {
    $sum += $number;
}
echo "- Iterator sum output: $sum\n";


echo "\n=== 2. YIELDING KEY => VALUE PAIRS ===\n";

function getFileLines(string $filepath): Generator {
    $handle = fopen($filepath, 'r');
    if (!$handle) return;
    
    $lineNumber = 1;
    while (($line = fgets($handle)) !== false) {
        // Yield the line index as key, and line content as value
        yield $lineNumber => trim($line);
        $lineNumber++;
    }
    fclose($handle);
}

// Create a temp file to read
$tempFile = __DIR__ . "/temp_gen.txt";
file_put_contents($tempFile, "Line Alpha\nLine Beta\nLine Gamma");

foreach (getFileLines($tempFile) as $num => $content) {
    echo "Line $num: $content\n";
}

// Clean up
unlink($tempFile);


echo "\n=== 3. ADVANCED: SENDING VALUES INTO GENERATORS ===\n";
// Generators can also receive data back using the send() method.

function printerGenerator(): Generator {
    echo ">> [Printer] Started.\n";
    while (true) {
        // Yield stops execution and waits for a value to be sent via $generator->send()
        $input = yield;
        if ($input === 'exit') {
            break;
        }
        echo ">> [Printer] Output received: " . strtoupper($input) . "\n";
    }
    echo ">> [Printer] Exited.\n";
}

$printer = printerGenerator();
// Start the generator (run up to the first yield, using current() or send(null))
$printer->current();

// Send values
$printer->send("hello");
$printer->send("modern php");
$printer->send("exit");
?>
