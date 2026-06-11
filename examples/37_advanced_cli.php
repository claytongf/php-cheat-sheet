<?php
/**
 * PHP Cheat Sheet - 37: Advanced CLI Scripting
 * 
 * Topics covered:
 * - Parsing arguments and flags (getopt vs $argv/$argc)
 * - Styling console output with ANSI escape sequences (colors, weights)
 * - Reading user interactive inputs (readline, STDIN stream)
 * - Rendering in-place updating console elements (dynamic progress bar)
 * - Checking if execution is TTY (interactive) or web-based
 */

// ANSI Color helper functions
define('COLOR_RESET', "\033[0m");
define('COLOR_RED', "\033[31m");
define('COLOR_GREEN', "\033[32m");
define('COLOR_YELLOW', "\033[33m");
define('COLOR_BLUE', "\033[34m");
define('COLOR_BOLD', "\033[1m");
define('COLOR_UNDERLINE', "\033[4m");

function colorText(string $text, string $colorCode): string {
    // Avoid printing ANSI colors if output is not a terminal (e.g. web browser output)
    if (php_sapi_name() !== 'cli') {
        return $text;
    }
    return $colorCode . $text . COLOR_RESET;
}

echo "=== 1. DETECTING RUNTIME ENVIRONMENT ===\n";
$isCli = (php_sapi_name() === 'cli');
echo "Script is executing in: " . ($isCli ? colorText("COMMAND LINE (CLI)", COLOR_GREEN) : colorText("WEB SERVER (HTTP)", COLOR_YELLOW)) . "\n\n";


echo "=== 2. PARSING COMMAND LINE FLAGS (getopt) ===\n";
echo "getopt() parses short flags (e.g. -v) and long options (e.g. --help):\n\n";

if ($isCli) {
    // Define options:
    // 'h' -> -h (flag)
    // 'u:' -> -u <value> (required value)
    // 'role::' -> --role[=value] (optional value)
    $shortOptions = "hu:";
    $longOptions = ["help", "username:", "role::"];
    
    $options = getopt($shortOptions, $longOptions);
    echo "Parsed Options Output:\n";
    print_r($options);
} else {
    echo "Simulating option parsing (as if run with: php script.php -u clayton --role=admin):\n";
    $simulatedOptions = [
        'u' => 'clayton',
        'role' => 'admin'
    ];
    print_r($simulatedOptions);
}
echo "\n";


echo "=== 3. ANSI TERMINAL OUTPUT STYLING ===\n";
echo colorText("This text is RED", COLOR_RED) . "\n";
echo colorText("This text is GREEN & BOLD", COLOR_GREEN . COLOR_BOLD) . "\n";
echo colorText("This text is BLUE & UNDERLINED", COLOR_BLUE . COLOR_UNDERLINE) . "\n\n";


echo "=== 4. READING USER INPUT ===\n";

if ($isCli) {
    echo "Reading from STDIN handle (or readline):\n";
    
    // Check if TTY is interactive
    if (stream_isatty(STDIN)) {
        // Readline is the preferred wrapper for history/editing
        if (function_exists('readline')) {
            $input = readline("Enter your nickname: ");
        } else {
            echo "Enter your nickname: ";
            $input = fgets(STDIN);
        }
        echo "Hello, " . trim($input) . "!\n\n";
    } else {
        echo "[Non-interactive TTY] Skipping user input prompt.\n\n";
    }
} else {
    echo "Simulating user input read:\n";
    echo "Prompt: Enter your nickname: \n";
    echo "User Input: clayton_coder\n";
    echo "Result: Hello, clayton_coder!\n\n";
}


echo "=== 5. IN-PLACE DYNAMIC PROGRESS BAR ===\n";
echo "We use the Carriage Return character (\\r) to return the cursor to the start of the line and overwrite output:\n\n";

function showProgressBar($current, $total, $size = 20) {
    $percentage = round(($current / $total) * 100);
    $filledSize = round(($current / $total) * $size);
    $unfilledSize = $size - $filledSize;
    
    $bar = str_repeat("=", $filledSize);
    if ($filledSize < $size) {
        $bar .= ">";
        $unfilledSize--;
    }
    $bar .= str_repeat(" ", $unfilledSize);
    
    // Output line with carriage return at start \r and NO newline \n at the end
    // Use stdout stream directly to ensure instant flushing
    $output = sprintf("\rProgress: [%s] %d%% (%d/%d)", $bar, $percentage, $current, $total);
    
    if (php_sapi_name() === 'cli') {
        fwrite(STDOUT, $output);
    } else {
        // Web output: print each stage as a line to show progress list
        echo "Processing... $percentage%\n";
    }
}

// Running the progress loop
$totalSteps = 10;
for ($step = 1; $step <= $totalSteps; $step++) {
    showProgressBar($step, $totalSteps);
    
    // Sleep to simulate work
    // Use short sleep in simulation to prevent UI hang
    usleep(50000); // 50ms
}

if ($isCli) {
    echo "\n\n" . colorText("Task completed successfully!", COLOR_GREEN . COLOR_BOLD) . "\n";
} else {
    echo "\nTask completed successfully!\n";
}
?>
