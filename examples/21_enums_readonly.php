<?php
/**
 * PHP Cheat Sheet - 21: Enums and Readonly Properties (PHP 8.1+)
 * 
 * Topics covered:
 * - Unit Enums & Backed Enums (String/Int values)
 * - Custom methods inside Enums
 * - Readonly Properties (PHP 8.1)
 * - Readonly Classes (PHP 8.2)
 */

echo "=== 1. UNIT ENUMS AND BACKED ENUMS ===\n";

// 1a. Unit Enum (Simple enumeration list without values)
enum Status {
    case Pending;
    case Active;
    case Suspended;
}

$userStatus = Status::Active;
echo "Unit Enum Status Name: " . $userStatus->name . "\n";


// 1b. Backed Enum (Each case is backed by a scalar string or integer value)
enum UserRole: string {
    case Admin = 'administrator';
    case Editor = 'editor';
    case Subscriber = 'subscriber';
    
    // Enums can contain methods!
    public function getLabel(): string {
        return match($this) {
            self::Admin => "System Administrator",
            self::Editor => "Content Editor",
            self::Subscriber => "Regular Reader",
        };
    }
}

$role = UserRole::Admin;
echo "Backed Enum Value: " . $role->value . "\n";
echo "Enum Custom Method Label: " . $role->getLabel() . "\n";

// Instantiating enum from its backed value
$inputRoleValue = 'editor';
$parsedRole = UserRole::from($inputRoleValue); // Throws ValueError if not found
echo "Parsed Enum from value '$inputRoleValue': " . $parsedRole->name . "\n";

// Safe tryFrom (returns null if not found instead of throwing an exception)
$invalidRole = UserRole::tryFrom('guest');
echo "Safe parsing invalid 'guest': " . ($invalidRole === null ? "Not found (Null)" : $invalidRole->name) . "\n";


echo "\n=== 2. READONLY PROPERTIES (PHP 8.1) ===\n";

class BlogPost {
    // Readonly properties can only be initialized once, and cannot be modified afterwards.
    public readonly string $title;
    public readonly string $author;

    public function __construct(string $title, string $author) {
        $this->title = $title;
        $this->author = $author;
    }
}

$post = new BlogPost("Learning PHP Enums", "Clayton");
echo "Readonly Post Title: " . $post->title . "\n";

try {
    // Attempting to change a readonly property throws an Error exception
    // $post->title = "New Title";
    echo "Attempting modification would throw error: Cannot modify readonly property BlogPost::\$title\n";
} catch (Error $e) {
    echo "Caught Error: " . $e->getMessage() . "\n";
}


echo "\n=== 3. READONLY CLASSES (PHP 8.2) ===\n";

// Marking a class readonly makes ALL its properties implicitly readonly and prevents adding dynamic properties.
readonly class UserProfile {
    public function __construct(
        public string $username,
        public string $email
    ) {}
}

$profile = new UserProfile("clayton123", "clayton@example.com");
echo "Readonly Class Username: " . $profile->username . "\n";
echo "Readonly Class Email: " . $profile->email . "\n";
?>
