<?php
/**
 * PHP Cheat Sheet - 07: Superglobals
 * 
 * Topics covered:
 * - What are Superglobals?
 * - $_SERVER (Server and execution environment information)
 * - $_GET & $_POST (Handling query params and form data)
 * - $_SESSION (Preserving data across requests)
 * - $_COOKIE (Handling client-side persistent cookies)
 * - $_FILES (Handling file uploads metadata)
 */

echo "=== 1. SERVER INFORMATION (\$_SERVER) ===\n";

// $_SERVER contains headers, paths, and script locations.
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Command Line Interface (CLI)') . "\n";
echo "Request Method: " . ($_SERVER['REQUEST_METHOD'] ?? 'CLI') . "\n";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'N/A') . "\n";


echo "\n=== 2. REQUEST DATA (\$_GET and \$_POST) ===\n";

// Let's simulate receiving GET parameters
if (empty($_GET)) {
    echo "No actual \$_GET parameters found. Simulating data...\n";
    // Mocking $_GET for CLI output demonstration
    $_GET = ['category' => 'books', 'sort' => 'price_desc'];
}

echo "Query parameters parsed from URL:\n";
foreach ($_GET as $key => $value) {
    // HTML escaping should be used when outputting user input to HTML pages to prevent XSS:
    // htmlspecialchars($value, ENT_QUOTES, 'UTF-8')
    echo "- Param '$key' = '$value'\n";
}

// Simulating POST parameters
if (empty($_POST)) {
    echo "\nNo actual \$_POST data. Simulating form submission...\n";
    $_POST = ['username' => 'clayton123', 'action' => 'login'];
}

echo "Form fields parsed from POST request:\n";
foreach ($_POST as $key => $value) {
    echo "- Form input '$key' = '$value'\n";
}


echo "\n=== 3. SESSIONS (\$_SESSION) ===\n";

// To use sessions, session_start() must be called at the beginning of the request.
// (We handle session errors in CLI mode since header functions might warn)
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    @session_start();
}

$_SESSION['user_id'] = 99;
$_SESSION['logged_in'] = true;

echo "Session data stored successfully:\n";
print_r($_SESSION);


echo "\n=== 4. COOKIES (\$_COOKIE) ===\n";

// Cookies are set using setcookie() function which sends HTTP headers.
// setcookie('theme', 'dark', time() + 3600, '/'); // Sets a cookie for 1 hour

// Reading cookies
if (empty($_COOKIE)) {
    echo "No cookies present in the current request. (Simulating 'theme' cookie)\n";
    $_COOKIE['theme'] = 'dark';
}
echo "Theme Cookie Value: " . $_COOKIE['theme'] . "\n";


echo "\n=== 5. FILES (\$_FILES) ===\n";
echo "This superglobal contains uploaded file metadata. Structure resembles:\n";
$dummyFilesStructure = [
    'profile_pic' => [
        'name' => 'avatar.png',
        'type' => 'image/png',
        'tmp_name' => '/tmp/phpYg45a',
        'error' => 0,
        'size' => 12540
    ]
];
print_r($dummyFilesStructure);
?>
