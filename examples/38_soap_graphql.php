<?php
/**
 * PHP Cheat Sheet - 38: SOAP and GraphQL APIs
 * 
 * Topics covered:
 * - SOAP Client: Initializing, passing headers, invoking methods, and SoapFault
 * - GraphQL Client: Constructing queries/mutations, setting headers, sending POST requests, and parsing responses
 */

echo "=== 1. SOAP WEB SERVICES ===\n";
echo "SOAP (Simple Object Access Protocol) is an XML-based enterprise protocol.\n\n";

if (!class_exists('SoapClient')) {
    echo "The SOAP extension is not installed in this PHP setup.\n";
    echo "To install on Linux: apt-get install php-soap\n";
    echo "[Simulation Mode Enabled] Demonstrating SOAP implementation code:\n\n";
    
    // SOAP simulation mock classes
    class SoapClient {
        public function __construct($wsdl, $options = []) {}
        public function __setSoapHeaders($headers) {}
        public function GetUserProfile($params) {
            return (object)[
                'UserProfileResult' => (object)[
                    'UserId' => $params['UserId'],
                    'Name' => 'Clayton Souza',
                    'Role' => 'Administrator'
                ]
            ];
        }
    }
    class SoapHeader {
        public function __construct($namespace, $name, $data) {}
    }
    class SoapFault extends Exception {}
}

try {
    // 1. Initializing SoapClient (WSDL-mode)
    $wsdlUrl = "http://example.com/api/v1/users.wsdl";
    $options = [
        'trace' => 1,          // Allows debugging XML request/response headers
        'exceptions' => true,  // Throw SoapFault exceptions on error
        'connection_timeout' => 5
    ];
    $soapClient = new SoapClient($wsdlUrl, $options);
    
    // 2. Setting custom SoapHeader for authentication (e.g. Auth header)
    $authHeader = new SoapHeader('http://example.com/auth', 'AuthToken', 'secret-session-token');
    $soapClient->__setSoapHeaders($authHeader);
    
    // 3. Invoking remote function
    echo "Calling SOAP Method: GetUserProfile(123)...\n";
    $response = $soapClient->GetUserProfile(['UserId' => 123]);
    
    echo "SOAP Response received:\n";
    print_r($response->UserProfileResult);
    
} catch (SoapFault $e) {
    echo "SOAP Fault Exception caught:\n";
    echo "- Code: " . $e->faultcode . "\n";
    echo "- Message: " . $e->getMessage() . "\n";
}
echo "\n";


echo "=== 2. GRAPHQL APIS ===\n";
echo "GraphQL is a query language for APIs. In PHP, we query GraphQL endpoints via standard HTTP POST containing JSON payloads:\n\n";

function queryGraphQL(string $endpoint, string $query, array $variables = [], string $token = ''): array {
    // Payload structure must match JSON query format
    $payload = json_encode([
        'query' => $query,
        'variables' => $variables
    ]);
    
    // Setting up HTTP Headers
    $headers = [
        'Content-Type: application/json',
        'User-Agent: PHP GraphQL Client'
    ];
    if ($token) {
        $headers[] = "Authorization: Bearer $token";
    }
    
    // Execute POST via cURL (standard industry pattern)
    if (!extension_loaded('curl')) {
        // Fallback trace simulation
        echo "[Simulation] Sending cURL POST payload to $endpoint...\n";
        return [
            'data' => [
                'user' => [
                    'id' => $variables['id'] ?? 'unknown',
                    'name' => 'Clayton Souza',
                    'email' => 'clayton@example.com'
                ]
            ]
        ];
    }
    
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if (PHP_VERSION_ID < 80500) {
        curl_close($ch);
    }
    
    if ($httpCode !== 200) {
        return ['error' => "HTTP Request failed with status code $httpCode"];
    }
    
    return json_decode($response, true) ?? ['error' => 'Invalid JSON response received'];
}

// Defining the GraphQL query document
$graphQuery = '
query GetUser($id: ID!) {
    user(id: $id) {
        id
        name
        email
    }
}';

$vars = ['id' => '42'];
$gqlEndpoint = "https://api.example.com/graphql";

echo "Executing GraphQL query request...\n";
$response = queryGraphQL($gqlEndpoint, $graphQuery, $vars, 'jwt-secure-token');

echo "GraphQL Response payload:\n";
print_r($response);
?>
