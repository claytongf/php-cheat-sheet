<?php
/**
 * PHP Cheat Sheet - 20: Regular Expressions (PCRE)
 * 
 * Topics covered:
 * - preg_match() (Single pattern matching and capturing)
 * - preg_match_all() (Global matching and capturing)
 * - preg_replace() (Pattern replacing with patterns or backreferences)
 * - preg_split() (Splitting string by patterns)
 */

echo "=== 1. preg_match() (Single Matching) ===\n";

// Validate format (e.g., username should be 4-12 alphanumeric characters)
$usernamePattern = "/^[a-zA-Z0-9_]{4,12}$/";

$testUsers = ["clayton", "cl", "clayton_super_long_username"];

foreach ($testUsers as $user) {
    // Returns 1 if matched, 0 if not matched, false on error
    $isMatch = preg_match($usernamePattern, $user);
    echo "Is '$user' a valid username? " . ($isMatch ? "YES" : "NO") . "\n";
}


echo "\n=== 2. CAPTURING GROUPS ===\n";

// Extract year, month, and day using named capturing groups (?<name>pattern)
$date = "2026-06-10";
$datePattern = "/^(?<year>\d{4})-(?<month>\d{2})-(?<day>\d{2})$/";

if (preg_match($datePattern, $date, $matches)) {
    echo "Full Match: " . $matches[0] . "\n";
    echo "Year: " . $matches['year'] . "\n";
    echo "Month: " . $matches['month'] . "\n";
    echo "Day: " . $matches['day'] . "\n";
}


echo "\n=== 3. preg_match_all() (Global Matching) ===\n";

// Extract all hashtags from a social media post
$post = "Learning #php is fun! #coding is great, especially #OOP and web #apis.";
$hashtagPattern = "/#([a-zA-Z0-9]+)/";

// Matches are loaded into the multidimensional array $allMatches
$count = preg_match_all($hashtagPattern, $post, $allMatches);

echo "Found $count hashtags:\n";
// $allMatches[0] contains full matched substrings e.g. #php
// $allMatches[1] contains values of the first capturing group e.g. php
print_r($allMatches[1]);


echo "\n=== 4. preg_replace() (Replacements) ===\n";

$text = "Contact support at help@example.com or sales@example.org.";
// Redact email addresses
$redacted = preg_replace("/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/", "[REDACTED EMAIL]", $text);
echo "Original: $text\n";
echo "Redacted: $redacted\n";


echo "\n=== 5. preg_split() (Splitting) ===\n";

// Split text by multiple punctuation marks (space, comma, dot, semi-colon)
$sentence = "PHP; HTML, CSS. Javascript";
$words = preg_split("/[\s,.;]+/", $sentence);
echo "Splitting '$sentence':\n";
print_r($words);
?>
