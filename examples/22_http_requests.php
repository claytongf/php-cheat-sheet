<?php
/**
 * PHP Cheat Sheet - 22: HTTP Requests (cURL & Streams)
 * 
 * Topics covered:
 * - Simple HTTP GET requests using file_get_contents()
 * - Customizing HTTP requests via Stream Contexts (headers, methods, body)
 * - Advanced API requests using the cURL extension
 * - Parsing headers and error handling
 */

// Utility function to check connection availability
function isInternetConnectionAvailable(): bool {
    $connected = @fsockopen("www.google.com", 80, $errno, $errstr, 2);
    if ($connected) {
        fclose($connected);
        return true;
    }
    return false;
}

$internetAvailable = isInternetConnectionAvailable();


echo "=== 1. SIMPLE HTTP REQUESTS (file_get_contents) ===\n";

if ($internetAvailable) {
    echo "Fetching a public JSON endpoint via file_get_contents()...\n";
    // Fetching user IP information
    $response = @file_get_contents("https://httpbin.org/ip");
    if ($response !== false) {
        echo "Response data: " . trim($response) . "\n";
    } else {
        echo "Failed to retrieve data.\n";
    }
} else {
    echo "Internet connection offline. Skipping live file_get_contents request.\n";
}


echo "\n=== 2. REQUEST CONTEXTS (file_get_contents with custom headers/POST) ===\n";
echo "You can perform custom methods (POST/PUT) and pass headers via stream contexts:\n\n";

$postData = json_encode(['name' => 'Clayton', 'job' => 'Developer']);

// Setup context array
$contextOptions = [
    'http' => [
        'method' => 'POST',
        'header' => [
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: PHP Stream Agent'
        ],
        'content' => $postData,
        'ignore_errors' => true, // Fetch body even on 4xx/5xx status codes
        'timeout' => 5 // timeout in seconds
    ]
];

// Create context resource
$streamContext = stream_context_create($contextOptions);

if ($internetAvailable) {
    echo "Sending POST request using stream context...\n";
    $postResponse = @file_get_contents("https://httpbin.org/post", false, $streamContext);
    if ($postResponse) {
        $decoded = json_decode($postResponse, true);
        echo "Received server confirmation (IP reflected): " . ($decoded['origin'] ?? 'N/A') . "\n";
    }
} else {
    // Show code sample representation
    echo "Sample usage code:\n";
    echo "\$context = stream_context_create(\$contextOptions);\n";
    echo "\$response = file_get_contents('https://api.example.com/data', false, \$context);\n";
}


echo "\n=== 3. ADVANCED REQUESTS WITH cURL ===\n";

if (!extension_loaded('curl')) {
    echo "cURL extension is not enabled. Documenting cURL template below:\n";
}

$url = "https://httpbin.org/post";

// 1. Initialize cURL handle
$ch = curl_init();

// 2. Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return response string instead of direct echoing
curl_setopt($ch, CURLOPT_POST, true);           // Identify request as POST
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData); // Pass payload string
curl_setopt($ch, CURLOPT_HTTPHEADER, [           // Set custom HTTP request headers
    'Content-Type: application/json',
    'User-Agent: PHP cURL Client'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);            // Timeout limit

if ($internetAvailable && extension_loaded('curl')) {
    echo "Executing live cURL request...\n";
    
    // 3. Execute request
    $result = curl_exec($ch);
    
    // 4. Check error occurrences
    if (curl_errno($ch)) {
        echo "cURL Error: " . curl_error($ch) . "\n";
    } else {
        // Fetch request info details
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        echo "HTTP Response Status Code: $httpCode\n";
        
        $decodedResult = json_decode($result, true);
        echo "Parsed returned JSON user name: " . ($decodedResult['json']['name'] ?? 'N/A') . "\n";
    }
} else {
    echo "Simulation Mode: cURL template is written in source code comments.\n";
}

// 5. Close cURL handle (deprecated in PHP 8.5 as handles destroy automatically)
if (PHP_VERSION_ID < 80500) {
    curl_close($ch);
}
?>
