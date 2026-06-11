<?php
/**
 * PHP Cheat Sheet - 35: Native JWT (JSON Web Tokens) Authentication
 * 
 * Topics covered:
 * - JWT Structure: Header, Payload, and Signature
 * - Base64Url encoding and decoding (URL-safe string normalization)
 * - Creating and signing a JWT using HMAC SHA-256 (hash_hmac)
 * - Verifying and parsing a token (handling expiration and signature checks)
 * - Preventing timing attacks during validation using hash_equals()
 */

echo "=== 1. THE STRUCTURE OF A JWT ===\n";
echo "A JWT consists of 3 dot-separated base64url-encoded parts:\n";
echo "1. Header: Specifies the token type (JWT) and signing algorithm (e.g. HS256)\n";
echo "2. Payload: Holds the token claims (e.g. user ID, name, permissions, exp timestamp)\n";
echo "3. Signature: Cryptographic hash verifying that sender identity and data haven't changed\n\n";

// Helper functions for URL-safe base64 encoding (replacing +, / with -, _ and stripping trailing padding =)
function base64UrlEncode(string $data): string {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
}

function base64UrlDecode(string $data): string {
    $remainder = strlen($data) % 4;
    if ($remainder) {
        $data .= str_repeat('=', 4 - $remainder);
    }
    return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
}

// Global Secret Key (keep this highly secure and in environment variables)
$jwtSecretKey = "SuperSecret_App_Key_2026!#$";

echo "=== 2. GENERATING & SIGNING A TOKEN (HS256) ===\n";

function generateJwtToken(array $payload, string $secret): string {
    // 1. Header
    $header = [
        'alg' => 'HS256',
        'typ' => 'JWT'
    ];
    $encodedHeader = base64UrlEncode(json_encode($header));
    
    // 2. Payload
    $encodedPayload = base64UrlEncode(json_encode($payload));
    
    // 3. Signature
    // Signature = HMAC-SHA256(encodedHeader + "." + encodedPayload, secret)
    $signatureInput = $encodedHeader . '.' . $encodedPayload;
    $rawSignature = hash_hmac('sha256', $signatureInput, $secret, true);
    $encodedSignature = base64UrlEncode($rawSignature);
    
    // 4. Combine parts
    return $encodedHeader . '.' . $encodedPayload . '.' . $encodedSignature;
}

// Setup user claims payload
$claims = [
    'sub' => '1234567890',         // Subject (user ID)
    'name' => 'Clayton Souza',    // User name claim
    'role' => 'administrator',     // Custom claim
    'iat' => time(),               // Issued At
    'exp' => time() + 3600         // Expiration Time (1 hour from now)
];

$token = generateJwtToken($claims, $jwtSecretKey);

echo "Payload Claims:\n";
print_r($claims);
echo "\nGenerated JWT:\n$token\n\n";


echo "=== 3. VERIFYING AND DECODING A TOKEN ===\n";

function verifyJwtToken(string $token, string $secret): array {
    // Split token into components
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        throw new Exception("Invalid token format (must contain header, payload, and signature).");
    }
    
    list($encodedHeader, $encodedPayload, $encodedSignature) = $parts;
    
    // 1. Verify Signature
    $signatureInput = $encodedHeader . '.' . $encodedPayload;
    $calculatedRawSig = hash_hmac('sha256', $signatureInput, $secret, true);
    $expectedSignature = base64UrlEncode($calculatedRawSig);
    
    // Prevent timing attacks by using hash_equals() instead of direct string comparison (==)
    if (!hash_equals($expectedSignature, $encodedSignature)) {
        throw new Exception("Signature verification failed. The token is invalid or has been tampered with.");
    }
    
    // 2. Parse Payload
    $payloadJson = base64UrlDecode($encodedPayload);
    $payload = json_decode($payloadJson, true);
    
    if (!$payload) {
        throw new Exception("Failed to decode token payload.");
    }
    
    // 3. Verify Expiration Claim ('exp')
    if (isset($payload['exp']) && time() >= $payload['exp']) {
        throw new Exception("Token has expired (expired at: " . date('Y-m-d H:i:s', $payload['exp']) . ").");
    }
    
    return $payload;
}

// 1. Verify valid token
try {
    $verifiedPayload = verifyJwtToken($token, $jwtSecretKey);
    echo "Verification SUCCESS! Parsed token details:\n";
    print_r($verifiedPayload);
} catch (Exception $e) {
    echo "Verification FAILED: " . $e->getMessage() . "\n";
}
echo "\n";

// 2. Test tampered token (Security Simulation)
echo "Security Test: Tampering with payload content...\n";
$tokenParts = explode('.', $token);
// Decode payload, modify user ID, re-encode
$payloadData = json_decode(base64UrlDecode($tokenParts[1]), true);
$payloadData['sub'] = '9999999999'; // Modifying the subject ID
$tamperedPayload = base64UrlEncode(json_encode($payloadData));
$tamperedToken = $tokenParts[0] . '.' . $tamperedPayload . '.' . $tokenParts[2];

try {
    verifyJwtToken($tamperedToken, $jwtSecretKey);
    echo "Result: Security flaw! Tampered token passed verification.\n";
} catch (Exception $e) {
    echo "Result: Security blocked. Verification failed as expected: " . $e->getMessage() . "\n";
}
?>
