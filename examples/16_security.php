<?php
/**
 * PHP Cheat Sheet - 16: Security Best Practices
 * 
 * Topics covered:
 * - Password Hashing (password_hash and password_verify)
 * - XSS Prevention (escaping outputs using htmlspecialchars)
 * - Input Sanitization & Validation (filter_var)
 * - CSRF Prevention concept
 */

echo "=== 1. PASSWORD HASHING ===\n";

// Raw password from a user
$password = "my_super_secure_pass123";

// Hash the password using default bcrypt algorithm (blowfish)
// This handles generating a secure, random salt automatically.
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
echo "Raw Password: $password\n";
echo "Hashed Password: $hashedPassword\n";

// Verifying the password (e.g. on login)
$inputPassword1 = "wrong_password";
$inputPassword2 = "my_super_secure_pass123";

$isCorrect1 = password_verify($inputPassword1, $hashedPassword);
$isCorrect2 = password_verify($inputPassword2, $hashedPassword);

echo "Verification 1 (wrong pass): " . ($isCorrect1 ? "VALID" : "INVALID") . "\n";
echo "Verification 2 (correct pass): " . ($isCorrect2 ? "VALID" : "INVALID") . "\n";


echo "\n=== 2. XSS (CROSS-SITE SCRIPTING) PREVENTION ===\n";

// Untrusted user input (e.g. from $_GET['comment'])
$userInput = "<script>alert('XSS Hack!');</script> I love PHP!";

// BAD: Outputting directly is vulnerable to XSS
echo "Vulnerable output: (Not rendered on CLI, but would execute JS in browser)\n";

// GOOD: Escaping output converts HTML special characters to text entities
$escapedOutput = htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');
echo "Escaped output (Safe): $escapedOutput\n";


echo "\n=== 3. DATA SANITIZATION & VALIDATION ===\n";

// 3a. Validation (Checks if data is in correct format)
$emailToValidate = "clayton@example.com";
$badEmail = "invalid-email@";

$isValid1 = filter_var($emailToValidate, FILTER_VALIDATE_EMAIL);
$isValid2 = filter_var($badEmail, FILTER_VALIDATE_EMAIL);

echo "Is '$emailToValidate' a valid email? " . ($isValid1 ? "Yes" : "No") . "\n";
echo "Is '$badEmail' a valid email? " . ($isValid2 ? "Yes" : "No") . "\n";

// 3b. Sanitization (Removes/filters illegal characters)
$dirtyUrl = "https://example.com/search?query=php & <script>";
$cleanUrl = filter_var($dirtyUrl, FILTER_SANITIZE_URL);
echo "Dirty URL: $dirtyUrl\n";
echo "Sanitized URL: $cleanUrl\n";

// Filtering integers
$intVal = "150abc";
$cleanInt = filter_var($intVal, FILTER_SANITIZE_NUMBER_INT);
echo "Sanitized Int from '$intVal': $cleanInt (Type: " . gettype($cleanInt) . ")\n";


echo "\n=== 4. CSRF (CROSS-SITE REQUEST FORGERY) CONCEPT ===\n";
echo "CSRF is prevented by generating a unique, cryptographically strong token for the session:\n";

// Simulating session token setup
$sessionToken = bin2hex(random_bytes(32));
echo "Generated CSRF Token: $sessionToken\n";
echo "To secure forms, embed this token as a hidden field:\n";
echo '<input type="hidden" name="csrf_token" value="' . $sessionToken . '">' . "\n";
echo "Verify it on post submissions: hash_equals(\$_SESSION['csrf_token'], \$_POST['csrf_token'])\n";
?>
