<?php
/**
 * PHP Cheat Sheet - 49: Advanced Data Validation and Sanitization Filters
 * 
 * Topics covered:
 * - filter_var(): Validation filters (Email, URL, IP ranges, Booleans)
 * - Sanitization filters (removing tags, chars, formatting floats)
 * - FILTER_CALLBACK: Custom validator callbacks
 * - filter_var_array() & filter_input_array(): Bulk array parsing and validation
 */

echo "=== 1. VALIDATION FILTERS (filter_var) ===\n";

// A. Email Validation
$email = "clayton.developer@example.com";
$isValidEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
echo "Email '$email' is: " . ($isValidEmail ? "VALID" : "INVALID") . "\n";

// B. URL Validation
$url = "https://github.com/claytongf";
$isValidUrl = filter_var($url, FILTER_VALIDATE_URL);
echo "URL '$url' is: " . ($isValidUrl ? "VALID" : "INVALID") . "\n";

// C. IP & Ranges Validation (IPv4, IPv6, Public vs Private)
$ip1 = "192.168.1.10";
$ip2 = "8.8.8.8";

// FILTER_FLAG_NO_PRIV_RANGE: reject private IPs (e.g. 192.168.x.x, 10.x.x.x)
// FILTER_FLAG_NO_RES_RANGE: reject reserved range IPs
$isPublicIp1 = filter_var($ip1, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);
$isPublicIp2 = filter_var($ip2, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);

echo "IP $ip1 is public: " . ($isPublicIp1 ? "YES" : "NO") . "\n";
echo "IP $ip2 is public: " . ($isPublicIp2 ? "YES" : "NO") . "\n\n";


echo "=== 2. SANITIZATION FILTERS ===\n";

// A. Sanitizing Floats (allows digits, dots, signs)
$dirtyNumber = "$1,234.56 USD";
$cleanFloat = filter_var($dirtyNumber, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND);
echo "Original: '$dirtyNumber' -> Sanitized Float: '$cleanFloat'\n";

// B. Special Characters Encoding
$dirtyHtml = "<h3>Hello & Welcome</h3>";
$cleanHtml = htmlspecialchars($dirtyHtml, ENT_QUOTES | ENT_HTML5, 'UTF-8');
echo "Original: '$dirtyHtml' -> htmlspecialchars: '$cleanHtml'\n\n";


echo "=== 3. CUSTOM CALLBACK FILTERS (FILTER_CALLBACK) ===\n";
// Custom filtering logic wrapped in filter_var
$text = "PHP 8.5 is really awesome!";
$cleanText = filter_var($text, FILTER_CALLBACK, [
    'options' => function($value) {
        // Strip everything except letters, spaces, and numbers
        return preg_replace('/[^a-zA-Z0-9\s\.]/', '', $value);
    }
]);
echo "Callback cleaned string: '$cleanText'\n\n";


echo "=== 4. BULK ARRAY VALIDATION (filter_var_array) ===\n";
// Dataset to validate
$inputData = [
    'username' => 'clayton_gf',
    'age' => '32',
    'email' => 'clayton@invalid-email-address',
    'website' => 'https://example.com'
];

// Define parsing schemas
$validationSchema = [
    'username' => [
        'filter' => FILTER_CALLBACK,
        'options' => function($value) {
            return preg_match('/^[a-z0-9_]{3,15}$/', $value) ? $value : false;
        }
    ],
    'age' => [
        'filter' => FILTER_VALIDATE_INT,
        'options' => ['min_range' => 18, 'max_range' => 120]
    ],
    'email' => FILTER_VALIDATE_EMAIL,
    'website' => FILTER_VALIDATE_URL
];

// Execute bulk parsing
$filteredResult = filter_var_array($inputData, $validationSchema);

echo "Bulk Filter Results:\n";
foreach ($filteredResult as $key => $value) {
    if ($value === false || $value === null) {
        echo " - Field '$key': VALIDATION FAILED\n";
    } else {
        echo " - Field '$key': Validated to '$value'\n";
    }
}
