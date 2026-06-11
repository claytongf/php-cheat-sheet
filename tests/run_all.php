<?php
/**
 * PHP Cheat Sheet - Automated Examples Execution Test Runner
 * 
 * This script scans the `examples/` directory and executes every PHP script
 * in a separate subprocess, ensuring that all examples execute successfully
 * (with a return status of 0) and without raising exceptions or compile errors.
 */

$examplesDir = dirname(__DIR__) . '/examples';
if (!is_dir($examplesDir)) {
    echo "Error: Examples directory not found at $examplesDir\n";
    exit(1);
}

// Find all .php files in examples
$files = glob($examplesDir . '/*.php');
sort($files);

$passed = 0;
$failed = 0;
$failuresList = [];

echo "==================================================\n";
echo "    PHP CHEAT SHEET AUTOMATED TEST RUNNER         \n";
echo "==================================================\n";
echo "Executing " . count($files) . " scripts in isolated subprocesses...\n\n";

foreach ($files as $file) {
    $basename = basename($file);
    
    // Start timing
    $startTime = microtime(true);
    
    // Run script in a clean isolated subprocess with stdin redirected to prevent blocking
    $command = sprintf('php %s < /dev/null 2>&1', escapeshellarg($file));
    exec($command, $output, $resultCode);
    
    $elapsed = round((microtime(true) - $startTime) * 1000, 2);
    
    if ($resultCode === 0) {
        echo sprintf("\033[32m[ PASS ]\033[0m %-45s (%6.2f ms)\n", $basename, $elapsed);
        $passed++;
    } else {
        echo sprintf("\033[31m[ FAIL ]\033[0m %-45s (%6.2f ms) - Exit Code: %d\n", $basename, $elapsed, $resultCode);
        $failed++;
        $failuresList[] = [
            'file' => $basename,
            'code' => $resultCode,
            'output' => implode("\n", array_slice($output, -10)) // show last 10 lines of fail output
        ];
    }
    
    // Clear output buffer for the next run
    unset($output);
}

echo "\n==================================================\n";
echo "                    SUMMARY                       \n";
echo "==================================================\n";
echo "Total Scripts Run: " . ($passed + $failed) . "\n";
echo "Passed:            \033[32m" . $passed . "\033[0m\n";
echo "Failed:            " . ($failed > 0 ? "\033[31m" . $failed . "\033[0m" : "0") . "\n";

if ($failed > 0) {
    echo "\nFailures details:\n";
    foreach ($failuresList as $fail) {
        echo "--------------------------------------------------\n";
        echo "File:      {$fail['file']} (Exit Code: {$fail['code']})\n";
        echo "Output snippet:\n";
        echo "...\n" . $fail['output'] . "\n";
    }
    echo "==================================================\n";
    exit(1);
}

echo "==================================================\n";
exit(0);
