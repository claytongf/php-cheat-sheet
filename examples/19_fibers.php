<?php
/**
 * PHP Cheat Sheet - 19: Asynchronous PHP with Fibers
 * 
 * Topics covered:
 * - Introduction to Fibers (introduced in PHP 8.1.0)
 * - Cooperative Multi-tasking (concurrency without threads)
 * - Creating, starting, suspending, and resuming Fibers
 * - Exchanging data between main script execution and Fibers
 */

echo "=== ASYNCHRONOUS PHP WITH FIBERS ===\n";

if (PHP_VERSION_ID < 80100) {
    echo "Fibers require PHP version 8.1.0 or higher. Your version is " . PHP_VERSION . ".\n";
    exit;
}

// 1. Defining a Fiber
// A Fiber runs a callback that can pause execution at any point via Fiber::suspend()
$fiber = new Fiber(function (): void {
    echo ">> [Fiber START] Fetching page 1...\n";
    
    // Suspend the fiber and return control to caller.
    // We can also pass data back inside suspend()
    Fiber::suspend("Data Page 1");
    
    echo ">> [Fiber RUNNING] Fetching page 2...\n";
    Fiber::suspend("Data Page 2");
    
    echo ">> [Fiber END] Fetching complete.\n";
});


// 2. Executing the Fiber
echo "Starting Fiber...\n";
// start() executes the Fiber up to the first suspend()
$value1 = $fiber->start();
echo "Main thread received: $value1\n\n";

echo "Doing other work in the main thread (non-blocking)...\n";
echo "Resuming Fiber...\n";
// resume() returns control to the Fiber from the last suspend point
$value2 = $fiber->resume();
echo "Main thread received: $value2\n\n";

echo "Doing more work in the main thread...\n";
echo "Resuming Fiber for final execution...\n";
$fiber->resume();

// 3. Verifying Fiber states
echo "\nIs Fiber terminated? " . ($fiber->isTerminated() ? "Yes" : "No") . "\n";
?>
