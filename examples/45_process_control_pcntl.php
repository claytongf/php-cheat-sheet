<?php
/**
 * PHP Cheat Sheet - 45: Process Control & Shared Memory (IPC)
 * 
 * Topics covered:
 * - pcntl_fork(): Creating child processes
 * - pcntl_wait(): Reaping child processes to prevent zombies
 * - pcntl_signal(): Catching OS signals (SIGTERM, SIGINT)
 * - shmop: Shared memory blocks (Inter-Process Communication)
 */

echo "=== 1. EXTENSION PRE-FLIGHT CHECK ===\n";
$hasPcntl = extension_loaded('pcntl');
$hasPosix = extension_loaded('posix');
$hasShmop = extension_loaded('shmop');

echo "Extension status:\n";
echo "- pcntl (Process Control): " . ($hasPcntl ? "Loaded" : "Not Loaded") . "\n";
echo "- posix (POSIX Helpers):  " . ($hasPosix ? "Loaded" : "Not Loaded") . "\n";
echo "- shmop (Shared Memory):  " . ($hasShmop ? "Loaded" : "Not Loaded") . "\n\n";

if (!$hasPcntl || !$hasPosix || !$hasShmop) {
    echo "[Notice] Missing required extensions to run full live fork/IPC simulations.\n";
    echo "Displaying cheat sheet code examples and mock demonstrations instead:\n\n";
}


echo "=== 2. MULTIPROCESS FORKING (pcntl_fork) ===\n";
if ($hasPcntl && $hasPosix) {
    echo "Parent Process PID: " . posix_getpid() . "\n";
    echo "Forking a child process...\n";
    
    // Fork the process
    $pid = pcntl_fork();
    
    if ($pid == -1) {
        // Failed to fork
        die("Could not fork process.\n");
    } elseif ($pid) {
        // Parent Process execution block
        echo "[Parent] Spawned Child Process with PID: $pid\n";
        
        // Wait for child process to finish (prevents zombie processes)
        pcntl_wait($status);
        echo "[Parent] Child process $pid exited.\n\n";
    } else {
        // Child Process execution block
        $childPid = posix_getpid();
        echo "[Child] Hello from Child Process! My PID is $childPid\n";
        echo "[Child] Doing work and exiting...\n";
        exit(0); // exit child process successfully
    }
} else {
    echo "Example Code for Process Forking:\n";
    echo "```php\n";
    echo "\$pid = pcntl_fork();\n";
    echo "if (\$pid === -1) {\n";
    echo "    die('Could not fork');\n";
    echo "} elseif (\$pid) {\n";
    echo "    // Parent process block\n";
    echo "    pcntl_wait(\$status); // wait/reap child\n";
    echo "} else {\n";
    echo "    // Child process block\n";
    echo "    exit(0);\n";
    echo "}\n";
    echo "```\n\n";
}


echo "=== 3. SIGNAL TRAPPING (pcntl_signal) ===\n";
if ($hasPcntl) {
    // Custom signal handler
    $signalHandler = function(int $signo) {
        switch ($signo) {
            case SIGTERM:
                echo "Caught SIGTERM! Gracefully shutting down...\n";
                break;
            case SIGINT:
                echo "Caught SIGINT (Ctrl+C)! Exiting...\n";
                break;
        }
    };

    // Register signals
    pcntl_signal(SIGTERM, $signalHandler);
    pcntl_signal(SIGINT, $signalHandler);

    echo "Registered handlers for SIGTERM and SIGINT.\n";
    echo "To dispatch pending signals, call: pcntl_signal_dispatch();\n\n";
} else {
    echo "Example Code for Signal Trapping:\n";
    echo "```php\n";
    echo "pcntl_signal(SIGTERM, function(\$signo) {\n";
    echo "    echo 'Terminating...\\n';\n";
    echo "});\n";
    echo "// Dispatch signals periodically\n";
    echo "pcntl_signal_dispatch();\n";
    echo "```\n\n";
}


echo "=== 4. SHARED MEMORY IPC (shmop) ===\n";
if ($hasShmop) {
    // Unique system key (system identifier for the memory segment)
    $shmKey = 0x1234; 
    
    // Create or open a shared memory segment
    // Flag 'c': create if it doesn't exist, read/write permissions
    // Mode 0644: permissions
    // Size 100: 100 bytes block size
    $shmId = shmop_open($shmKey, "c", 0644, 100);
    
    if ($shmId) {
        $writeString = "Shared memory message!";
        
        // Write string into the shared memory segment (offset 0)
        shmop_write($shmId, $writeString, 0);
        echo "Successfully wrote to shared memory segment: '$writeString'\n";
        
        // Read memory block (offset 0, size 100 bytes)
        $readData = shmop_read($shmId, 0, 100);
        echo "Successfully read from shared memory segment: '" . trim($readData) . "'\n";
        
        // Delete the segment (marks it for deletion when all connections close)
        shmop_delete($shmId);
        
        // Close handle (Deprecated since PHP 8.0 as Shmop objects auto-destroy)
        if (PHP_VERSION_ID < 80000) {
            shmop_close($shmId);
        }
        echo "Shared memory segment deleted & closed.\n";
    } else {
        echo "Could not open shared memory segment.\n";
    }
} else {
    echo "Example Code for Shared Memory Operations:\n";
    echo "```php\n";
    echo "\$shmId = shmop_open(0x1234, 'c', 0644, 100);\n";
    echo "shmop_write(\$shmId, 'Hello Memory', 0);\n";
    echo "\$data = shmop_read(\$shmId, 0, 100);\n";
    echo "shmop_delete(\$shmId);\n";
    echo "shmop_close(\$shmId);\n";
    echo "```\n";
}
