<?php
/**
 * PHP Cheat Sheet - 13j: Advanced JSON & JsonSerializable
 * 
 * Topics covered:
 * - json_encode() flags: JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES, JSON_UNESCAPED_UNICODE
 * - Exception handling: JSON_THROW_ON_ERROR
 * - Custom object serialization using the JsonSerializable interface
 * - Depth limits and nested structures
 * - Safe decoding options
 */

echo "=== 1. JSON_ENCODE FLAGS & PRETTY PRINT ===\n";
$data = [
    "user" => "Clayton Souza",
    "website" => "https://github.com/claytongf",
    "languages" => ["Português", "English", "PHP 🐘"],
    "nested" => [
        "active" => true,
        "score" => 98.5
    ]
];

// Simple JSON encoding (escapes slashes and unicode by default)
$defaultJson = json_encode($data);
echo "Default JSON:\n$defaultJson\n\n";

// Advanced JSON encoding with flags combined
$advancedJson = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
echo "Formatted JSON (Pretty, Unescaped Slashes & Unicode):\n$advancedJson\n\n";


echo "=== 2. ERROR HANDLING WITH JSON_THROW_ON_ERROR ===\n";
// Create a structure with invalid UTF-8 bytes to trigger encoding error
$invalidData = ["bad_string" => "\xB1\x31"];

// Approach A: Traditional error checking using json_last_error()
$result = @json_encode($invalidData);
if ($result === false) {
    echo "Encoding failed (Traditional check). Error: " . json_last_error_msg() . "\n";
}

// Approach B: Modern exception handling (PHP 7.3+) using JSON_THROW_ON_ERROR
try {
    json_encode($invalidData, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    echo "Caught expected JsonException: " . $e->getMessage() . "\n";
}
echo "\n";


echo "=== 3. CUSTOM OBJECT SERIALIZATION (JsonSerializable) ===\n";
// Classes can implement JsonSerializable to control how they are encoded into JSON.
class UserProfile implements JsonSerializable {
    public function __construct(
        private string $name,
        private string $email,
        private string $passwordHash // This should be hidden in JSON responses!
    ) {}

    /**
     * Specify data which should be serialized to JSON
     * 
     * @return mixed data which can be serialized by json_encode()
     */
    public function jsonSerialize(): mixed {
        // Return only the public/safe fields
        return [
            'type' => 'user_profile',
            'name' => $this->name,
            'email' => $this->email,
            'timestamp' => time()
        ];
    }
}

$user = new UserProfile("Clayton Souza", "clayton@example.com", '$2y$10$abcdefghijklmnopqrstuv');
echo "Serialized UserProfile:\n";
echo json_encode($user, JSON_PRETTY_PRINT) . "\n\n";


echo "=== 4. DECODING JSON (json_decode) ===\n";
$jsonString = '{"name":"Clayton","role":"developer","active":true}';

// Decode to stdClass object (default)
$obj = json_decode($jsonString);
echo "Decoded as Object (type): " . get_class($obj) . " -> name: " . $obj->name . "\n";

// Decode to associative array (pass true as second argument)
$arr = json_decode($jsonString, true);
echo "Decoded as Array (type): " . gettype($arr) . " -> name: " . $arr['name'] . "\n";
