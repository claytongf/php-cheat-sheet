<?php
/**
 * PHP Cheat Sheet - 13h: URLs, Query Strings, and URL Encoding
 * 
 * Topics covered:
 * - parse_url(): Parse a URL and return its components
 * - http_build_query(): Generate URL-encoded query string from array/object
 * - parse_str(): Parse query string into variables
 * - urlencode() vs rawurlencode(): Differences between RFC 1738 and RFC 3986 (space encoding)
 * - urldecode() and rawurldecode()
 */

echo "=== 1. PARSING A URL (parse_url) ===\n";
$url = "https://user:pass@example.com:8080/path/to/script.php?query=value&name=Clayton%20Souza#fragment-anchor";

$components = parse_url($url);
echo "Parsed components of: $url\n";
print_r($components);

// Accessing individual parts safely
echo "Scheme: " . ($components['scheme'] ?? 'none') . "\n";
echo "Host: " . ($components['host'] ?? 'none') . "\n";
echo "Path: " . ($components['path'] ?? 'none') . "\n";
echo "Query: " . ($components['query'] ?? 'none') . "\n\n";


echo "=== 2. GENERATING QUERY STRINGS (http_build_query) ===\n";
$queryParams = [
    'search' => 'PHP 8.5 features',
    'categories' => ['web', 'development', 'programming'],
    'page' => 2,
    'null_val' => null, // note: null fields are typically omitted or formatted specifically
    'custom_symbol' => 'c++ & php'
];

// Simple query string
$queryString = http_build_query($queryParams);
echo "Built Query String:\n$queryString\n\n";

// Query string with custom separator and encoding style
// PHP_QUERY_RFC3986 (encodes spaces as %20) vs PHP_QUERY_RFC1738 (encodes spaces as +)
$queryStringRFC3986 = http_build_query($queryParams, arg_separator: '&', encoding_type: PHP_QUERY_RFC3986);
echo "Built Query String (RFC 3986 - spaces as %20):\n$queryStringRFC3986\n\n";


echo "=== 3. PARSING QUERY STRINGS (parse_str) ===\n";
$rawQueryString = "theme=dark&languages[]=php&languages[]=javascript&user[name]=Clayton&user[role]=admin";

// parse_str parses query strings into an array
// WARNING: In PHP 8.0+, calling parse_str without the second argument (which populated global variables) is removed.
// You MUST provide the second argument as a reference array.
parse_str($rawQueryString, $outputArray);
echo "Parsed Query Array:\n";
print_r($outputArray);
echo "\n";


echo "=== 4. URLENCODE VS RAWURLENCODE ===\n";
$specialString = "Clayton Souza & Co. ~ path/file";

// urlencode: Space becomes '+' (RFC 1738), tilde remains '~'
$urlencoded = urlencode($specialString);

// rawurlencode: Space becomes '%20' (RFC 3986), tilde remains '~'
$rawurlencoded = rawurlencode($specialString);

echo "Original string: '$specialString'\n";
echo "urlencode():      $urlencoded\n";
echo "rawurlencode():   $rawurlencoded\n\n";

// Decoding
echo "urldecode() result:    " . urldecode($urlencoded) . "\n";
echo "rawurldecode() result: " . rawurldecode($rawurlencoded) . "\n";
