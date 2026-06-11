<?php
/**
 * PHP Cheat Sheet - 08: Error & Exception Handling
 * 
 * Topics covered:
 * - Exceptions vs Errors
 * - Try-Catch-Finally Blocks
 * - Throwing Custom Exceptions
 * - Multiple Catch Blocks
 * - Custom Error Handler Setup
 */

echo "=== 1. TRY-CATCH-FINALLY ===\n";

function divide(int $dividend, int $divisor): float {
    if ($divisor === 0) {
        throw new Exception("Division by zero error.");
    }
    return $dividend / $divisor;
}

try {
    echo "Attempting division...\n";
    $result = divide(10, 0);
    echo "Result: $result\n"; // This line will NOT be reached
} catch (Exception $e) {
    echo "Caught Exception: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " on line " . $e->getLine() . "\n";
} finally {
    echo "Finally block always executes, regardless of whether an exception occurred.\n";
}


echo "\n=== 2. CUSTOM EXCEPTIONS & MULTIPLE CATCHES ===\n";

// Defining custom Exception classes
class InvalidEmailException extends Exception {}
class DomainBlockedException extends Exception {}

function registerUser(string $email): void {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new InvalidEmailException("The email '$email' is invalid.");
    }
    
    if (str_ends_with($email, "@spam.com")) {
        throw new DomainBlockedException("The email domain '@spam.com' is blocked.");
    }
    
    echo "User registered successfully with email: $email\n";
}

// Testing custom exceptions
$testEmails = ["invalid-email", "user@spam.com", "user@example.com"];

foreach ($testEmails as $email) {
    try {
        echo "Registering $email...\n";
        registerUser($email);
    } catch (InvalidEmailException $e) {
        echo ">> Registration Error (Invalid Email): " . $e->getMessage() . "\n";
    } catch (DomainBlockedException $e) {
        echo ">> Registration Error (Blocked Domain): " . $e->getMessage() . "\n";
    } catch (Exception $e) {
        echo ">> Generic Registration Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}


echo "=== 3. CUSTOM ERROR HANDLERS ===\n";

// Set a custom handler for PHP errors (like notices, warnings)
set_error_handler(function(int $errno, string $errstr, string $errfile, int $errline) {
    echo "[Custom Error Handler] Level: $errno | Message: $errstr | File: $errfile | Line: $errline\n";
    return true; // Stop PHP's internal error handler from running
});

// Trigger a warning by accessing an undefined variable
$undefinedVar = $nonExistentVariable; // Notice/Warning will be captured by our custom handler

// Restore default error handler
restore_error_handler();
?>
