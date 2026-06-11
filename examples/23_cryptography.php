<?php
/**
 * PHP Cheat Sheet - 23: Cryptography & Data Encryption
 * 
 * Topics covered:
 * - Secure Random Bytes generation
 * - Hashing (MD5, SHA-256)
 * - HMAC signatures (Hash-based Message Authentication Codes)
 * - Symmetric Encryption & Decryption (AES-256-CBC using OpenSSL)
 */

echo "=== 1. SECURE RANDOM BYTES AND TOKENS ===\n";

// Generating cryptographically secure pseudo-random bytes
$bytes = random_bytes(16); // Input: length in bytes
echo "Random bytes hex value: " . bin2hex($bytes) . "\n";

// Generate a random user session or reset token
$secureToken = bin2hex(random_bytes(32));
echo "Secure 64-character token: $secureToken\n";


echo "\n=== 2. HASHING DATA ===\n";

$data = "secret message content";

// Simple SHA-256 hash (one-way hashing)
$shaHash = hash('sha256', $data);
echo "Data to hash: '$data'\n";
echo "SHA-256 Hash: $shaHash\n";


echo "\n=== 3. HMAC SIGNATURES ===\n";
// HMAC checks data integrity and authenticity using a shared secret key
$apiKeySecret = "my-private-api-key-secret";

$hmacSignature = hash_hmac('sha256', $data, $apiKeySecret);
echo "HMAC-SHA256 Signature: $hmacSignature\n";


echo "\n=== 4. SYMMETRIC ENCRYPTION/DECRYPTION (AES-256-CBC) ===\n";
// Symmetric encryption uses the same key for both encryption and decryption.

$plaintext = "This is a super sensitive message.";
$encryptionKey = "a-very-strong-secret-key-32-bytes!"; // Must be 32 bytes for AES-256
$cipher = "aes-256-cbc";

// 4a. Encrypting data
// AES-256-CBC requires an initialization vector (IV) to ensure identical plaintexts yield different ciphertexts.
// The IV must be random, secure, and unique for every encryption!
$ivLength = openssl_cipher_iv_length($cipher);
$iv = random_bytes($ivLength);

$ciphertext = openssl_encrypt(
    $plaintext, 
    $cipher, 
    $encryptionKey, 
    0, // Options (0, or OPENSSL_RAW_DATA)
    $iv
);

echo "Original Plaintext: $plaintext\n";
echo "Encrypted Ciphertext: $ciphertext\n";

// To decrypt later, we need BOTH the ciphertext AND the IV.
// Commonly, the IV is prepended to the ciphertext (sometimes base64 encoded together).

// 4b. Decrypting data
$decrypted = openssl_decrypt(
    $ciphertext, 
    $cipher, 
    $encryptionKey, 
    0, 
    $iv
);

echo "Decrypted Output: $decrypted\n";
?>
