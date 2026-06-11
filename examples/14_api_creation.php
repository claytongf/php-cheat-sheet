<?php
/**
 * PHP Cheat Sheet - 14: Creating REST APIs in PHP
 * 
 * Topics covered:
 * - Setting Response Headers (JSON & CORS)
 * - Handling HTTP Methods (GET, POST, PUT, DELETE)
 * - Reading Request Bodies (php://input for raw JSON)
 * - Setting HTTP Response Status Codes
 * - Routing API Endpoints
 * - Handling input validation and errors
 * 
 * Note: If run via the dashboard or CLI, this script simulates
 * API requests & responses to demonstrate the functionality in stdout.
 */

// --- API Controller Implementation ---

// Mock database
$usersDb = [
    1 => ["id" => 1, "name" => "Clayton", "email" => "clayton@example.com"],
    2 => ["id" => 2, "name" => "Alice", "email" => "alice@example.com"]
];

// Main function to handle and process the API request
function handleApiRequest(string $method, string $path, ?string $rawInput = null): void {
    // 1. Set response headers for JSON and CORS (Cross-Origin Resource Sharing)
    if (!headers_sent()) {
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    }

    global $usersDb;

    // Simple Router logic
    // Endpoint: /users
    if ($path === "/users" || $path === "/users/") {
        switch ($method) {
            case "GET":
                // Retrieve all users
                http_response_code(200); // OK
                echo json_encode([
                    "success" => true,
                    "data" => array_values($usersDb)
                ]);
                break;
                
            case "POST":
                // Read and decode the raw JSON input body
                // In a real server: $rawInput = file_get_contents("php://input");
                $data = json_decode($rawInput, true);

                // Validation
                if (empty($data["name"]) || empty($data["email"])) {
                    http_response_code(400); // Bad Request
                    echo json_encode([
                        "success" => false,
                        "error" => "Missing required fields: name and email"
                    ]);
                    break;
                }

                // Create new record
                $newId = count($usersDb) + 1;
                $newUser = [
                    "id" => $newId,
                    "name" => $data["name"],
                    "email" => $data["email"]
                ];
                $usersDb[$newId] = $newUser;

                http_response_code(201); // Created
                echo json_encode([
                    "success" => true,
                    "message" => "User created successfully",
                    "data" => $newUser
                ]);
                break;

            default:
                http_response_code(455); // Method Not Allowed (405)
                echo json_encode([
                    "success" => false,
                    "error" => "Method $method not allowed on this endpoint"
                ]);
        }
    } 
    // Endpoint: /users/{id}
    elseif (preg_match('/^\/users\/(\d+)$/', $path, $matches)) {
        $userId = (int)$matches[1];

        if (!isset($usersDb[$userId])) {
            http_response_code(404); // Not Found
            echo json_encode([
                "success" => false,
                "error" => "User with ID $userId not found"
            ]);
            return;
        }

        switch ($method) {
            case "GET":
                http_response_code(200);
                echo json_encode([
                    "success" => true,
                    "data" => $usersDb[$userId]
                ]);
                break;
                
            case "PUT":
                $data = json_decode($rawInput, true);
                
                // Partially update user properties if provided
                if (isset($data["name"])) $usersDb[$userId]["name"] = $data["name"];
                if (isset($data["email"])) $usersDb[$userId]["email"] = $data["email"];

                http_response_code(200);
                echo json_encode([
                    "success" => true,
                    "message" => "User updated successfully",
                    "data" => $usersDb[$userId]
                ]);
                break;

            case "DELETE":
                unset($usersDb[$userId]);
                http_response_code(200);
                echo json_encode([
                    "success" => true,
                    "message" => "User deleted successfully"
                ]);
                break;

            default:
                http_response_code(455);
                echo json_encode([
                    "success" => false,
                    "error" => "Method $method not allowed on this endpoint"
                ]);
        }
    } 
    // 404 Route
    else {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "error" => "API Route not found"
        ]);
    }
}


// --- EXECUTION OR SIMULATION MODE ---

// Detect if request is real HTTP or simulation (CLI/Playground Dashboard run)
$isRealRequest = (php_sapi_name() !== 'cli' && isset($_SERVER['REQUEST_METHOD']));

if ($isRealRequest) {
    // Real API Execution Mode
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Parse path info e.g. /index.php/users -> /users
    $path = $_SERVER['PATH_INFO'] ?? '/';
    
    // Read raw body (for JSON payloads)
    $rawInput = file_get_contents("php://input");
    
    handleApiRequest($method, $path, $rawInput);
} else {
    // Simulation / Demo Mode for the Interactive Dashboard
    echo "=== PHP REST API SIMULATION MODULE ===\n";
    echo "This script demonstrates a raw PHP REST API implementation.\n";
    echo "When requested over HTTP, it serves JSON responses. Running simulation...\n\n";

    echo "--- [1] GET /users (List all users) ---\n";
    handleApiRequest("GET", "/users");
    echo "\n\n";

    echo "--- [2] POST /users (Create a new user with valid JSON) ---\n";
    $postPayload = json_encode(["name" => "Charlie Brown", "email" => "charlie@example.com"]);
    echo "Payload sent: $postPayload\n";
    handleApiRequest("POST", "/users", $postPayload);
    echo "\n\n";

    echo "--- [3] POST /users (Create with missing validation fields) ---\n";
    $badPayload = json_encode(["name" => "No Email"]);
    echo "Payload sent: $badPayload\n";
    handleApiRequest("POST", "/users", $badPayload);
    echo "\n\n";

    echo "--- [4] GET /users/1 (Retrieve single user) ---\n";
    handleApiRequest("GET", "/users/1");
    echo "\n\n";

    echo "--- [5] PUT /users/2 (Update existing user data) ---\n";
    $putPayload = json_encode(["name" => "Alice Cooper"]);
    echo "Payload sent: $putPayload\n";
    handleApiRequest("PUT", "/users/2", $putPayload);
    echo "\n\n";

    echo "--- [6] DELETE /users/1 (Remove user) ---\n";
    handleApiRequest("DELETE", "/users/1");
    echo "\n\n";

    echo "--- [7] GET /users/99 (Request non-existent user) ---\n";
    handleApiRequest("GET", "/users/99");
    echo "\n";
}
?>
