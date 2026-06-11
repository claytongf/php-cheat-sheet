<?php
/**
 * PHP Cheat Sheet - 36: Benchmarking & Performance Optimization
 * 
 * Topics covered:
 * - High-precision execution timing (hrtime vs microtime)
 * - Memory footprint monitoring (memory_get_usage, memory_get_peak_usage)
 * - Controlling memory limits (ini_set('memory_limit', ...))
 * - Garbage Collection status and manual invocation (gc_collect_cycles)
 * - Practical micro-benchmark: O(1) isset() array key lookup vs O(N) in_array() value scan
 */

echo "=== 1. HIGH-PRECISION TIMING ===\n";
echo "Use hrtime(true) for high-resolution nanosecond timestamps (unaffected by system clock shifts):\n\n";

$startNano = hrtime(true);
$startMicro = microtime(true);

// Run a small computation loop
$sum = 0;
for ($i = 0; $i < 500000; $i++) {
    $sum += $i;
}

$endNano = hrtime(true);
$endMicro = microtime(true);

$elapsedNano = $endNano - $startNano;
$elapsedMilli = $elapsedNano / 1e6; // 1 Million Nanoseconds = 1 Millisecond
$elapsedMicroSec = ($endMicro - $startMicro) * 1000;

echo "Sum calculation loop (500k iterations):\n";
echo "- hrtime() duration:    " . number_format($elapsedMilli, 4) . " ms ($elapsedNano ns)\n";
echo "- microtime() duration: " . number_format($elapsedMicroSec, 4) . " ms\n\n";


echo "=== 2. MEMORY PROFILING & LIMITS ===\n";
echo "Monitor memory utilization in bytes to optimize performance and prevent Out-Of-Memory exceptions:\n\n";

echo "Initial Memory Usage: " . number_format(memory_get_usage()) . " bytes\n";

// Load a large list into memory
$largeArray = [];
for ($i = 0; $i < 50000; $i++) {
    $largeArray[] = "item_index_$i";
}

echo "Memory after creating 50k strings array: " . number_format(memory_get_usage()) . " bytes\n";
echo "Peak Memory Usage reached by this script: " . number_format(memory_get_peak_usage()) . " bytes\n";

// Read and adjust memory limit dynamically
$currentLimit = ini_get('memory_limit');
echo "Current PHP memory limit: $currentLimit\n";

// Increase limit to 256 Megabytes if needed
ini_set('memory_limit', '256M');
echo "New PHP memory limit: " . ini_get('memory_limit') . "\n\n";


echo "=== 3. GARBAGE COLLECTION (GC) ===\n";
echo "PHP utilizes reference counting and a cycle detector to free memory automatically. You can monitor and force collection:\n\n";

echo "Garbage collection active? " . (gc_enabled() ? "Yes" : "No") . "\n";
print_r(gc_status());

// Create circular references (memory leak pattern in older engines)
class Node {
    public $sibling;
}
$a = new Node();
$b = new Node();
$a->sibling = $b;
$b->sibling = $a;

// Break main references
unset($a, $b, $largeArray);

// Force manual clean up cycle of circular references
$collected = gc_collect_cycles();
echo "GC manually executed: Freed $collected circular object references.\n";
echo "Memory after unset and GC collection: " . number_format(memory_get_usage()) . " bytes\n\n";


echo "=== 4. MICRO-BENCHMARK: O(1) LOOKUP vs O(N) SCAN ===\n";
echo "Comparing 'isset(\$array[\$key])' (hash table lookup) against 'in_array(\$value, \$array)' (linear scan):\n\n";

// Setup benchmark set
$datasetSize = 10000;
$lookupTarget = "target_element";

// Array structured for value search: ['val1', 'val2', ..., 'target_element']
$searchArrayValues = array_map(function($i) { return "element_$i"; }, range(1, $datasetSize));
$searchArrayValues[] = $lookupTarget; // Put at the very end to maximize scan time

// Array structured for key lookup: ['target_element' => true]
$searchArrayKeys = array_fill_keys($searchArrayValues, true);

$iterations = 2000;

// Test A: in_array() scan
$start = hrtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $found = in_array($lookupTarget, $searchArrayValues);
}
$timeInArray = (hrtime(true) - $start) / 1e6;

// Test B: isset() key check
$start = hrtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $found = isset($searchArrayKeys[$lookupTarget]);
}
$timeIsset = (hrtime(true) - $start) / 1e6;

echo "Results over $iterations executions on $datasetSize elements:\n";
echo "- in_array() Scan (Linear search): " . number_format($timeInArray, 4) . " ms\n";
echo "- isset() Key Check (Hash-map lookup): " . number_format($timeIsset, 4) . " ms\n";
if ($timeIsset > 0) {
    echo "Isset key check was " . number_format($timeInArray / $timeIsset, 1) . "x FASTER than linear value search!\n";
}
?>
