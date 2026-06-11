<?php
/**
 * PHP Cheat Sheet - 27: CLI Scripting & Interactive Console
 * 
 * Topics covered:
 * - Detecting CLI SAPI (PHP_SAPI / php_sapi_name)
 * - Handling Command Line Arguments ($argv and $argc)
 * - Reading user inputs dynamically from STDIN (fgets, readline)
 * - Writing to STDOUT and STDERR stream resources
 * - Script Exit Codes (exit(0), exit(1))
 * 
 * Note: If run via the dashboard web interface, this script runs
 * in simulation mode to print CLI execution traces to stdout.
 */

// 1. SAPI Detection
$isCli = (php_sapi_name() === 'cli');

echo "=== 1. CLI ENVIRONMENT DETECTION ===\n";
echo "Current SAPI: " . php_sapi_name() . "\n";
echo "Is CLI Environment? " . ($isCli ? "YES" : "NO (Web / Server)") . "\n\n";


// --- CLI Execution Handler ---
if ($isCli) {
    echo "=== 2. CLI ARGUMENTS ===\n";
    echo "Total arguments count (\$argc): $argc\n";
    echo "Arguments array (\$argv):\n";
    foreach ($argv as $index => $arg) {
        echo "- \$argv[$index]: $arg\n";
    }
    
    echo "\n=== 3. INTERACTIVE CONSOLE (STDIN/STDOUT) ===\n";
    echo "Prompting user input (Write something and press ENTER):\n";
    
    // Write directly to STDOUT stream
    fwrite(STDOUT, "Enter your name: ");
    
    // Read line from STDIN stream
    $nameInput = trim(fgets(STDIN));
    
    if (empty($nameInput)) {
        // Write to STDERR stream on validation error
        fwrite(STDERR, "Error: Name cannot be empty.\n");
        exit(1); // Exit with error status code
    }
    
    echo "Hello, $nameInput! Welcome to the PHP CLI.\n";
    exit(0); // Exit with success status code
} 


// --- Dashboard / Web Server Simulation Mode ---
else {
    echo "=== 2. HOW TO RUN IN CLI ===\n";
    echo "To execute a PHP script in CLI, call it from your terminal:\n";
    echo "   php examples/27_cli_scripting.php arg1 arg2 --flag\n\n";

    echo "=== 3. SIMULATED CLI OUTPUT ===\n";
    // Mock argv
    $mockArgc = 4;
    $mockArgv = ["examples/27_cli_scripting.php", "john_doe", "--verbose", "-id=42"];
    
    echo "Arguments Count (\$argc): $mockArgc\n";
    echo "Arguments Array (\$argv):\n";
    foreach ($mockArgv as $index => $arg) {
        echo "- \$argv[$index]: '$arg'\n";
    }
    
    echo "\nSimulating STDIN read operations:\n";
    echo "   [STDOUT] Enter your name: \n";
    echo "   [STDIN Input] => 'Clayton' (Simulated user keystroke)\n";
    echo "   [STDOUT] Hello, Clayton! Welcome to the PHP CLI.\n\n";

    echo "=== 4. EXIT STATUS CODES ===\n";
    echo "CLI scripts should return exit status codes to inform calling processes:\n";
    echo "- exit(0): Execution succeeded (Success).\n";
    echo "- exit(1) (or greater): Execution failed with errors.\n";
}
?>
