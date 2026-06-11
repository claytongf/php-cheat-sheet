<?php
/**
 * PHP Cheat Sheet - 46: System Command Execution & Subprocesses
 * 
 * Topics covered:
 * - command capture: exec(), system(), shell_exec(), passthru()
 * - escapeshellarg() vs escapeshellcmd() (Security & Sanitization)
 * - proc_open(): Interacting with process input/output streams via pipes
 */

echo "=== 1. QUICK SHELL CAPTURE METHODS ===\n";

// A. shell_exec() - returns complete output as a string (equivalent to backticks)
$host = shell_exec("hostname");
echo "shell_exec(): Hostname is " . trim($host) . "\n";

// B. exec() - returns the last line of output; populates array with all lines
$lastLine = exec("echo 'Line 1'\necho 'Line 2'\necho 'Line 3'", $outputLines, $resultCode);
echo "exec() returns last line: '$lastLine'\n";
echo "exec() populates array of lines:\n";
print_r($outputLines);
echo "Result code (0 means success): $resultCode\n\n";

// C. passthru() - outputs the raw binary stream directly (ideal for binary files/images)
echo "passthru() execution output:\n";
passthru("echo 'Direct stream output'");
echo "\n";


echo "=== 2. SHELL SANITIZATION & SECURITY ===\n";
$untrustedInput = "hello; rm -rf /";

// escapeshellarg(): escapes values to be passed as single argument strings
$safeArg = escapeshellarg($untrustedInput);
echo "escapeshellarg() output:  $safeArg\n";
echo "Command generated: echo $safeArg\n\n";

// escapeshellcmd(): escapes characters that could run secondary commands
$safeCmd = escapeshellcmd("echo $untrustedInput");
echo "escapeshellcmd() output:  $safeCmd\n\n";


echo "=== 3. ADVANCED SUBPROCESS PIPES (proc_open) ===\n";
// proc_open allows running commands with direct stdin, stdout, and stderr piping.

// Descriptor specifications
$descriptors = [
    0 => ["pipe", "r"], // stdin
    1 => ["pipe", "w"], // stdout
    2 => ["pipe", "w"]  // stderr
];

// Let's run 'cat' which bounces stdin back to stdout
$process = proc_open("cat", $descriptors, $pipes);

if (is_resource($process)) {
    // Write data to child process's stdin
    $inputData = "Hello from PHP parent pipe!\n";
    fwrite($pipes[0], $inputData);
    fclose($pipes[0]); // close write pipe so child process knows input is finished
    
    // Read stdout from child process
    $stdoutContent = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    
    // Read stderr from child process
    $stderrContent = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    
    // Close process and get return value
    $exitCode = proc_close($process);
    
    echo "Subprocess read back (stdout):\n$stdoutContent";
    echo "Subprocess error (stderr):\n$stderrContent";
    echo "Subprocess exit code: $exitCode\n";
} else {
    echo "Failed to start subprocess.\n";
}
