<?php
/**
 * PHP Cheat Sheet - 13a: Standard Library - String Functions
 * 
 * This file lists and runs essential built-in string functions in PHP.
 */

function printTitle(string $title): void {
    echo "\n--- $title ---\n";
}

printTitle("1. STRING METADATA & SPLITTING");

$text = "PHP Programming";
echo "Input: '$text'\n";

// strlen() - gets string length (number of bytes)
echo "strlen() Output: " . strlen($text) . "\n";

// str_word_count() - counts words in string
echo "str_word_count() Output: " . str_word_count($text) . "\n";

// str_split() - converts string to array of characters (or chunks of specified size)
echo "str_split(size: 3) Output: ";
print_r(str_split($text, 3));

// str_repeat() - repeats a string
echo "str_repeat('-*', 5) Output: " . str_repeat("-*", 5) . "\n";


printTitle("2. CASE SHIFTING");

$mixCase = "i LoVe PhP!";
echo "Input: '$mixCase'\n";

echo "strtoupper() Output: " . strtoupper($mixCase) . "\n";
echo "strtolower() Output: " . strtolower($mixCase) . "\n";
echo "ucfirst() Output: " . ucfirst("hello world") . "\n"; // Upper case first char of string
echo "lcfirst() Output: " . lcfirst("HELLO") . "\n"; // Lower case first char of string
echo "ucwords() Output: " . ucwords("php standard library") . "\n"; // Upper case first char of each word


printTitle("3. TRIMMING & PADDING");

$dirtyText = "   hello   ";
echo "Input: '$dirtyText'\n";
echo "trim() Output: '" . trim($dirtyText) . "'\n";     // Removes from both ends
echo "ltrim() Output: '" . ltrim($dirtyText) . "'\n";   // Left trim
echo "rtrim() Output: '" . rtrim($dirtyText) . "'\n";   // Right trim

$padText = "PHP";
echo "Input: '$padText'\n";
// str_pad() - pads string to new length with another string
echo "str_pad(10, '-') Output: '" . str_pad($padText, 10, "-") . "'\n";
echo "str_pad(10, '*', STR_PAD_BOTH) Output: '" . str_pad($padText, 10, "*", STR_PAD_BOTH) . "'\n";


printTitle("4. SEARCHING & SUBSTRINGS");

$phrase = "The quick brown fox jumps over the lazy dog";
echo "Input: '$phrase'\n";

// strpos() - finds index of first occurrence (case-sensitive, returns false if not found)
echo "strpos('quick') Output: " . strpos($phrase, "quick") . "\n";

// stripos() - case-insensitive find
echo "stripos('THE') Output: " . stripos($phrase, "THE") . "\n";

// strrpos() - finds index of last occurrence
echo "strrpos('the') Output: " . strrpos($phrase, "the") . "\n";

// strstr() / strchr() - returns rest of string from first match
echo "strstr('fox') Output: '" . strstr($phrase, "fox") . "'\n";

// substr() - extracts part of a string (start index, length)
echo "substr(10, 5) Output: '" . substr($phrase, 10, 5) . "'\n";
echo "substr(-3) Output: '" . substr($phrase, -3) . "'\n"; // Negative starts from end


printTitle("5. EDITING & REPLACING");

$original = "Hello world, hello PHP!";
echo "Input: '$original'\n";

// str_replace() - replaces all occurrences of search term (case-sensitive)
echo "str_replace() Output: " . str_replace("hello", "goodbye", $original) . "\n";

// str_ireplace() - case-insensitive replace
echo "str_ireplace() Output: " . str_ireplace("hello", "goodbye", $original) . "\n";

// str_shuffle() - randomly shuffles a string
echo "str_shuffle() Output: " . str_shuffle("PHP") . "\n";

// strrev() - reverses a string
echo "strrev() Output: " . strrev("ReverseMe") . "\n";


printTitle("6. HTML & SPECIAL ESCAPING");

$htmlText = "<b>Clayton & Co.</b>";
echo "Input: '$htmlText'\n";

// strip_tags() - strips HTML and PHP tags from a string
echo "strip_tags() Output: " . strip_tags($htmlText) . "\n";

// nl2br() - inserts HTML line breaks before all newlines in a string
$multiline = "Line 1\nLine 2";
echo "nl2br() Output: " . nl2br($multiline) . "\n";

// wordwrap() - wraps string to a given number of characters
$longText = "This is a very long string that will be wrapped at twenty characters.";
echo "wordwrap() Output:\n" . wordwrap($longText, 20, "\n") . "\n";
?>
